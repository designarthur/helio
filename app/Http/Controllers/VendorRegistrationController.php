<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VendorRegistrationController extends Controller
{
    /**
     * Display the vendor registration form.
     */
    public function create()
    {
        return view('auth.vendor-register'); // This will point to resources/views/auth/vendor-register.blade.php
    }

    /**
     * Handle an incoming vendor registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming request data
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:vendors'], // 'unique:vendors' checks if email is unique in 'vendors' table
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' checks for password_confirmation field
            'phone' => ['nullable', 'string', 'max:20'],
            'primary_address' => ['nullable', 'string', 'max:255'],
            // Add validation for other fields if needed, e.g., operating_hours, service_areas
        ]);

        // 2. Create the new Vendor
        $vendor = Vendor::create([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password before saving
            'phone' => $request->phone,
            'primary_address' => $request->primary_address,
            'status' => 'Pending Approval', // Default status for new registrations
            'subscription_plan' => 'Basic', // Default plan
            // Initial branding settings (you can set defaults or let them update later)
            'branding_settings' => [
                'logoUrl' => 'https://via.placeholder.com/100x40/E0E0E0/6C757D?text=Default+Logo',
                'faviconUrl' => 'https://via.placeholder.com/32x32/E0E0E0/6C757D?text=Fav',
                'primaryColor' => '#EA3A26', // Chili Red
                'secondaryColor' => '#FF8600', // UT Orange
                'customDomain' => '',
                'senderName' => $request->company_name, // Default sender name from company
                'replyToEmail' => $request->email,
                'portalBannerText' => 'Welcome to ' . $request->company_name . '\'s Rental Portal!',
                'customCss' => '',
                'customJs' => '',
            ],
        ]);

        // 3. Optionally, log the vendor in immediately or redirect to a "Registration Successful, Awaiting Approval" page
        // auth()->guard('vendor')->login($vendor); // If you want to log them in directly

        return redirect()->route('vendor.registration.success')
                         ->with('success', 'Registration successful! Your account is pending approval.');
    }

    /**
     * Display a registration success message.
     */
    public function success()
    {
        return view('auth.registration-success'); // Point to a success page
    }
}