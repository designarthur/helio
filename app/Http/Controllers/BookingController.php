<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer; // To fetch customers for dropdowns
use App\Models\Equipment; // To fetch equipment for dropdowns
use App\Models\Vendor;    // Assuming Vendor model for authentication fallback
use App\Models\User;      // Assuming User model for drivers/staff
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // For authenticating vendors
use Illuminate\Validation\Rule; // For conditional validation rules

class BookingController extends Controller
{
    /**
     * Display a listing of the bookings.
     */
    public function index(Request $request)
    {
        // Get the authenticated vendor's ID
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            $vendor = Vendor::first(); // Fallback for dev if not authenticated
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;

        $query = Booking::where('vendor_id', $vendorId)
                        ->with(['customer', 'equipment']); // Eager load relationships

        // Apply filters if present in the request (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', '%' . $searchTerm . '%') // Search by Booking ID
                  ->orWhereHas('customer', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%'); // Search by Customer Name
                  })
                  ->orWhereHas('equipment', function ($q) use ($searchTerm) {
                      $q->where('type', 'like', '%' . $searchTerm . '%') // Search by Equipment Type
                        ->orWhere('size', 'like', '%' . $searchTerm . '%'); // Search by Equipment Size
                  })
                  ->orWhere('booking_notes', 'like', '%' . $searchTerm . '%'); // Search by Booking Notes
            });
        }
        if ($request->has('status_filter') && $request->input('status_filter') !== null) {
            $query->where('status', $request->input('status_filter'));
        }
        if ($request->has('date_filter') && $request->input('date_filter') !== null) {
            $filterDate = $request->input('date_filter');
            $query->where(function($q) use ($filterDate) {
                $q->where('rental_start_date', '<=', $filterDate)
                  ->where('rental_end_date', '>=', $filterDate);
            });
        }

        $bookings = $query->orderBy('rental_start_date', 'desc')->paginate(10);

        return view('vendor.booking.index', compact('bookings', 'vendor'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to create bookings.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        // Only show 'Available' equipment for new bookings, or equipment that is currently 'On Rent' by this vendor (for potential re-booking or extended use cases)
        $equipment = Equipment::where('vendor_id', $vendorId)
                              ->whereIn('status', ['Available', 'On Rent'])
                              ->get();
        $drivers = User::where('role', 'Driver')->get(); // Assuming 'users' table stores drivers

        return view('vendor.booking.create-edit', compact('customers', 'equipment', 'drivers'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        // Validate common fields
        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId], // Customer must exist and belong to this vendor
            'equipment_id' => ['required', 'exists:equipment,id,vendor_id,' . $vendorId], // Equipment must exist and belong to this vendor
            'rental_start_date' => ['required', 'date', 'after_or_equal:today'],
            'rental_end_date' => ['required', 'date', 'after_or_equal:rental_start_date'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'pickup_address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['Pending', 'Confirmed', 'Delivered', 'Completed', 'Cancelled'])],
            'total_price' => ['required', 'numeric', 'min:0'],
            'booking_notes' => ['nullable', 'string'],
            'driver_id' => ['nullable', 'exists:users,id'], // Drivers are users
        ]);

        // Validate type-specific fields based on selected equipment's type
        $selectedEquipment = Equipment::where('id', $validatedData['equipment_id'])
                                      ->where('vendor_id', $vendorId)
                                      ->first();

        if ($selectedEquipment) {
            switch ($selectedEquipment->type) {
                case 'Dumpster':
                    $validatedData = array_merge($validatedData, $request->validate([
                        'estimated_tonnage' => ['nullable', 'numeric', 'min:0'],
                        'prohibited_materials_ack' => ['nullable', 'string'],
                    ]));
                    break;
                case 'Temporary Toilet':
                    $validatedData = array_merge($validatedData, $request->validate([
                        'requested_service_freq' => ['nullable', 'string', Rule::in(['Weekly', 'Bi-weekly', 'Event-specific'])],
                        'toilet_special_requests' => ['nullable', 'string'],
                    ]));
                    break;
                case 'Storage Container':
                    $validatedData = array_merge($validatedData, $request->validate([
                        'container_placement_notes' => ['nullable', 'string'],
                        'container_security_access' => ['nullable', 'string'],
                    ]));
                    break;
            }
        }

        // Create booking with vendor_id
        $booking = new Booking($validatedData);
        $booking->vendor_id = $vendorId;
        $booking->save();

        // Optional: Update equipment status if the booking is 'Confirmed' or 'Delivered'
        // For example, set status to 'On Rent'
        if (in_array($booking->status, ['Confirmed', 'Delivered'])) {
            $selectedEquipment->update(['status' => 'On Rent']);
        }


        return redirect()->route('bookings.index')->with('success', 'Booking created successfully!');
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        // Ensure the authenticated vendor owns this booking
        if (!Auth::guard('vendor')->check() || $booking->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('bookings.index')->with('error', 'Unauthorized access to booking.');
        }

        // Eager load customer, equipment, and driver relationships for display
        $booking->load(['customer', 'equipment', 'driver']);

        return view('vendor.booking.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        // Ensure the authenticated vendor owns this booking
        if (!Auth::guard('vendor')->check() || $booking->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('bookings.index')->with('error', 'Unauthorized access to booking.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        // For editing, include the currently booked equipment even if its status is not 'Available'
        $equipment = Equipment::where('vendor_id', $vendorId)
                              ->where(function ($query) use ($booking) {
                                  $query->whereIn('status', ['Available', 'On Rent'])
                                        ->orWhere('id', $booking->equipment_id); // Include current equipment
                              })
                              ->get();
        $drivers = User::where('role', 'Driver')->get();

        return view('vendor.booking.create-edit', compact('booking', 'customers', 'equipment', 'drivers'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        // Ensure the authenticated vendor owns this booking
        if (!Auth::guard('vendor')->check() || $booking->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Validate common fields
        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $booking->vendor_id],
            'equipment_id' => ['required', 'exists:equipment,id,vendor_id,' . $booking->vendor_id],
            'rental_start_date' => ['required', 'date', 'after_or_equal:today'], // Can adjust this rule for editing past dates if needed
            'rental_end_date' => ['required', 'date', 'after_or_equal:rental_start_date'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'pickup_address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['Pending', 'Confirmed', 'Delivered', 'Completed', 'Cancelled'])],
            'total_price' => ['required', 'numeric', 'min:0'],
            'booking_notes' => ['nullable', 'string'],
            'driver_id' => ['nullable', 'exists:users,id'],
        ]);

        // Validate type-specific fields based on selected equipment's type (could change during edit)
        $selectedEquipment = Equipment::where('id', $validatedData['equipment_id'])
                                      ->where('vendor_id', $booking->vendor_id)
                                      ->first();

        if ($selectedEquipment) {
            switch ($selectedEquipment->type) {
                case 'Dumpster':
                    $validatedData = array_merge($validatedData, $request->validate([
                        'estimated_tonnage' => ['nullable', 'numeric', 'min:0'],
                        'prohibited_materials_ack' => ['nullable', 'string'],
                    ]));
                    break;
                case 'Temporary Toilet':
                    $validatedData = array_merge($validatedData, $request->validate([
                        'requested_service_freq' => ['nullable', 'string', Rule::in(['Weekly', 'Bi-weekly', 'Event-specific'])],
                        'toilet_special_requests' => ['nullable', 'string'],
                    ]));
                    break;
                case 'Storage Container':
                    $validatedData = array_merge($validatedData, $request->validate([
                        'container_placement_notes' => ['nullable', 'string'],
                        'container_security_access' => ['nullable', 'string'],
                    ]));
                    break;
            }
        }
        // Set fields to null if they become empty strings due to optional inputs in form
        foreach ($validatedData as $key => $value) {
            if ($value === '' && !in_array($key, ['rental_start_date', 'rental_end_date', 'delivery_address', 'status', 'total_price'])) {
                $validatedData[$key] = null;
            }
        }

        // Before updating, store original equipment ID if it changed
        $originalEquipmentId = $booking->equipment_id;

        $booking->update($validatedData);

        // Update equipment status if necessary (e.g., if booking status changed or equipment changed)
        // If booking is Completed/Cancelled, original equipment might become Available
        if ($booking->status === 'Completed' || $booking->status === 'Cancelled') {
            Equipment::where('id', $originalEquipmentId)->update(['status' => 'Available']);
        } elseif (in_array($booking->status, ['Confirmed', 'Delivered'])) {
             Equipment::where('id', $booking->equipment_id)->update(['status' => 'On Rent']);
        }
        // If equipment was changed on the booking, the old one might need status reset
        if ($originalEquipmentId !== $booking->equipment_id) {
             Equipment::where('id', $originalEquipmentId)->update(['status' => 'Available']); // Assume old equipment is freed up
        }


        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully!');
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        // Ensure the authenticated vendor owns this booking
        if (!Auth::guard('vendor')->check() || $booking->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Optional: Update equipment status to 'Available' if the booking is deleted
        if ($booking->equipment) {
            $booking->equipment->update(['status' => 'Available']);
        }

        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully!');
    }

    /**
     * Helper to calculate price (for JS) - not a full endpoint
     */
    public function calculatePrice(Request $request) {
        // This method is conceptual and would likely be a more complex JS-driven client-side calculation
        // or a simple API endpoint for specific price lookups.
        // For full functionality, you'd need to fetch equipment rates etc.
        $equipmentId = $request->input('equipment_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $estimatedTonnage = $request->input('estimated_tonnage');
        $serviceFreq = $request->input('service_frequency');

        if (!$equipmentId || !$startDate || !$endDate) {
            return response()->json(['totalPrice' => 0, 'error' => 'Missing required parameters'], 400);
        }

        $equipment = Equipment::find($equipmentId);

        if (!$equipment) {
            return response()->json(['totalPrice' => 0, 'error' => 'Equipment not found'], 404);
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1; // Include both start and end day

        $calculatedPrice = $equipment->base_daily_rate * $days;

        // Add flat fees (delivery, pickup, damage waiver)
        $calculatedPrice += $equipment->delivery_fee ?? 0;
        $calculatedPrice += $equipment->pickup_fee ?? 0;
        $calculatedPrice += $equipment->damage_waiver_cost ?? 0;

        // Add type-specific pricing
        if ($equipment->type === 'Dumpster') {
            // For demo, if estimated tonnage exceeds maxTonnage, add overage
            if ($estimatedTonnage && $equipment->max_tonnage && $estimatedTonnage > $equipment->max_tonnage) {
                $calculatedPrice += ($estimatedTonnage - $equipment->max_tonnage) * ($equipment->overage_per_ton_fee ?? 0);
            }
            // Add disposal rate for all estimated tonnage
            $calculatedPrice += ($estimatedTonnage ?? 0) * ($equipment->disposal_rate_per_ton ?? 0);
        } elseif ($equipment->type === 'Temporary Toilet') {
            $numServices = 0;
            if ($serviceFreq === 'Weekly') {
                $numServices = floor($days / 7);
            } elseif ($serviceFreq === 'Bi-weekly') {
                $numServices = floor($days / 14);
            } elseif ($serviceFreq === 'Event-specific' && $days < 7) { // Assume 1 service for short events
                $numServices = 1;
            }
            // Dummy cost per service for toilet servicing, assuming $20 per service
            $calculatedPrice += $numServices * 20;
        }
        // No specific additional pricing logic provided for Storage Container in original JS

        return response()->json(['totalPrice' => round($calculatedPrice, 2)]);
    }
}