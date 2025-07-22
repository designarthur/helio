<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User; // The model used for customer authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerProfileController extends Controller
{
    /**
     * Display the customer's profile.
     */
    public function show()
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

        return view('customer.profile.show', compact('user', 'customerProfile'));
    }

    /**
     * Show the form for editing the customer's profile.
     */
    public function edit()
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed.');
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            return redirect()->route('customer.profile.show')->with('error', 'Profile not found.');
        }

        return view('customer.profile.edit', compact('user', 'customerProfile'));
    }

    /**
     * Update the customer's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            return redirect()->back()->with('error', 'Authentication failed.');
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            return redirect()->back()->with('error', 'Profile not found.');
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'billing_address' => ['required', 'string', 'max:255'],
            'service_addresses' => ['nullable', 'string'], // Comma-separated addresses
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Optional password update
        ]);

        // Handle service addresses
        if (isset($validatedData['service_addresses'])) {
            $validatedData['service_addresses'] = array_map('trim', explode(';', $validatedData['service_addresses']));
            $validatedData['service_addresses'] = array_filter($validatedData['service_addresses']);
        } else {
            $validatedData['service_addresses'] = [];
        }

        // Update customer profile
        $customerProfile->update([
            'name' => $validatedData['name'],
            'company' => $validatedData['company'],
            'phone' => $validatedData['phone'],
            'billing_address' => $validatedData['billing_address'],
            'service_addresses' => $validatedData['service_addresses'],
        ]);

        // Update user account if password provided
        if (!empty($validatedData['password'])) {
            $user->update([
                'name' => $validatedData['name'],
                'password' => Hash::make($validatedData['password']),
            ]);
        } else {
            $user->update([
                'name' => $validatedData['name'],
            ]);
        }

        return redirect()->route('customer.profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update personal information (AJAX endpoint).
     */
    public function updatePersonal(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $customerProfile->update($validatedData);
        $user->update(['name' => $validatedData['name']]);

        return response()->json(['success' => true, 'message' => 'Personal information updated successfully!']);
    }

    /**
     * Update addresses (AJAX endpoint).
     */
    public function updateAddresses(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $validatedData = $request->validate([
            'billing_address' => ['required', 'string', 'max:255'],
            'service_addresses' => ['nullable', 'array'],
            'service_addresses.*' => ['string', 'max:255'],
        ]);

        $customerProfile->update([
            'billing_address' => $validatedData['billing_address'],
            'service_addresses' => $validatedData['service_addresses'] ?? [],
        ]);

        return response()->json(['success' => true, 'message' => 'Addresses updated successfully!']);
    }

    /**
     * Add a service address.
     */
    public function addServiceAddress(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $validatedData = $request->validate([
            'address' => ['required', 'string', 'max:255'],
        ]);

        $serviceAddresses = $customerProfile->service_addresses ?? [];
        $serviceAddresses[] = $validatedData['address'];

        $customerProfile->update(['service_addresses' => $serviceAddresses]);

        return response()->json(['success' => true, 'message' => 'Service address added successfully!']);
    }

    /**
     * Update a specific service address.
     */
    public function updateServiceAddress(Request $request, $addressId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $validatedData = $request->validate([
            'address' => ['required', 'string', 'max:255'],
        ]);

        $serviceAddresses = $customerProfile->service_addresses ?? [];
        
        if (!isset($serviceAddresses[$addressId])) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        $serviceAddresses[$addressId] = $validatedData['address'];
        $customerProfile->update(['service_addresses' => $serviceAddresses]);

        return response()->json(['success' => true, 'message' => 'Service address updated successfully!']);
    }

    /**
     * Remove a service address.
     */
    public function removeServiceAddress(Request $request, $addressId)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $serviceAddresses = $customerProfile->service_addresses ?? [];
        
        if (!isset($serviceAddresses[$addressId])) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        unset($serviceAddresses[$addressId]);
        $serviceAddresses = array_values($serviceAddresses); // Re-index array

        $customerProfile->update(['service_addresses' => $serviceAddresses]);

        return response()->json(['success' => true, 'message' => 'Service address removed successfully!']);
    }
}
