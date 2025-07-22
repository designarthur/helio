<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Customer Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating customers for the application and
    | redirecting them to their customer dashboard.
    |
    */

    /**
     * Show the customer login form.
     */
    public function showLoginForm()
    {
        return view('customer.login'); // This will point to resources/views/customer/login.blade.php
    }

    /**
     * Handle an incoming customer authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate using the 'customer' guard
        // We also check for 'customer' role to ensure only customers can log in via this guard
        if (Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 'customer'], $request->filled('remember'))) {
            $request->session()->regenerate();

            // Redirect to the customer dashboard
            return redirect()->intended(route('customer.dashboard')); // Define 'customer.dashboard' route later
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')], // Using Laravel's default auth.failed message
        ]);
    }

    /**
     * Log the customer out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/customer/login')->with('success', 'You have been logged out.'); // Redirect back to customer login page
    }
}