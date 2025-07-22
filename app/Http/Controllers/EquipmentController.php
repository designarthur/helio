<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Vendor; // Assuming Vendor model exists for relationships
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // For authenticating vendors

class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipment.
     */
    public function index(Request $request)
    {
        // In a real application, you would get the authenticated vendor's ID
        // For now, let's assume a dummy vendor ID or get it from Auth if setup
        // $vendorId = Auth::guard('vendor')->id(); // If using vendor-specific authentication guard
        // For demonstration, let's assume vendor with ID 1 exists or use the first vendor
        $vendor = Auth::guard('vendor')->user(); // Get the authenticated vendor
        if (!$vendor) {
            // Handle unauthenticated state, maybe redirect to login or show an error
            // For now, let's fetch a dummy vendor for display if not authenticated
            $vendor = Vendor::first(); // Fallback to the first vendor in DB for development
            if (!$vendor) {
                 return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;


        $query = Equipment::where('vendor_id', $vendorId);

        // Apply filters if present in the request (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('internal_id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('type', 'like', '%' . $searchTerm . '%')
                  ->orWhere('size', 'like', '%' . $searchTerm . '%')
                  ->orWhere('location', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        if ($request->has('status_filter') && $request->input('status_filter') !== null) {
            $query->where('status', $request->input('status_filter'));
        }
        if ($request->has('type_filter') && $request->input('type_filter') !== null) {
            $query->where('type', $request->input('type_filter'));
        }

        $equipment = $query->orderBy('updated_at', 'desc')->paginate(10); // Paginate results

        return view('vendor.equipment.index', compact('equipment', 'vendor'));
    }

    /**
     * Show the form for creating a new equipment.
     */
    public function create()
    {
        // Ensure only authenticated vendors can create
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to add equipment.');
        }

        return view('vendor.equipment.create-edit');
    }

    /**
     * Store a newly created equipment in storage.
     */
    public function store(Request $request)
    {
        // Get the authenticated vendor's ID
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        // Validate common fields
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'base_daily_rate' => 'required|numeric|min:0',
            // General optional fields
            'internal_id' => 'nullable|string|max:255|unique:equipment,internal_id,NULL,id,vendor_id,' . $vendorId, // Unique per vendor
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'supplier_manufacturer' => 'nullable|string|max:255',
            // Pricing optional fields
            'default_rental_period' => 'nullable|integer|min:1',
            'min_rental_period' => 'nullable|integer|min:1',
            'extended_daily_rate' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'pickup_fee' => 'nullable|numeric|min:0',
            'damage_waiver_cost' => 'nullable|numeric|min:0',
        ]);

        // Validate type-specific fields
        switch ($validatedData['type']) {
            case 'Dumpster':
                $validatedData = array_merge($validatedData, $request->validate([
                    'dumpster_dimensions' => 'nullable|string|max:255',
                    'max_tonnage' => 'nullable|numeric|min:0',
                    'overage_per_ton_fee' => 'nullable|numeric|min:0',
                    'disposal_rate_per_ton' => 'nullable|numeric|min:0',
                    'dumpster_container_type' => 'nullable|string|max:255',
                    'gate_type' => 'nullable|string|max:255',
                    'prohibited_materials' => 'nullable|string', // Will be cast to array by model
                ]));
                // Convert comma-separated string to array for storing if it's text area
                if (isset($validatedData['prohibited_materials'])) {
                    $validatedData['prohibited_materials'] = array_map('trim', explode(',', $validatedData['prohibited_materials']));
                }
                break;
            case 'Temporary Toilet':
                $validatedData = array_merge($validatedData, $request->validate([
                    'toilet_capacity' => 'nullable|string|max:255',
                    'service_frequency' => 'nullable|string|max:255',
                    'toilet_features' => 'nullable|string', // Will be cast to array by model
                ]));
                 if (isset($validatedData['toilet_features'])) {
                    $validatedData['toilet_features'] = array_map('trim', explode(',', $validatedData['toilet_features']));
                }
                break;
            case 'Storage Container':
                $validatedData = array_merge($validatedData, $request->validate([
                    'storage_container_type' => 'nullable|string|max:255',
                    'door_type' => 'nullable|string|max:255',
                    'condition' => 'nullable|string|max:255',
                    'security_features' => 'nullable|string', // Will be cast to array by model
                ]));
                if (isset($validatedData['security_features'])) {
                    $validatedData['security_features'] = array_map('trim', explode(',', $validatedData['security_features']));
                }
                break;
        }

        // Create equipment with vendor_id
        $equipment = new Equipment($validatedData);
        $equipment->vendor_id = $vendorId;
        $equipment->save();

        return redirect()->route('equipment.index')->with('success', 'Equipment created successfully!');
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipment $equipment)
    {
        // Ensure the authenticated vendor owns this equipment
        if (!Auth::guard('vendor')->check() || $equipment->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('equipment.index')->with('error', 'Unauthorized access to equipment.');
        }

        return view('vendor.equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified equipment.
     */
    public function edit(Equipment $equipment)
    {
        // Ensure the authenticated vendor owns this equipment
        if (!Auth::guard('vendor')->check() || $equipment->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('equipment.index')->with('error', 'Unauthorized access to equipment.');
        }

        return view('vendor.equipment.create-edit', compact('equipment'));
    }

    /**
     * Update the specified equipment in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        // Ensure the authenticated vendor owns this equipment
        if (!Auth::guard('vendor')->check() || $equipment->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Validate common fields
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'base_daily_rate' => 'required|numeric|min:0',
            // General optional fields
            'internal_id' => 'nullable|string|max:255|unique:equipment,internal_id,' . $equipment->id . ',id,vendor_id,' . Auth::guard('vendor')->id(), // Unique per vendor, ignoring current equipment
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'supplier_manufacturer' => 'nullable|string|max:255',
            // Pricing optional fields
            'default_rental_period' => 'nullable|integer|min:1',
            'min_rental_period' => 'nullable|integer|min:1',
            'extended_daily_rate' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'pickup_fee' => 'nullable|numeric|min:0',
            'damage_waiver_cost' => 'nullable|numeric|min:0',
        ]);

        // Validate type-specific fields
        switch ($validatedData['type']) {
            case 'Dumpster':
                $validatedData = array_merge($validatedData, $request->validate([
                    'dumpster_dimensions' => 'nullable|string|max:255',
                    'max_tonnage' => 'nullable|numeric|min:0',
                    'overage_per_ton_fee' => 'nullable|numeric|min:0',
                    'disposal_rate_per_ton' => 'nullable|numeric|min:0',
                    'dumpster_container_type' => 'nullable|string|max:255',
                    'gate_type' => 'nullable|string|max:255',
                    'prohibited_materials' => 'nullable|string',
                ]));
                 if (isset($validatedData['prohibited_materials'])) {
                    $validatedData['prohibited_materials'] = array_map('trim', explode(',', $validatedData['prohibited_materials']));
                }
                break;
            case 'Temporary Toilet':
                $validatedData = array_merge($validatedData, $request->validate([
                    'toilet_capacity' => 'nullable|string|max:255',
                    'service_frequency' => 'nullable|string|max:255',
                    'toilet_features' => 'nullable|string',
                ]));
                if (isset($validatedData['toilet_features'])) {
                    $validatedData['toilet_features'] = array_map('trim', explode(',', $validatedData['toilet_features']));
                }
                break;
            case 'Storage Container':
                $validatedData = array_merge($validatedData, $request->validate([
                    'storage_container_type' => 'nullable|string|max:255',
                    'door_type' => 'nullable|string|max:255',
                    'condition' => 'nullable|string|max:255',
                    'security_features' => 'nullable|string',
                ]));
                if (isset($validatedData['security_features'])) {
                    $validatedData['security_features'] = array_map('trim', explode(',', $validatedData['security_features']));
                }
                break;
        }
        // Set fields to null if they become empty strings due to optional inputs in form
        foreach ($validatedData as $key => $value) {
            if ($value === '' && !in_array($key, ['type', 'size', 'status', 'location'])) { // Exclude required fields
                $validatedData[$key] = null;
            }
        }

        $equipment->update($validatedData);

        return redirect()->route('equipment.index')->with('success', 'Equipment updated successfully!');
    }

    /**
     * Remove the specified equipment from storage.
     */
    public function destroy(Equipment $equipment)
    {
        // Ensure the authenticated vendor owns this equipment
        if (!Auth::guard('vendor')->check() || $equipment->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $equipment->delete();

        return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully!');
    }
}