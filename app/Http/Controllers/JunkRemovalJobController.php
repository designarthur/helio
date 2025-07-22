<?php

namespace App\Http\Controllers;

use App\Models\JunkRemovalJob;
use App\Models\Vendor; // For authentication fallback
use App\Models\Customer; // For dropdowns
use App\Models\User; // For drivers dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JunkRemovalJobController extends Controller
{
    /**
     * Display a listing of the junk removal jobs.
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            $vendor = Vendor::first();
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;

        $query = JunkRemovalJob::where('vendor_id', $vendorId)
                               ->with(['customer', 'driver']); // Eager load relationships

        // Apply filters (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('job_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('job_location', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description_of_junk', 'like', '%' . $searchTerm . '%')
                  ->orWhere('job_notes', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('customer', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        if ($request->has('status_filter') && $request->input('status_filter') !== null) {
            $query->where('status', $request->input('status_filter'));
        }

        $junkRemovalJobs = $query->orderBy('requested_date', 'desc')->paginate(10);

        return view('vendor.junk_removal.index', compact('junkRemovalJobs', 'vendor'));
    }

    /**
     * Show the form for creating a new junk removal job.
     */
    public function create()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to create junk removal jobs.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        $drivers = User::where('vendor_id', $vendorId)->where('role', 'Driver')->get(); // Only drivers for THIS vendor

        return view('vendor.junk_removal.create-edit', compact('customers', 'drivers'));
    }

    /**
     * Store a newly created junk removal job in storage.
     */
    public function store(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId],
            'requested_date' => ['required', 'date', 'after_or_equal:today'],
            'requested_time' => ['nullable', 'date_format:H:i'],
            'job_location' => ['required', 'string', 'max:255'],
            'description_of_junk' => ['required', 'string'],
            'volume_estimate' => ['nullable', 'string', 'max:255'],
            'weight_estimate' => ['nullable', 'string', 'max:255'],
            'crew_requirements' => ['required', 'integer', 'min:1'],
            'assigned_driver' => ['nullable', 'exists:users,id,vendor_id,' . $vendorId], // Driver must belong to this vendor
            'estimated_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in(['Pending Quote', 'Quoted', 'Scheduled', 'In Progress', 'Completed', 'Cancelled'])],
            'job_notes' => ['nullable', 'string'],
            // 'customer_uploaded_images' => ['nullable', 'array'], // If implementing file uploads later
            // 'customer_uploaded_videos' => ['nullable', 'array'],
        ]);

        // Generate a simple unique job number
        $jobNumber = 'JR' . (JunkRemovalJob::where('vendor_id', $vendorId)->count() + 1 + mt_rand(100, 999));

        $job = new JunkRemovalJob($validatedData);
        $job->vendor_id = $vendorId;
        $job->job_number = $jobNumber; // Assign generated job number
        $job->save();

        return redirect()->route('junk_removal_jobs.index')->with('success', 'Junk Removal Job ' . $job->job_number . ' created successfully!');
    }

    /**
     * Display the specified junk removal job.
     */
    public function show(JunkRemovalJob $junkRemovalJob)
    {
        if (!Auth::guard('vendor')->check() || $junkRemovalJob->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('junk_removal_jobs.index')->with('error', 'Unauthorized access to junk removal job.');
        }

        $junkRemovalJob->load(['customer', 'driver']); // Eager load relationships

        return view('vendor.junk_removal.show', compact('junkRemovalJob'));
    }

    /**
     * Show the form for editing the specified junk removal job.
     */
    public function edit(JunkRemovalJob $junkRemovalJob)
    {
        if (!Auth::guard('vendor')->check() || $junkRemovalJob->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('junk_removal_jobs.index')->with('error', 'Unauthorized access to junk removal job.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        $drivers = User::where('vendor_id', $vendorId)->where('role', 'Driver')->get();

        return view('vendor.junk_removal.create-edit', compact('junkRemovalJob', 'customers', 'drivers'));
    }

    /**
     * Update the specified junk removal job in storage.
     */
    public function update(Request $request, JunkRemovalJob $junkRemovalJob)
    {
        if (!Auth::guard('vendor')->check() || $junkRemovalJob->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $vendorId = Auth::guard('vendor')->id();

        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId],
            'requested_date' => ['required', 'date'],
            'requested_time' => ['nullable', 'date_format:H:i'],
            'job_location' => ['required', 'string', 'max:255'],
            'description_of_junk' => ['required', 'string'],
            'volume_estimate' => ['nullable', 'string', 'max:255'],
            'weight_estimate' => ['nullable', 'string', 'max:255'],
            'crew_requirements' => ['required', 'integer', 'min:1'],
            'assigned_driver' => ['nullable', 'exists:users,id,vendor_id,' . $vendorId],
            'estimated_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', Rule::in(['Pending Quote', 'Quoted', 'Scheduled', 'In Progress', 'Completed', 'Cancelled'])],
            'job_notes' => ['nullable', 'string'],
        ]);

        // Set nullable fields to null if they become empty strings from the form
        foreach ([
            'requested_time', 'volume_estimate', 'weight_estimate',
            'assigned_driver', 'job_notes'
        ] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }
        
        $junkRemovalJob->update($validatedData);

        return redirect()->route('junk_removal_jobs.index')->with('success', 'Junk Removal Job ' . $junkRemovalJob->job_number . ' updated successfully!');
    }

    /**
     * Remove the specified junk removal job from storage.
     */
    public function destroy(JunkRemovalJob $junkRemovalJob)
    {
        if (!Auth::guard('vendor')->check() || $junkRemovalJob->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $junkRemovalJob->delete();

        return redirect()->route('junk_removal_jobs.index')->with('success', 'Junk Removal Job ' . $junkRemovalJob->job_number . ' deleted successfully!');
    }

    // TODO: Implement methods for visual quoting if the user decides to proceed with that complexity
    // public function processVisualQuote(Request $request) { ... }
}