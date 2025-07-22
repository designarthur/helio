<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Vendor; // Assuming Vendor model exists for relationships
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // For authenticating vendors

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        // Get the authenticated vendor's ID
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

        $query = Customer::where('vendor_id', $vendorId);

        // Apply filters if present in the request (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('company', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('billing_address', 'like', '%' . $searchTerm . '%')
                  ->orWhere('internal_notes', 'like', '%' . $searchTerm . '%');
            });
        }
        if ($request->has('type_filter') && $request->input('type_filter') !== null) {
            $query->where('customer_type', $request->input('type_filter'));
        }
        if ($request->has('status_filter') && $request->input('status_filter') !== null) {
            $query->where('status', $request->input('status_filter'));
        }

        $customers = $query->orderBy('updated_at', 'desc')->paginate(10); // Paginate results

        return view('vendor.customer.index', compact('customers', 'vendor'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        // Ensure only authenticated vendors can create
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to add customers.');
        }

        return view('vendor.customer.create-edit');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        // Get the authenticated vendor's ID
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,NULL,id,vendor_id,' . $vendorId, // Email unique per vendor
            'phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'service_addresses' => 'nullable|string', // Will be cast to array by model
            'customer_type' => 'required|string|in:Residential,Commercial',
            'status' => 'required|string|in:Active,Inactive,On Hold',
            'internal_notes' => 'nullable|string',
        ]);

        // Convert comma-separated string to array for 'service_addresses'
        if (isset($validatedData['service_addresses'])) {
            $validatedData['service_addresses'] = array_map('trim', explode(';', $validatedData['service_addresses']));
            // Remove empty strings that might result from trailing semicolons or multiple semicolons
            $validatedData['service_addresses'] = array_filter($validatedData['service_addresses']);
        } else {
            $validatedData['service_addresses'] = [];
        }

        // Create customer with vendor_id
        $customer = new Customer($validatedData);
        $customer->vendor_id = $vendorId;
        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        // Ensure the authenticated vendor owns this customer
        if (!Auth::guard('vendor')->check() || $customer->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('customers.index')->with('error', 'Unauthorized access to customer.');
        }

        return view('vendor.customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        // Ensure the authenticated vendor owns this customer
        if (!Auth::guard('vendor')->check() || $customer->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('customers.index')->with('error', 'Unauthorized access to customer.');
        }

        return view('vendor.customer.create-edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // Ensure the authenticated vendor owns this customer
        if (!Auth::guard('vendor')->check() || $customer->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id . ',id,vendor_id,' . Auth::guard('vendor')->id(), // Email unique per vendor, ignoring current customer
            'phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:255',
            'service_addresses' => 'nullable|string', // Will be cast to array by model
            'customer_type' => 'required|string|in:Residential,Commercial',
            'status' => 'required|string|in:Active,Inactive,On Hold',
            'internal_notes' => 'nullable|string',
        ]);

        // Convert comma-separated string to array for 'service_addresses'
        if (isset($validatedData['service_addresses'])) {
            $validatedData['service_addresses'] = array_map('trim', explode(';', $validatedData['service_addresses']));
            $validatedData['service_addresses'] = array_filter($validatedData['service_addresses']); // Remove empty strings
        } else {
            $validatedData['service_addresses'] = []; // Ensure it's an empty array if null
        }

        // Set nullable fields to null if they become empty strings from the form
        foreach (['company', 'internal_notes'] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }

        $customer->update($validatedData);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        // Ensure the authenticated vendor owns this customer
        if (!Auth::guard('vendor')->check() || $customer->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}