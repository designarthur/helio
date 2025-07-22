<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer; // To identify the customer profile from the authenticated user
use App\Models\Equipment; // For new booking request form
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class CustomerBookingController extends Controller
{
    /**
     * Display a listing of the customer's bookings with filters.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed. Please log in as a customer.');
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Customer profile not found. Please contact support.');
        }

        $customerId = $customerProfile->id;
        $currentDate = Carbon::now();

        $query = Booking::where('customer_id', $customerId)
                        ->with('equipment'); // Eager load equipment details

        // Apply filters based on request (e.g., 'active', 'upcoming', 'past')
        $filter = $request->input('filter', 'active'); // Default to 'active'

        switch ($filter) {
            case 'active':
                $query->where('rental_end_date', '>=', $currentDate)
                      ->whereIn('status', ['Confirmed', 'Delivered']);
                break;
            case 'upcoming':
                $query->where('rental_start_date', '>', $currentDate)
                      ->whereIn('status', ['Pending', 'Confirmed']);
                break;
            case 'past':
                $query->where('rental_end_date', '<', $currentDate)
                      ->where('status', 'Completed'); // Only completed past rentals
                break;
            default: // all
                // No additional date/status filters for 'all'
                break;
        }

        $bookings = $query->orderBy('rental_start_date', 'desc')->paginate(10);

        // Fetch counts for badges
        $activeCount = Booking::where('customer_id', $customerId)
                               ->where('rental_end_date', '>=', $currentDate)
                               ->whereIn('status', ['Confirmed', 'Delivered'])
                               ->count();
        $upcomingCount = Booking::where('customer_id', $customerId)
                                 ->where('rental_start_date', '>', $currentDate)
                                 ->whereIn('status', ['Pending', 'Confirmed'])
                                 ->count();
        $pastCount = Booking::where('customer_id', $customerId)
                             ->where('rental_end_date', '<', $currentDate)
                             ->where('status', 'Completed')
                             ->count();


        return view('customer.bookings.index', compact(
            'bookings',
            'filter',
            'activeCount',
            'upcomingCount',
            'pastCount',
            'customerProfile' // For sidebar if needed
        ));
    }

    /**
     * Show the form for customers to request a new booking/rental.
     */
    public function create()
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed. Please log in as a customer.');
        }
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile) {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Customer profile not found. Please contact support.');
        }

        $vendorId = $user->vendor_id;
        // Fetch available equipment for this vendor
        $availableEquipment = Equipment::where('vendor_id', $vendorId)
                                       ->where('status', 'Available')
                                       ->get();

        return view('customer.bookings.create', compact('availableEquipment', 'customerProfile'));
    }

    /**
     * Store a new booking request from the customer.
     * Note: This typically creates a 'Pending' booking, which the vendor then confirms.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('customer')->user();
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile) {
            return redirect()->back()->with('error', 'Customer profile not found. Please contact support.');
        }

        $validatedData = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id,vendor_id,' . $user->vendor_id], // Ensure equipment belongs to the customer's vendor
            'rental_start_date' => ['required', 'date', 'after_or_equal:today'],
            'rental_end_date' => ['required', 'date', 'after_or_equal:rental_start_date'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'pickup_address' => ['nullable', 'string', 'max:255'],
            'booking_notes' => ['nullable', 'string'],
            // Type-specific fields (must match potential inputs from create.blade.php)
            'estimated_tonnage' => ['nullable', 'numeric', 'min:0'],
            'prohibited_materials_ack' => ['nullable', 'string'],
            'requested_service_freq' => ['nullable', 'string', Rule::in(['Weekly', 'Bi-weekly', 'Event-specific'])],
            'toilet_special_requests' => ['nullable', 'string'],
            'container_placement_notes' => ['nullable', 'string'],
            'container_security_access' => ['nullable', 'string'],
        ]);

        // Calculate a preliminary price (vendor will finalize)
        $selectedEquipment = Equipment::find($validatedData['equipment_id']);
        $preliminaryPrice = 0;
        if ($selectedEquipment) {
            $start = Carbon::parse($validatedData['rental_start_date']);
            $end = Carbon::parse($validatedData['rental_end_date']);
            $days = $start->diffInDays($end) + 1;
            $preliminaryPrice = $selectedEquipment->base_daily_rate * $days;
            $preliminaryPrice += ($selectedEquipment->delivery_fee ?? 0) + ($selectedEquipment->pickup_fee ?? 0);
            // Add other fees or make this more robust if needed for customer-facing requests.
        }


        Booking::create([
            'vendor_id' => $user->vendor_id,
            'customer_id' => $customerProfile->id,
            'equipment_id' => $validatedData['equipment_id'],
            'rental_start_date' => $validatedData['rental_start_date'],
            'rental_end_date' => $validatedData['rental_end_date'],
            'delivery_address' => $validatedData['delivery_address'],
            'pickup_address' => $validatedData['pickup_address'] ?? $validatedData['delivery_address'],
            'status' => 'Pending', // New requests start as Pending
            'total_price' => round($preliminaryPrice, 2), // Preliminary price
            'booking_notes' => $validatedData['booking_notes'],
            'driver_id' => null, // Not assigned by customer
            // Populate type-specific fields
            'estimated_tonnage' => $validatedData['estimated_tonnage'] ?? null,
            'prohibited_materials_ack' => $validatedData['prohibited_materials_ack'] ?? null,
            'requested_service_freq' => $validatedData['requested_service_freq'] ?? null,
            'toilet_special_requests' => $validatedData['toilet_special_requests'] ?? null,
            'container_placement_notes' => $validatedData['container_placement_notes'] ?? null,
            'container_security_access' => $validatedData['container_security_access'] ?? null,
        ]);

        return redirect()->route('customer.bookings.index')->with('success', 'Your rental request has been submitted successfully! Awaiting vendor confirmation.');
    }

    /**
     * Display the specified booking for the customer.
     */
    public function show(Booking $booking)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed.');
        }

        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile || $booking->customer_id !== $customerProfile->id) {
            return redirect()->route('customer.bookings.index')->with('error', 'Unauthorized access to booking.');
        }
        
        $booking->load(['customer', 'equipment', 'driver']); // Eager load relationships

        return view('customer.bookings.show', compact('booking'));
    }

    // Customer-initiated actions (conceptual for now, will involve new forms/controllers later)
    // public function requestService(Booking $booking) { ... }
    // public function cancel(Booking $booking) { ... }
    // public function extend(Booking $booking) { ... }
}