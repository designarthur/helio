<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\VehicleInspection;
use App\Models\User; // The model used for driver authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // For conceptual file uploads
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class VehicleInspectionController extends Controller
{
    /**
     * Display the vehicle inspection form and a list of past inspections for the driver.
     */
    public function index(Request $request)
    {
        $user = Auth->guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth->guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed. Please log in as a driver.');
        }

        $driverId = $user->id;
        $vendorId = $user->vendor_id;

        // Fetch past inspections for this driver
        $pastInspections = VehicleInspection::where('driver_id', $driverId)
                                             ->orderByDesc('inspection_datetime')
                                             ->paginate(10); // Paginate past inspections


        // Dummy vehicle ID for the form (could be fetched from driver's profile or a separate vehicles table)
        $vehicleId = $user->assigned_vehicle ?? 'Truck #DVIR-001';


        return view('driver.vehicle_inspection.index', compact(
            'user',
            'pastInspections',
            'vehicleId'
        ));
    }

    /**
     * Store a newly created vehicle inspection record.
     */
    public function store(Request $request)
    {
        $user = Auth->guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $driverId = $user->id;
        $vendorId = $user->vendor_id;

        // Validation for checklist items (expected to be 'ok' or 'defect')
        $checklistItems = [
            'lights', 'tires', 'brakes', 'steering', 'wipers', 'mirrors', 'fluids'
        ];

        $validationRules = [
            'inspection_type' => ['required', 'string', Rule::in(['pre-trip', 'post-trip'])],
            'vehicle_id' => ['required', 'string', 'max:255'],
            'odometer_reading' => ['required', 'integer', 'min:0'],
            'inspection_datetime' => ['required', 'date_format:Y-m-d\TH:i'], // Matches datetime-local input format
            'defect_notes' => ['nullable', 'string'],
            'driver_certified_safe' => ['required', 'boolean'],
            'driver_signature_image' => ['nullable', 'string'], // Base64 encoded image or URL
            'defect_photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif|max:2048'], // For each photo if multiple
        ];

        // Add rules for each checklist item
        foreach ($checklistItems as $item) {
            $validationRules["checklist_results.{$item}"] = ['required', 'string', Rule::in(['ok', 'defect'])];
        }

        $validatedData = $request->validate($validationRules);

        // Determine if any defects were found
        $defectsFound = false;
        foreach ($checklistItems as $item) {
            if (isset($validatedData['checklist_results'][$item]) && $validatedData['checklist_results'][$item] === 'defect') {
                $defectsFound = true;
                break;
            }
        }
        $validatedData['defects_found'] = $defectsFound;


        // --- Handle Defect Photos Upload (Conceptual) ---
        $defectPhotoPaths = [];
        if ($request->hasFile('defect_photos')) {
            foreach ($request->file('defect_photos') as $file) {
                // In a real app, you'd store these to disk and save the path
                // For now, we'll simulate by adding placeholder paths or converting to base64.
                $path = $file->store('inspections/defects', 'public'); // Stores to storage/app/public/inspections/defects
                $defectPhotoPaths[] = Storage::url($path); // Get public URL
            }
        }
        $validatedData['defect_photos'] = $defectPhotoPaths;

        // --- Handle Signature Image (Conceptual) ---
        // If signature is submitted as base64 data URL
        if ($request->filled('driver_signature_image')) {
            // Remove 'data:image/png;base64,' part
            $imageData = $request->input('driver_signature_image');
            list($type, $imageData) = explode(';', $imageData);
            list(, $imageData) = explode(',', $imageData);
            $imageData = base64_decode($imageData);

            $fileName = 'signatures/' . Str::uuid() . '.png';
            Storage::disk('public')->put($fileName, $imageData);
            $validatedData['driver_signature_image'] = Storage::url($fileName);
        }

        // Create the inspection record
        $inspection = new VehicleInspection($validatedData);
        $inspection->driver_id = $driverId;
        $inspection->vendor_id = $vendorId;
        $inspection->save();

        // Optional: Notify dispatch/mechanics if defects_found is true
        if ($defectsFound) {
            // Trigger notification
            // showNotification('Defects reported in DVIR for ' . $validatedData['vehicle_id'], 'warning');
        }

        return redirect()->route('driver.vehicle_inspection.index')->with('success', 'Vehicle inspection submitted successfully!');
    }

    /**
     * Display the specified past inspection details.
     */
    public function show(VehicleInspection $vehicleInspection)
    {
        $user = Auth->guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth->guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed.');
        }

        // Ensure the inspection belongs to this driver and vendor
        if ($vehicleInspection->driver_id !== $user->id || $vehicleInspection->vendor_id !== $user->vendor_id) {
            return redirect()->route('driver.vehicle_inspection.index')->with('error', 'Unauthorized access to inspection.');
        }

        return view('driver.vehicle_inspection.show', compact('vehicleInspection'));
    }

    /**
     * Conceptual: Allows dispatch/mechanic to update inspection status or notes.
     * Not typically a driver function.
     */
    public function edit(VehicleInspection $vehicleInspection)
    {
        // Drivers typically don't edit past DVIRs directly for compliance reasons.
        // This method would be for dispatch/mechanic in a full system.
        return redirect()->back()->with('error', 'Editing past inspections is restricted.');
    }

    public function update(Request $request, VehicleInspection $vehicleInspection)
    {
        // Restricted as above.
        return redirect()->back()->with('error', 'Updating past inspections is restricted.');
    }

    public function destroy(VehicleInspection $vehicleInspection)
    {
        // DVIRs are compliance records and are usually not deleted.
        // This method should be restricted or require high-level admin privileges.
        return redirect()->back()->with('error', 'Deleting inspection records is restricted.');
    }
}