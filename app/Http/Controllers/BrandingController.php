<?php

namespace App\Http\Controllers;

use App\Models\Vendor; // We need the Vendor model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // For file uploads (conceptual)
use Illuminate\Validation\Rule;

class BrandingController extends Controller
{
    /**
     * Display the branding settings page.
     * This will effectively be the 'index' or 'show' for branding.
     */
    public function show()
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            $vendor = Vendor::first(); // Fallback for dev if not authenticated
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }

        // Get current branding settings or apply defaults if not set
        $brandingSettings = $vendor->branding_settings ?? [
            'logoUrl' => 'https://via.placeholder.com/100x40/E0E0E0/6C757D?text=Default+Logo',
            'faviconUrl' => 'https://via.placeholder.com/32x32/E0E0E0/6C757D?text=Fav',
            'primaryColor' => '#EA3A26', // Chili Red
            'secondaryColor' => '#FF8600', // UT Orange
            'customDomain' => '',
            'senderName' => $vendor->company_name ?? 'Your Company Rentals',
            'replyToEmail' => $vendor->email ?? 'info@yourcompany.com',
            'portalBannerText' => 'Welcome to ' . ($vendor->company_name ?? 'Your Company') . '\'s Rental Portal!',
            'customCss' => '',
            'customJs' => '',
        ];

        return view('vendor.branding.show', compact('vendor', 'brandingSettings'));
    }

    /**
     * Update the branding settings for the authenticated vendor.
     * Note: This method handles ALL branding updates for all tabs in one go.
     */
    public function update(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        // Current branding settings to merge new data into
        $currentBranding = $vendor->branding_settings ?? [];

        // Validate incoming data
        $validatedData = $request->validate([
            'logoUpload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
            'faviconUpload' => 'nullable|image|mimes:ico,jpeg,png,jpg,gif,svg|max:2048',
            'primaryColor' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondaryColor' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'customDomain' => 'nullable|string|max:255',
            'senderName' => 'nullable|string|max:255',
            'replyToEmail' => 'nullable|email|max:255',
            'portalBannerText' => 'nullable|string|max:255',
            'customCss' => 'nullable|string',
            'customJs' => 'nullable|string',
        ]);

        $newBrandingSettings = $currentBranding; // Start with current settings

        // Handle logo upload
        if ($request->hasFile('logoUpload')) {
            // Delete old logo if it's a file on storage (not a placeholder URL)
            if (isset($currentBranding['logoUrl']) && !str_contains($currentBranding['logoUrl'], 'placeholder.com')) {
                // Assuming old logo was stored in 'public' disk
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $currentBranding['logoUrl']));
            }
            $path = $request->file('logoUpload')->store('branding/logos', 'public');
            $newBrandingSettings['logoUrl'] = asset('storage/' . $path); // Store full URL
        } elseif ($request->input('logo_cleared')) { // If a hidden field indicates old logo was cleared
            if (isset($currentBranding['logoUrl']) && !str_contains($currentBranding['logoUrl'], 'placeholder.com')) {
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $currentBranding['logoUrl']));
            }
            $newBrandingSettings['logoUrl'] = 'https://via.placeholder.com/100x40/E0E0E0/6C757D?text=Default+Logo';
        }

        // Handle favicon upload
        if ($request->hasFile('faviconUpload')) {
            if (isset($currentBranding['faviconUrl']) && !str_contains($currentBranding['faviconUrl'], 'placeholder.com')) {
                 Storage::disk('public')->delete(str_replace(asset('storage/'), '', $currentBranding['faviconUrl']));
            }
            $path = $request->file('faviconUpload')->store('branding/favicons', 'public');
            $newBrandingSettings['faviconUrl'] = asset('storage/' . $path);
        } elseif ($request->input('favicon_cleared')) {
            if (isset($currentBranding['faviconUrl']) && !str_contains($currentBranding['faviconUrl'], 'placeholder.com')) {
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $currentBranding['faviconUrl']));
            }
            $newBrandingSettings['faviconUrl'] = 'https://via.placeholder.com/32x32/E0E0E0/6C757D?text=Fav';
        }

        // Update other fields from validated data
        $newBrandingSettings['primaryColor'] = $validatedData['primaryColor'];
        $newBrandingSettings['secondaryColor'] = $validatedData['secondaryColor'];
        $newBrandingSettings['customDomain'] = $validatedData['customDomain'] ?? null;
        $newBrandingSettings['senderName'] = $validatedData['senderName'] ?? null;
        $newBrandingSettings['replyToEmail'] = $validatedData['replyToEmail'] ?? null;
        $newBrandingSettings['portalBannerText'] = $validatedData['portalBannerText'] ?? null;
        $newBrandingSettings['customCss'] = $validatedData['customCss'] ?? null;
        $newBrandingSettings['customJs'] = $validatedData['customJs'] ?? null;

        // Save the updated branding_settings JSON to the vendor
        $vendor->update(['branding_settings' => $newBrandingSettings]);

        return redirect()->route('branding.show')->with('success', 'Branding settings updated successfully!');
    }
}