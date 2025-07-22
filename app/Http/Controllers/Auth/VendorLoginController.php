<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Vendor;
 // Make sure to use the Vendor model

class VendorLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Vendor Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating vendors for the application and
    | redirecting them to their vendor dashboard.
    |
    */

    /**
     * Show the vendor login form.
     */
    public function showLoginForm()
    {
        return view('vendor.login'); // This will point to resources/views/vendor/login.blade.php
    }

    /**
     * Handle an incoming vendor authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate using the 'vendor' guard
        // The Vendor model directly represents the vendor user
        if (Auth::guard('vendor')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            // Redirect to the vendor dashboard
            return redirect()->intended(route('vendor.dashboard')); // This route should point to your main vendor dashboard
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')], // Using Laravel's default auth.failed message
        ]);
    }

    /**
     * Log the vendor out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out.'); // Redirect back to the main login page
    }
}