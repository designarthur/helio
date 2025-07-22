<?php

namespace App\Http\Controllers;

use App\Models\User; // We use the User model for drivers
use App\Models\Vendor; // For vendor relationship and fallback
use App\Models\Booking; // To show assigned bookings
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // For password hashing
use Illuminate\Validation\Rule; // For validation rules

class DriverController extends Controller
{
    /**
     * Display a listing of the drivers.
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

        // Query users with 'driver' role belonging to this vendor
        $query = User::where('vendor_id', $vendorId)
                     ->where('role', 'Driver'); // Filter by driver role

        // Apply filters if present in the request (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('internal_driver_id', 'like', '%' . $searchTerm . '%') // If you added this field
                  ->orWhere('assigned_vehicle', 'like', '%' . $searchTerm . '%');
            });
        }
        if ($request->has('status_filter') && $request->input('status_filter') !== null) {
            $query->where('status', $request->input('status_filter'));
        }

        $drivers = $query->orderBy('updated_at', 'desc')->paginate(10); // Paginate results

        return view('vendor.driver.index', compact('drivers', 'vendor'));
    }

    /**
     * Show the form for creating a new driver.
     */
    public function create()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to add drivers.');
        }

        return view('vendor.driver.create-edit');
    }

    /**
     * Store a newly created driver in storage.
     */
    public function store(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,NULL,id,vendor_id,' . $vendorId], // Email unique per vendor
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' checks for password_confirmation
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:255',
            'license_expiry' => 'nullable|date',
            'cdl_class' => 'nullable|string|max:255',
            'assigned_vehicle' => 'nullable|string|max:255',
            'driver_notes' => 'nullable|string',
            'status' => ['required', 'string', Rule::in(['Active', 'On Leave', 'Inactive'])],
            'certifications' => 'nullable|string', // Will be cast to array by model
        ]);

        // Convert comma-separated string to array for 'certifications'
        if (isset($validatedData['certifications'])) {
            $validatedData['certifications'] = array_map('trim', explode(',', $validatedData['certifications']));
            $validatedData['certifications'] = array_filter($validatedData['certifications']); // Remove empty strings
        } else {
            $validatedData['certifications'] = [];
        }

        // Hash password before saving
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['vendor_id'] = $vendorId;
        $validatedData['role'] = 'Driver'; // Explicitly set role to 'Driver'

        User::create($validatedData);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully!');
    }

    /**
     * Display the specified driver.
     */
    public function show(User $driver) // Using 'User' model and 'driver' parameter name
    {
        // Ensure the authenticated vendor owns this driver and the user is a driver
        if (!Auth::guard('vendor')->check() || $driver->vendor_id !== Auth::guard('vendor')->id() || $driver->role !== 'Driver') {
            return redirect()->route('drivers.index')->with('error', 'Unauthorized access to driver.');
        }

        // Fetch assigned bookings for this driver (e.g., upcoming bookings)
        $assignedBookings = Booking::where('driver_id', $driver->id)
                                   ->where('rental_end_date', '>=', now()->toDateString()) // Future or ongoing bookings
                                   ->with(['customer', 'equipment'])
                                   ->get();

        return view('vendor.driver.show', compact('driver', 'assignedBookings'));
    }

    /**
     * Show the form for editing the specified driver.
     */
    public function edit(User $driver) // Using 'User' model and 'driver' parameter name
    {
        // Ensure the authenticated vendor owns this driver and the user is a driver
        if (!Auth::guard('vendor')->check() || $driver->vendor_id !== Auth::guard('vendor')->id() || $driver->role !== 'Driver') {
            return redirect()->route('drivers.index')->with('error', 'Unauthorized access to driver.');
        }

        return view('vendor.driver.create-edit', compact('driver'));
    }

    /**
     * Update the specified driver in storage.
     */
    public function update(Request $request, User $driver) // Using 'User' model and 'driver' parameter name
    {
        // Ensure the authenticated vendor owns this driver and the user is a driver
        if (!Auth::guard('vendor')->check() || $driver->vendor_id !== Auth::guard('vendor')->id() || $driver->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($driver->id, 'id')->where(function ($query) use ($driver) {
                return $query->where('vendor_id', $driver->vendor_id);
            })], // Email unique per vendor, ignoring current driver
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:255',
            'license_expiry' => 'nullable|date',
            'cdl_class' => 'nullable|string|max:255',
            'assigned_vehicle' => 'nullable|string|max:255',
            'driver_notes' => 'nullable|string',
            'status' => ['required', 'string', Rule::in(['Active', 'On Leave', 'Inactive'])],
            'certifications' => 'nullable|string', // Will be cast to array by model
        ]);

        // Handle password update only if provided
        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:8', 'confirmed']]);
            $validatedData['password'] = Hash::make($request->password);
        } else {
            // Remove password from validatedData if not updated, so it doesn't try to hash an empty string
            unset($validatedData['password']);
        }

        // Convert comma-separated string to array for 'certifications'
        if (isset($validatedData['certifications'])) {
            $validatedData['certifications'] = array_map('trim', explode(',', $validatedData['certifications']));
            $validatedData['certifications'] = array_filter($validatedData['certifications']);
        } else {
            $validatedData['certifications'] = []; // Ensure it's an empty array if null
        }

        // Set nullable fields to null if they become empty strings from the form
        foreach ([
            'phone', 'license_number', 'license_expiry', 'cdl_class',
            'assigned_vehicle', 'driver_notes', 'certifications'
        ] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }
        
        $driver->update($validatedData);

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully!');
    }

    /**
     * Remove the specified driver from storage.
     */
    public function destroy(User $driver) // Using 'User' model and 'driver' parameter name
    {
        // Ensure the authenticated vendor owns this driver and the user is a driver
        if (!Auth::guard('vendor')->check() || $driver->vendor_id !== Auth::guard('vendor')->id() || $driver->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Before deleting, unassign this driver from any bookings
        Booking::where('driver_id', $driver->id)->update(['driver_id' => null]);

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully!');
    }
}