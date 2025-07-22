<?php

namespace App\Http\Controllers;

use App\Models\User;        // The model used for driver authentication
use App\Models\Booking;     // To fetch assigned jobs
use App\Models\Vendor;      // For authentication fallback
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class DriverDashboardController extends Controller
{
    /**
     * Display the driver dashboard.
     */
    public function index()
    {
        // Get the authenticated User instance (who is a driver)
        $user = Auth::guard('driver')->user();

        if (!$user || $user->role !== 'Driver') {
            // This should ideally be caught by middleware, but good to have a fallback
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed. Please log in as a driver.');
        }

        $driverId = $user->id;
        $vendorId = $user->vendor_id; // Get the vendor this driver belongs to

        // --- Fetch Data for Dashboard KPIs ---
        $currentDate = Carbon::now();

        // Assigned Routes / Jobs Today
        $assignedJobsTodayCount = Booking::where('driver_id', $driverId)
                                         ->whereDate('rental_start_date', $currentDate->toDateString()) // Jobs starting today
                                         ->whereIn('status', ['Confirmed', 'Delivered']) // Only confirmed/delivered to driver
                                         ->count();

        // Pending Jobs (Upcoming or In-Progress assigned jobs)
        $pendingJobsCount = Booking::where('driver_id', $driverId)
                                   ->where('rental_end_date', '>=', $currentDate) // End date today or in future
                                   ->whereIn('status', ['Confirmed', 'Delivered']) // Already sent to driver
                                   ->count();
        // A more nuanced 'pending' might filter out 'Completed' or 'Cancelled'

        // HOS Status (Conceptual)
        $hosStatus = [
            'current_status' => 'OFF DUTY', // Default
            'drive_time_remaining' => '8h 00m',
            'shift_time_remaining' => '14h 00m',
            'cycle_time_remaining' => '70h 00m',
            'violations' => 'No Violations',
        ];
        // In a real system, HOS would be calculated from a separate driver log table
        // or integrate with an ELD (Electronic Logging Device) system.

        // Notifications (Conceptual, or fetch from a real notifications system)
        $notificationsCount = 5; // Dummy count
        $unreadMessagesCount = 3; // Dummy count
        $routeUpdatesCount = 1; // Dummy count

        // Upcoming Tasks (simplified to show next 3 jobs assigned to this driver)
        $upcomingTasks = Booking::where('driver_id', $driverId)
                                ->where('rental_end_date', '>=', $currentDate)
                                ->whereIn('status', ['Confirmed', 'Delivered'])
                                ->orderBy('rental_start_date', 'asc')
                                ->limit(3)
                                ->with(['customer', 'equipment'])
                                ->get();

        return view('driver.dashboard', compact(
            'user', // The authenticated user object (driver)
            'assignedJobsTodayCount',
            'pendingJobsCount',
            'hosStatus',
            'notificationsCount',
            'unreadMessagesCount',
            'routeUpdatesCount',
            'upcomingTasks'
        ));
    }
}