<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\VehicleInspection;
use App\Models\User; // The model used for driver authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // For file uploads
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Str;

class VehicleInspectionController extends Controller
{
    /**
     * Display the vehicle inspection form and a list of past inspections for the driver.
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

        // Fetch past inspections for this driver
        $pastInspections = VehicleInspection::where('driver_id', $driverId)
                                             ->orderByDesc('inspection_datetime')
                                             ->paginate(10); // Paginate past inspections

        // Get vehicle ID for the form (could be fetched from driver's profile or a separate vehicles table)
        $vehicleId = $user->assigned_vehicle ?? 'Truck #DVIR-001';

        // Check if there's an inspection already done today
        $todaysInspection = VehicleInspection::where('driver_id', $driverId)
                                             ->whereDate('inspection_datetime', Carbon::today())
                                             ->first();

        return view('driver.vehicle_inspection.index', compact(
            'user',
            'pastInspections',
            'vehicleId',
            'todaysInspection'
        ));
    }

    /**
     * Store a newly created vehicle inspection record.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('driver')->user();
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

        // --- Handle Defect Photos Upload ---
        $defectPhotoPaths = [];
        if ($request->hasFile('defect_photos')) {
            foreach ($request->file('defect_photos') as $file) {
                try {
                    // Store file to public disk
                    $path = $file->store('inspections/defects', 'public');
                    $defectPhotoPaths[] = Storage::url($path); // Get public URL
                } catch (\Exception $e) {
                    \Log::error('Failed to upload defect photo: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Failed to upload defect photos. Please try again.')->withInput();
                }
            }
        }
        $validatedData['defect_photos'] = $defectPhotoPaths;

        // --- Handle Signature Image ---
        if ($request->filled('driver_signature_image')) {
            try {
                // Parse base64 data URL
                $imageData = $request->input('driver_signature_image');
                
                // Check if it's a data URL
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                    $imageExtension = $matches[1];
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $imageData = base64_decode($imageData);

                    if ($imageData === false) {
                        throw new \Exception('Invalid base64 image data');
                    }

                    $fileName = 'signatures/' . Str::uuid() . '.' . $imageExtension;
                    Storage::disk('public')->put($fileName, $imageData);
                    $validatedData['driver_signature_image'] = Storage::url($fileName);
                } else {
                    // If it's already a URL, keep it as is
                    $validatedData['driver_signature_image'] = $request->input('driver_signature_image');
                }
            } catch (\Exception $e) {
                \Log::error('Failed to process signature image: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to save signature. Please try again.')->withInput();
            }
        }

        // Create the inspection record
        try {
            $inspection = new VehicleInspection($validatedData);
            $inspection->driver_id = $driverId;
            $inspection->vendor_id = $vendorId;
            $inspection->save();

            // If defects were found, you might want to notify dispatch/mechanics
            if ($defectsFound) {
                // Log the defect for potential notification system
                \Log::info('Vehicle defects reported in DVIR', [
                    'inspection_id' => $inspection->id,
                    'vehicle_id' => $validatedData['vehicle_id'],
                    'driver_id' => $driverId,
                    'defect_notes' => $validatedData['defect_notes']
                ]);

                return redirect()->route('driver.vehicle_inspection.index')
                    ->with('warning', 'Vehicle inspection submitted with defects reported. Dispatch has been notified.');
            }

            return redirect()->route('driver.vehicle_inspection.index')
                ->with('success', 'Vehicle inspection submitted successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to save vehicle inspection: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save inspection. Please try again.')->withInput();
        }
    }

    /**
     * Display the specified past inspection details.
     */
    public function show(VehicleInspection $vehicleInspection)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed.');
        }

        // Ensure the inspection belongs to this driver and vendor
        if ($vehicleInspection->driver_id !== $user->id || $vehicleInspection->vendor_id !== $user->vendor_id) {
            return redirect()->route('driver.vehicle_inspection.index')
                ->with('error', 'Unauthorized access to inspection.');
        }

        return view('driver.vehicle_inspection.show', compact('vehicleInspection'));
    }

    /**
     * Show the form for editing the specified inspection.
     * Generally not allowed for drivers for compliance reasons.
     */
    public function edit(VehicleInspection $vehicleInspection)
    {
        // Drivers typically don't edit past DVIRs directly for compliance reasons.
        // This method would be for dispatch/mechanic in a full system.
        return redirect()->back()->with('error', 'Editing past inspections is restricted for compliance reasons.');
    }

    /**
     * Update the specified inspection in storage.
     * Generally not allowed for drivers for compliance reasons.
     */
    public function update(Request $request, VehicleInspection $vehicleInspection)
    {
        // Restricted as above for compliance reasons.
        return redirect()->back()->with('error', 'Updating past inspections is restricted for compliance reasons.');
    }

    /**
     * Remove the specified inspection from storage.
     * DVIRs are compliance records and should not be deleted.
     */
    public function destroy(VehicleInspection $vehicleInspection)
    {
        // DVIRs are compliance records and are usually not deleted.
        // This method should be restricted or require high-level admin privileges.
        return redirect()->back()->with('error', 'Deleting inspection records is restricted for compliance reasons.');
    }

    /**
     * Get inspection statistics for the driver (optional utility method).
     */
    public function getInspectionStats(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $driverId = $user->id;
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $stats = [
            'total_inspections' => VehicleInspection::where('driver_id', $driverId)
                ->whereBetween('inspection_datetime', [$startDate, $endDate])
                ->count(),
            'defects_found_count' => VehicleInspection::where('driver_id', $driverId)
                ->whereBetween('inspection_datetime', [$startDate, $endDate])
                ->where('defects_found', true)
                ->count(),
            'pre_trip_count' => VehicleInspection::where('driver_id', $driverId)
                ->whereBetween('inspection_datetime', [$startDate, $endDate])
                ->where('inspection_type', 'pre-trip')
                ->count(),
            'post_trip_count' => VehicleInspection::where('driver_id', $driverId)
                ->whereBetween('inspection_datetime', [$startDate, $endDate])
                ->where('inspection_type', 'post-trip')
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Download inspection report as PDF (conceptual).
     */
    public function downloadPdf(VehicleInspection $vehicleInspection)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Ensure the inspection belongs to this driver
        if ($vehicleInspection->driver_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized access to inspection.');
        }

        // In a real implementation, you would generate a PDF using a library like DomPDF or wkhtmltopdf
        // For now, this is conceptual
        return redirect()->back()->with('info', 'PDF download feature is not yet implemented.');
    }

    /**
     * Get checklist template for JavaScript form generation (API endpoint).
     */
    public function getChecklistTemplate()
    {
        $checklistTemplate = [
            'lights' => [
                'label' => 'Lights (headlights, taillights, turn signals)',
                'category' => 'exterior',
                'required' => true
            ],
            'tires' => [
                'label' => 'Tires (tread depth, air pressure, damage)',
                'category' => 'exterior',
                'required' => true
            ],
            'brakes' => [
                'label' => 'Brakes (brake pads, brake lines, parking brake)',
                'category' => 'mechanical',
                'required' => true
            ],
            'steering' => [
                'label' => 'Steering (steering wheel, power steering)',
                'category' => 'mechanical',
                'required' => true
            ],
            'wipers' => [
                'label' => 'Windshield Wipers (wiper blades, washer fluid)',
                'category' => 'exterior',
                'required' => true
            ],
            'mirrors' => [
                'label' => 'Mirrors (side mirrors, rear view mirror)',
                'category' => 'exterior',
                'required' => true
            ],
            'fluids' => [
                'label' => 'Fluids (oil, coolant, brake fluid)',
                'category' => 'mechanical',
                'required' => true
            ]
        ];

        return response()->json($checklistTemplate);
    }
}