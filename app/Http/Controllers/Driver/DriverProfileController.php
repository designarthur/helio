<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\User; // The model used for driver authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DriverProfileController extends Controller
{
    /**
     * Display the driver's profile.
     */
    public function show()
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed. Please log in as a driver.');
        }

        // Calculate some profile statistics
        $profileStats = [
            'account_created' => $user->created_at->diffForHumans(),
            'last_updated' => $user->updated_at->diffForHumans(),
            'license_expires_in' => $user->license_expiry ? Carbon::parse($user->license_expiry)->diffForHumans() : 'Not set',
            'certifications_count' => is_array($user->certifications) ? count($user->certifications) : 0,
        ];

        return view('driver.profile.show', compact('user', 'profileStats'));
    }

    /**
     * Show the form for editing the driver's profile.
     */
    public function edit()
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            Auth::guard('driver')->logout();
            return redirect()->route('driver.login')->with('error', 'Authentication failed.');
        }

        return view('driver.profile.edit', compact('user'));
    }

    /**
     * Update the driver's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return redirect()->back()->with('error', 'Authentication failed.');
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)->where(function ($query) use ($user) {
                return $query->where('vendor_id', $user->vendor_id);
            })],
            'phone' => ['nullable', 'string', 'max:20'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'license_expiry' => ['nullable', 'date', 'after:today'],
            'cdl_class' => ['nullable', 'string', 'max:255'],
            'certifications' => ['nullable', 'string'], // Comma-separated
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Optional password update
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif|max:2048'], // 2MB max
        ]);

        // Handle certifications (convert comma-separated string to array)
        if (isset($validatedData['certifications'])) {
            $validatedData['certifications'] = array_map('trim', explode(',', $validatedData['certifications']));
            $validatedData['certifications'] = array_filter($validatedData['certifications']);
        } else {
            $validatedData['certifications'] = [];
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            try {
                // Delete old photo if exists
                if ($user->profile_photo && !str_contains($user->profile_photo, 'placeholder')) {
                    $oldPhotoPath = str_replace(asset('storage/'), '', $user->profile_photo);
                    Storage::disk('public')->delete($oldPhotoPath);
                }

                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $validatedData['profile_photo'] = asset('storage/' . $path);
            } catch (\Exception $e) {
                \Log::error('Failed to upload profile photo: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to upload profile photo. Please try again.')->withInput();
            }
        }

        // Handle password update
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Update user profile
        try {
            $user->update($validatedData);
            return redirect()->route('driver.profile.show')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to update driver profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update profile. Please try again.')->withInput();
        }
    }

    /**
     * Update personal information (AJAX endpoint).
     */
    public function updatePersonal(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)->where(function ($query) use ($user) {
                return $query->where('vendor_id', $user->vendor_id);
            })],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $user->update($validatedData);
            return response()->json(['success' => true, 'message' => 'Personal information updated successfully!']);
        } catch (\Exception $e) {
            \Log::error('Failed to update driver personal info: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update personal information.'], 500);
        }
    }

    /**
     * Update license information (AJAX endpoint).
     */
    public function updateLicense(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $validatedData = $request->validate([
            'license_number' => ['nullable', 'string', 'max:255'],
            'license_expiry' => ['nullable', 'date', 'after:today'],
            'cdl_class' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $user->update($validatedData);
            
            // Check if license is expiring soon (within 30 days)
            $expiryWarning = null;
            if ($validatedData['license_expiry']) {
                $expiryDate = Carbon::parse($validatedData['license_expiry']);
                $daysUntilExpiry = Carbon::now()->diffInDays($expiryDate, false);
                
                if ($daysUntilExpiry <= 30 && $daysUntilExpiry >= 0) {
                    $expiryWarning = "Your license expires in {$daysUntilExpiry} days. Please renew soon.";
                } elseif ($daysUntilExpiry < 0) {
                    $expiryWarning = "Your license has expired! Please renew immediately.";
                }
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'License information updated successfully!',
                'warning' => $expiryWarning
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update driver license info: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update license information.'], 500);
        }
    }

    /**
     * Update certifications (AJAX endpoint).
     */
    public function updateCertifications(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $validatedData = $request->validate([
            'certifications' => ['nullable', 'array'],
            'certifications.*' => ['string', 'max:255'],
        ]);

        try {
            $user->update([
                'certifications' => $validatedData['certifications'] ?? []
            ]);
            
            $certCount = count($validatedData['certifications'] ?? []);
            return response()->json([
                'success' => true, 
                'message' => "Certifications updated successfully! You have {$certCount} certification(s)."
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update driver certifications: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update certifications.'], 500);
        }
    }

    /**
     * Upload profile photo (AJAX endpoint).
     */
    public function uploadProfilePhoto(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif|max:2048'],
        ]);

        try {
            // Delete old photo if exists
            if ($user->profile_photo && !str_contains($user->profile_photo, 'placeholder')) {
                $oldPhotoPath = str_replace(asset('storage/'), '', $user->profile_photo);
                Storage::disk('public')->delete($oldPhotoPath);
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $photoUrl = asset('storage/' . $path);

            $user->update(['profile_photo' => $photoUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully!',
                'photo_url' => $photoUrl
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to upload profile photo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload profile photo.'], 500);
        }
    }

    /**
     * Remove profile photo (AJAX endpoint).
     */
    public function removeProfilePhoto()
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        try {
            // Delete photo file if exists
            if ($user->profile_photo && !str_contains($user->profile_photo, 'placeholder')) {
                $oldPhotoPath = str_replace(asset('storage/'), '', $user->profile_photo);
                Storage::disk('public')->delete($oldPhotoPath);
            }

            // Set to default placeholder
            $defaultPhoto = 'https://via.placeholder.com/150x150/E0E0E0/6C757D?text=Driver';
            $user->update(['profile_photo' => $defaultPhoto]);

            return response()->json([
                'success' => true,
                'message' => 'Profile photo removed successfully!',
                'photo_url' => $defaultPhoto
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to remove profile photo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to remove profile photo.'], 500);
        }
    }

    /**
     * Get driver statistics for profile dashboard.
     */
    public function getDriverStats()
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        // In a real system, you'd calculate these from the database
        $stats = [
            'total_jobs_completed' => \App\Models\Booking::where('driver_id', $user->id)
                ->where('status', 'Completed')->count(),
            'total_miles_driven' => 'N/A', // Would need odometer tracking
            'safety_score' => 'A+', // Would calculate from incidents/violations
            'on_time_delivery_rate' => '98%', // Would calculate from delivery times
            'customer_rating' => '4.8/5', // Would calculate from customer feedback
            'years_of_service' => $user->created_at->diffInYears(Carbon::now()),
        ];

        return response()->json($stats);
    }

    /**
     * Update driver availability status.
     */
    public function updateAvailability(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $validatedData = $request->validate([
            'status' => ['required', 'string', Rule::in(['Active', 'On Leave', 'Inactive'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $user->update([
                'status' => $validatedData['status'],
                'driver_notes' => $validatedData['notes'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Availability status updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update driver availability: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update availability.'], 500);
        }
    }

    /**
     * Change password (AJAX endpoint).
     */
    public function changePassword(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return response()->json(['error' => 'Authentication failed'], 403);
        }

        $validatedData = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Verify current password
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 422);
        }

        try {
            $user->update(['password' => Hash::make($validatedData['new_password'])]);
            
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to change driver password: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to change password.'], 500);
        }
    }

    /**
     * Export driver profile data (conceptual).
     */
    public function exportProfile(Request $request)
    {
        $user = Auth::guard('driver')->user();
        if (!$user || $user->role !== 'Driver') {
            return redirect()->back()->with('error', 'Authentication failed.');
        }

        $format = $request->input('format', 'pdf'); // pdf, json
        
        // In a real implementation, you would generate the file based on format
        // For now, this is conceptual
        return redirect()->back()->with('info', 'Profile export feature is not yet implemented.');
    }
} 