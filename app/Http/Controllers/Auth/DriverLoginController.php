<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DriverLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Driver Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating drivers for the application and
    | redirecting them to their driver dashboard.
    |
    */

    /**
     * Show the driver login form.
     */
    public function showLoginForm()
    {
        return view('driver.login'); // This will point to resources/views/driver/login.blade.php
    }

    /**
     * Handle an incoming driver authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate using the 'driver' guard
        // We also check for 'Driver' role to ensure only drivers can log in via this guard
        if (Auth::guard('driver')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 'Driver'], $request->filled('remember'))) {
            $request->session()->regenerate();

            // Redirect to the driver dashboard
            return redirect()->intended(route('driver.dashboard')); // Define 'driver.dashboard' route later
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')], // Using Laravel's default auth.failed message
        ]);
    }

    /**
     * Log the driver out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('driver')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/driver/login')->with('success', 'You have been logged out.'); // Redirect back to driver login page
    }
}