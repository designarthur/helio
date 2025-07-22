<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User; // For drivers
use App\Models\Customer; // To get customer names for views
use App\Models\Equipment; // To get equipment names for views
use App\Models\Vendor; // For authentication fallback
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    /**
     * Display the main dispatcher dashboard (Driver Board).
     * This method will load data for all tabs conceptually.
     */
    public function show(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            $vendor = Vendor::first(); // Fallback for dev if not authenticated
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;

        // Fetch all relevant bookings for the vendor, eager load relationships
        $allBookings = Booking::where('vendor_id', $vendorId)
                               ->with(['customer', 'equipment', 'driver'])
                               ->orderBy('rental_start_date', 'asc')
                               ->get();

        // Separate assigned and unassigned jobs for the board view
        $unassignedJobs = $allBookings->filter(function($booking) {
            return is_null($booking->driver_id) || empty($booking->driver_id);
        });

        // Fetch active drivers for the board view
        $activeDrivers = User::where('vendor_id', $vendorId)
                             ->where('role', 'Driver')
                             ->where('status', 'Active')
                             ->get();
        
        // Group assigned jobs by driver for the board view
        $assignedJobsByDriver = $allBookings->filter(function($booking) {
            return !is_null($booking->driver_id) && !empty($booking->driver_id);
        })->groupBy('driver_id');


        // Prepare a lookup for customers and equipment for the list view
        $customerLookup = Customer::where('vendor_id', $vendorId)->get()->keyBy('id');
        $equipmentLookup = Equipment::where('vendor_id', $vendorId)->get()->keyBy('id');
        $driverLookup = User::where('vendor_id', $vendorId)->where('role', 'Driver')->get()->keyBy('id');


        // Determine which tab to display (for active state in view)
        $tab = $request->input('tab', 'board'); // Default to 'board'

        return view('vendor.dispatching.show', compact(
            'vendor',
            'tab',
            'unassignedJobs',
            'activeDrivers',
            'assignedJobsByDriver',
            'allBookings', // For the list view
            'customerLookup', // For list view helper
            'equipmentLookup', // For list view helper
            'driverLookup' // For list view helper
        ));
    }

    /**
     * Handles job assignment/unassignment.
     * This is an API-like endpoint, triggered by a form submission or AJAX.
     */
    public function assignJob(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'booking_id' => 'required|exists:bookings,id,vendor_id,' . $vendor->id,
            'driver_id' => 'nullable|exists:users,id,vendor_id,' . $vendor->id . ',role,Driver', // Must be a driver for this vendor
            'action' => ['required', 'string', Rule::in(['assign', 'unassign'])],
        ]);

        $booking = Booking::find($validatedData['booking_id']);

        if (!$booking || $booking->vendor_id !== $vendor->id) {
            return redirect()->back()->with('error', 'Booking not found or unauthorized.');
        }

        if ($validatedData['action'] === 'assign') {
            if (empty($validatedData['driver_id'])) {
                return redirect()->back()->with('error', 'Driver must be selected for assignment.');
            }
            $booking->driver_id = $validatedData['driver_id'];
            $booking->status = 'Confirmed'; // Assuming assignment confirms booking
            $message = 'Booking ' . $booking->id . ' assigned to driver ' . ($booking->driver->name ?? 'N/A') . ' successfully!';
        } elseif ($validatedData['action'] === 'unassign') {
            $booking->driver_id = null;
            $booking->status = 'Pending'; // Revert to pending
            $message = 'Booking ' . $booking->id . ' unassigned successfully!';
        }
        
        $booking->save();

        return redirect()->route('dispatching.show', ['tab' => 'board'])->with('success', $message);
    }

    /**
     * Simulates route optimization.
     */
    public function simulateRouteOptimization(Request $request)
    {
        // This method remains conceptual, as full route optimization requires
        // complex algorithms and external API integrations (e.g., Google Maps API).
        
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $unassignedCount = Booking::where('vendor_id', $vendor->id)
                                  ->whereNull('driver_id')
                                  ->orWhere('driver_id', '') // Handle empty string for driver_id
                                  ->count();
        $activeDriversCount = User::where('vendor_id', $vendor->id)
                                  ->where('role', 'Driver')
                                  ->where('status', 'Active')
                                  ->count();

        $simulationMessage = "Simulating Route Optimization Process (Google Maps API Integration):\n\n";
        $simulationMessage .= "Step 1: Gathering {$unassignedCount} unassigned jobs, {$activeDriversCount} active drivers, time windows, and vehicle capacities.\n";
        $simulationMessage .= "Step 2: Using Google Maps Platform APIs (Geocoding, Distance Matrix) to calculate precise distances and travel times between all points, considering real-time traffic.\n";
        $simulationMessage .= "Step 3: Solving the Vehicle Routing Problem (VRP) algorithm to determine the most efficient routes that minimize travel time, respect time windows, and balance workload.\n";
        $simulationMessage .= "Step 4: Optimized routes are generated and displayed on the Map View for dispatcher review. They can then be dispatched to drivers' mobile apps.\n\n";
        $simulationMessage .= "(Note: This is a simulation. Actual optimization would happen in a backend service connected to Google Maps APIs.)";

        // This would typically return a JSON response for an AJAX call, or redirect with a message
        return redirect()->route('dispatching.show', ['tab' => 'board'])
                         ->with('info', str_replace("\n", "<br>", $simulationMessage)); // Convert newlines for HTML display
    }
}