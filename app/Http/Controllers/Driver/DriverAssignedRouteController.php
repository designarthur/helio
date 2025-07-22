<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User; // The model used for driver authentication
use App\Models\Equipment; // To get equipment details
use App\Models\Customer; // To get customer details
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class DriverAssignedRouteController extends Controller
{
    /**
     * Display a listing of the assigned bookings/jobs for the authenticated driver.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed. Please log in as a driver.');
        }

        $driverId = $user->id;
        $currentDate = Carbon::now();

        // Fetch bookings assigned to this driver that are not yet completed or cancelled
        $assignedBookings = Booking::where('driver_id', $driverId)
                                   ->where(function($query) use ($currentDate) {
                                        $query->where('rental_end_date', '>=', $currentDate->toDateString()) // Not yet ended
                                              ->orWhere(function($q) { // Or currently 'Delivered' but not 'Completed'
                                                  $q->where('status', 'Delivered');
                                              });
                                   })
                                   ->whereNotIn('status', ['Completed', 'Cancelled'])
                                   ->with(['customer', 'equipment']) // Eager load relationships
                                   ->orderBy('rental_start_date', 'asc')
                                   ->get(); // Fetch all for current driver for the view


        // Basic summary for the top cards (similar to dashboard)
        $totalStops = $assignedBookings->count();
        $estimatedDuration = '4h 30m'; // Conceptual
        $estimatedMileage = '45 miles'; // Conceptual
        $assignedVehicle = $user->assigned_vehicle ?? 'N/A'; // From driver's user profile


        return view('driver.assigned_routes.index', compact(
            'user', // Pass driver user object
            'assignedBookings',
            'totalStops',
            'estimatedDuration',
            'estimatedMileage',
            'assignedVehicle'
        ));
    }

    /**
     * Display the specified booking/stop details for the driver.
     * In a full app, this might show more specific job instructions, maps, etc.
     */
    public function show(Booking $booking)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed.');
        }

        // Ensure the booking is assigned to this driver and belongs to their vendor
        if ($booking->driver_id !== $user->id || $booking->vendor_id !== $user->vendor_id) {
            return redirect()->route('driver.assigned_routes.index')->with('error', 'Unauthorized access to this job.');
        }

        $booking->load(['customer', 'equipment']); // Eager load necessary relationships

        return view('driver.assigned_routes.show', compact('booking'));
    }


    /**
     * Conceptual: Mark a booking/stop as arrived.
     */
    public function markArrived(Request $request, Booking $booking)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver' || $booking->driver_id !== $user->id || $booking->vendor_id !== $user->vendor_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status === 'Delivered') {
            return redirect()->back()->with('info', 'This job is already marked as delivered.');
        }

        $booking->update(['status' => 'Delivered']); // Mark as 'Delivered' upon arrival at site.

        return redirect()->back()->with('success', 'Job ' . $booking->id . ' marked as arrived!');
    }

    /**
     * Conceptual: Complete a booking/job (e.g., after POD).
     */
    public function completeJob(Request $request, Booking $booking)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver' || $booking->driver_id !== $user->id || $booking->vendor_id !== $user->vendor_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status === 'Completed') {
            return redirect()->back()->with('info', 'This job is already completed.');
        }

        // In a real scenario, this would handle POD (Proof of Delivery) details like signature, photos, notes.
        // For now, it just updates status.
        $booking->update(['status' => 'Completed']);

        // Optional: Make equipment 'Available' if it was assigned to this booking
        if ($booking->equipment && $booking->equipment->status === 'On Rent') {
            $booking->equipment->update(['status' => 'Available']);
        }

        return redirect()->back()->with('success', 'Job ' . $booking->id . ' marked as completed!');
    }

    /**
     * Conceptual: Report a problem with a booking/job.
     */
    public function reportProblem(Request $request, Booking $booking)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver' || $booking->driver_id !== $user->id || $booking->vendor_id !== $user->vendor_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // In a real scenario, this would open a form to capture problem details,
        // send notifications to dispatch/vendor, and potentially change booking status.
        $booking->update(['status' => 'Problem Reported']); // New status for problems

        return redirect()->back()->with('info', 'Problem reported for Job ' . $booking->id . '. Dispatch has been notified.');
    }
}