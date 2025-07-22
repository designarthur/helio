<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User; // The model used for driver authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class DriverScheduleController extends Controller
{
    /**
     * Display the driver's schedule/calendar view.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed. Please log in as a driver.');
        }

        $driverId = $user->id;
        $vendorId = $user->vendor_id;

        // Get date range for calendar (default to current month)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        // Parse dates if they come as strings
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Fetch assigned bookings for the date range
        $assignedBookings = Booking::where('driver_id', $driverId)
                                   ->where('vendor_id', $vendorId)
                                   ->whereBetween('rental_start_date', [$startDate, $endDate])
                                   ->with(['customer', 'equipment'])
                                   ->orderBy('rental_start_date', 'asc')
                                   ->get();

        // Format bookings for calendar display
        $calendarEvents = $assignedBookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => ($booking->equipment->type ?? 'Equipment') . ' - ' . ($booking->customer->name ?? 'Customer'),
                'start' => $booking->rental_start_date->format('Y-m-d'),
                'end' => $booking->rental_end_date->addDay()->format('Y-m-d'), // FullCalendar expects end date to be exclusive
                'color' => $this->getEventColor($booking->status),
                'extendedProps' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                    'customer' => $booking->customer->name ?? 'Unknown',
                    'equipment' => ($booking->equipment->type ?? 'Equipment') . ' (' . ($booking->equipment->size ?? 'N/A') . ')',
                    'delivery_address' => $booking->delivery_address,
                    'pickup_address' => $booking->pickup_address,
                ],
            ];
        });

        // Get summary statistics
        $upcomingJobs = $assignedBookings->where('rental_start_date', '>=', Carbon::today())->count();
        $completedJobs = $assignedBookings->where('status', 'Completed')->count();
        $pendingJobs = $assignedBookings->whereIn('status', ['Confirmed', 'Delivered'])->count();

        return view('driver.schedule.index', compact(
            'user',
            'calendarEvents',
            'upcomingJobs',
            'completedJobs',
            'pendingJobs',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display calendar view (separate from index if needed).
     */
    public function calendar(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Submit a time-off request.
     */
    public function submitTimeOffRequest(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $validatedData = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', Rule::in(['Vacation', 'Sick Leave', 'Personal', 'Family Emergency', 'Other'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // In a real application, you would store this in a time_off_requests table
        // For now, we'll just log it or store it conceptually
        
        // Check for conflicts with existing bookings
        $conflictingBookings = Booking::where('driver_id', $user->id)
                                     ->where(function ($query) use ($validatedData) {
                                         $query->whereBetween('rental_start_date', [$validatedData['start_date'], $validatedData['end_date']])
                                               ->orWhereBetween('rental_end_date', [$validatedData['start_date'], $validatedData['end_date']])
                                               ->orWhere(function ($q) use ($validatedData) {
                                                   $q->where('rental_start_date', '<=', $validatedData['start_date'])
                                                     ->where('rental_end_date', '>=', $validatedData['end_date']);
                                               });
                                     })
                                     ->whereIn('status', ['Confirmed', 'Delivered'])
                                     ->count();

        if ($conflictingBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => "You have {$conflictingBookings} scheduled job(s) during this period. Please contact dispatch to reassign these bookings before submitting your time-off request."
            ]);
        }

        // In a real system, create time-off request record
        // TimeOffRequest::create([
        //     'driver_id' => $user->id,
        //     'vendor_id' => $user->vendor_id,
        //     'start_date' => $validatedData['start_date'],
        //     'end_date' => $validatedData['end_date'],
        //     'reason' => $validatedData['reason'],
        //     'notes' => $validatedData['notes'],
        //     'status' => 'Pending',
        // ]);

        // Log for now (in production, you'd send to dispatch/management)
        \Log::info('Time-off request submitted', [
            'driver_id' => $user->id,
            'driver_name' => $user->name,
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'reason' => $validatedData['reason'],
            'notes' => $validatedData['notes'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Time-off request submitted successfully! Your supervisor will review and respond soon.'
        ]);
    }

    /**
     * Get schedule data for AJAX/API calls (JSON format for calendar widgets).
     */
    public function getScheduleData(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $startDate = $request->input('start', Carbon::now()->startOfMonth());
        $endDate = $request->input('end', Carbon::now()->endOfMonth());

        $assignedBookings = Booking::where('driver_id', $user->id)
                                   ->whereBetween('rental_start_date', [$startDate, $endDate])
                                   ->with(['customer', 'equipment'])
                                   ->get();

        $events = $assignedBookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => ($booking->equipment->type ?? 'Equipment') . ' - ' . ($booking->customer->name ?? 'Customer'),
                'start' => $booking->rental_start_date->format('Y-m-d'),
                'end' => $booking->rental_end_date->addDay()->format('Y-m-d'),
                'color' => $this->getEventColor($booking->status),
                'url' => route('driver.assigned_routes.show', $booking->id),
                'extendedProps' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                    'customer' => $booking->customer->name ?? 'Unknown',
                    'equipment' => ($booking->equipment->type ?? 'Equipment') . ' (' . ($booking->equipment->size ?? 'N/A') . ')',
                    'delivery_address' => $booking->delivery_address,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Get available time slots for a specific date (for scheduling new jobs).
     */
    public function getAvailableTimeSlots(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $date = $request->input('date');
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $dateCarbon = Carbon::parse($date);

        // Get existing bookings for the date
        $existingBookings = Booking::where('driver_id', $user->id)
                                   ->where('rental_start_date', '<=', $dateCarbon)
                                   ->where('rental_end_date', '>=', $dateCarbon)
                                   ->whereIn('status', ['Confirmed', 'Delivered'])
                                   ->count();

        // Simple availability check (in reality, this would be more complex)
        $isAvailable = $existingBookings === 0;

        return response()->json([
            'date' => $date,
            'is_available' => $isAvailable,
            'existing_bookings' => $existingBookings,
            'message' => $isAvailable ? 'Available' : "Already have {$existingBookings} job(s) scheduled"
        ]);
    }

    /**
     * Helper method to get event colors based on booking status.
     */
    private function getEventColor($status)
    {
        switch ($status) {
            case 'Confirmed':
                return '#007bff'; // Blue
            case 'Delivered':
                return '#ffc107'; // Yellow
            case 'Completed':
                return '#28a745'; // Green
            case 'Cancelled':
                return '#dc3545'; // Red
            case 'Pending':
                return '#6c757d'; // Gray
            default:
                return '#17a2b8'; // Teal
        }
    }

    /**
     * Export schedule to various formats (conceptual).
     */
    public function exportSchedule(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return redirect()->back()->with('error', 'Authentication failed.');
        }

        $format = $request->input('format', 'pdf'); // pdf, excel, ical
        
        // In a real implementation, you would generate the file based on format
        // For now, this is conceptual
        return redirect()->back()->with('info', 'Schedule export feature is not yet implemented.');
    }
}
