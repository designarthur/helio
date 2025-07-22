<?php

namespace App\Http\Controllers;

use App\Models\Vendor; // For company profile and notification settings
use App\Models\User;   // For managing internal users
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // For user password hashing
use Illuminate\Validation\Rule;


class SettingsController extends Controller
{
    /**
     * Display the main settings page.
     * This method will load the default 'profile' tab content.
     */
    public function show(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            $vendor = Vendor::first(); // Fallback for dev if not authenticated
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;

        // Fetch data based on the requested tab or default to 'profile'
        $tab = $request->input('tab', 'profile');

        $internalUsers = collect([]); // Initialize as empty collection
        if ($tab === 'users') {
            // Fetch users belonging to this vendor (excluding the vendor admin themselves, or based on specific role)
            $internalUsers = User::where('vendor_id', $vendorId)
                                 ->where('role', '!=', 'Vendor Admin') // Assuming 'Vendor Admin' is a distinct role for the main vendor user
                                 ->paginate(10);
        }

        // Default settings for initial load or if not yet saved
        $vendorSettings = $vendor->toArray(); // Get all direct vendor attributes

        // Get notification settings (can be separate fields in vendor table or part of a json column)
        // For now, these are direct columns on the vendor table or simple booleans.
        // We will adapt this structure if vendor_settings uses a JSON column for these.
        $notificationSettings = [
            'notifyNewBooking' => $vendor->notify_new_booking ?? false,
            'notifyPaymentReceived' => $vendor->notify_payment_received ?? false,
            'notifyOverdueInvoice' => $vendor->notify_overdue_invoice ?? false,
            'notifyDriverIssues' => $vendor->notify_driver_issues ?? false,
        ];
        // Merge with overall vendor settings for easier access in view
        $vendorSettings = array_merge($vendorSettings, $notificationSettings);


        // For Integrations tab, if conceptual fields were direct columns:
        $integrationSettings = [
            'smsApiKey' => $vendor->sms_api_key ?? null,
            'chatCode' => $vendor->chat_code ?? null,
        ];
        $vendorSettings = array_merge($vendorSettings, $integrationSettings);

        return view('vendor.settings.show', compact('vendor', 'tab', 'vendorSettings', 'internalUsers'));
    }

    /**
     * Update vendor company profile settings.
     */
    public function updateProfile(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'string', 'email', 'max:255', Rule::unique('vendors', 'email')->ignore($vendor->id)],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'primary_address' => ['nullable', 'string', 'max:255'],
            'operating_hours' => ['nullable', 'string', 'max:255'],
            'service_areas' => ['nullable', 'string'], // Comma-separated or JSON string
        ]);

        // Convert service_areas to array if needed by model casting or store as string
        if (isset($validatedData['service_areas'])) {
            $validatedData['service_areas'] = array_map('trim', explode(',', $validatedData['service_areas']));
            $validatedData['service_areas'] = array_filter($validatedData['service_areas']); // Remove empty strings
        } else {
            $validatedData['service_areas'] = [];
        }

        // Map request fields to vendor model attributes
        $vendor->company_name = $validatedData['company_name'];
        $vendor->email = $validatedData['contact_email']; // Update main vendor email
        $vendor->phone = $validatedData['contact_phone'] ?? null;
        $vendor->primary_address = $validatedData['primary_address'] ?? null;
        $vendor->operating_hours = $validatedData['operating_hours'] ?? null;
        $vendor->service_areas = $validatedData['service_areas']; // Will be JSON-encoded by model cast

        $vendor->save();

        return redirect()->route('settings.show', ['tab' => 'profile'])->with('success', 'Company profile updated successfully!');
    }

    /**
     * Update vendor notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'notifyNewBooking' => ['boolean'],
            'notifyPaymentReceived' => ['boolean'],
            'notifyOverdueInvoice' => ['boolean'],
            'notifyDriverIssues' => ['boolean'],
        ]);

        // These would be direct boolean columns on the Vendor model
        $vendor->notify_new_booking = $request->has('notifyNewBooking');
        $vendor->notify_payment_received = $request->has('notifyPaymentReceived');
        $vendor->notify_overdue_invoice = $request->has('notifyOverdueInvoice');
        $vendor->notify_driver_issues = $request->has('notifyDriverIssues');
        
        $vendor->save();

        return redirect()->route('settings.show', ['tab' => 'notifications'])->with('success', 'Notification settings updated successfully!');
    }

    /**
     * Update vendor integration settings (conceptual).
     */
    public function updateIntegrations(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'smsApiKey' => ['nullable', 'string', 'max:255'],
            'chatCode' => ['nullable', 'string'],
        ]);

        // These would be direct columns on the Vendor model or part of branding_settings JSON
        $vendor->sms_api_key = $validatedData['smsApiKey'] ?? null;
        $vendor->chat_code = $validatedData['chatCode'] ?? null;

        $vendor->save();

        return redirect()->route('settings.show', ['tab' => 'integrations'])->with('success', 'Integration settings updated successfully!');
    }

    // --- Internal User Management (CRUD for Users with roles) ---
    // These methods handle Users who are staff members (not vendors themselves)
    // For simplicity, these will be in SettingsController for now, but in larger apps,
    // they might warrant their own UserController if the functionality gets complex.

    // Display list of internal users (already done partially in show method based on tab)
    // public function indexUsers() { ... } // If 'users' was a resource under settings

    // Show form to add/edit internal user
    public function createUser()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in to manage users.');
        }
        $vendorId = Auth::guard('vendor')->id();
        // Assuming roles are fixed, otherwise fetch from config/db
        $roles = ['Admin', 'Dispatcher', 'Driver', 'Sales', 'Viewer'];
        $statuses = ['Active', 'Inactive'];

        return view('vendor.settings.users.create-edit', compact('roles', 'statuses'));
    }

    public function storeUser(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }
        $vendorId = $vendor->id;

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,NULL,id,vendor_id,' . $vendorId], // Email unique per vendor
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(['Admin', 'Dispatcher', 'Driver', 'Sales', 'Viewer'])],
            'status' => ['required', 'string', Rule::in(['Active', 'Inactive'])],
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['vendor_id'] = $vendorId;

        User::create($validatedData);

        return redirect()->route('settings.show', ['tab' => 'users'])->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        if (!Auth::guard('vendor')->check() || $user->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('settings.show', ['tab' => 'users'])->with('error', 'Unauthorized access to user.');
        }
        // Assuming roles are fixed
        $roles = ['Admin', 'Dispatcher', 'Driver', 'Sales', 'Viewer'];
        $statuses = ['Active', 'Inactive'];

        return view('vendor.settings.users.create-edit', compact('user', 'roles', 'statuses'));
    }

    public function updateUser(Request $request, User $user)
    {
        if (!Auth::guard('vendor')->check() || $user->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        $vendorId = Auth::guard('vendor')->id();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)->where(function ($query) use ($vendorId) {
                return $query->where('vendor_id', $vendorId);
            })],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Optional password change
            'role' => ['required', 'string', Rule::in(['Admin', 'Dispatcher', 'Driver', 'Sales', 'Viewer'])],
            'status' => ['required', 'string', Rule::in(['Active', 'Inactive'])],
        ]);

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']); // Don't update password if empty
        }

        $user->update($validatedData);

        return redirect()->route('settings.show', ['tab' => 'users'])->with('success', 'User updated successfully!');
    }

    public function destroyUser(User $user)
    {
        if (!Auth::guard('vendor')->check() || $user->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Prevent deleting the main vendor admin user (if applicable) or self-deletion
        if ($user->role === 'Vendor Admin' || $user->id === Auth::guard('vendor')->id()) {
             return redirect()->back()->with('error', 'Cannot delete this user.');
        }

        // Unassign user from any bookings if they were a driver
        Booking::where('driver_id', $user->id)->update(['driver_id' => null]);

        $user->delete();

        return redirect()->route('settings.show', ['tab' => 'users'])->with('success', 'User deleted successfully!');
    }
}