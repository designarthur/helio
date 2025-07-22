<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Branding Live Preview</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'chili-red': '#EA3A26',
                        'ut-orange': '#FF8600',
                        'tangelo': '#F54F1D',
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen">

    {{-- Main content wrapper - for now, this will be a full page, later part of a layout --}}
    <div class="p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Rebranding & Customization</h2>

        {{-- Success/Error Messages from Controller --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Validation Error!</strong>
                <span class="block sm:inline">Please check your input.</span>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="flex border-b border-gray-200 mb-8 space-x-6">
            <a href="{{ route('branding.show') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">General Branding</a>
            <a href="{{ route('branding.show', ['tab' => 'domain']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Custom Domain</a>
            <a href="{{ route('branding.show', ['tab' => 'email']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Email Templates</a>
            <a href="{{ route('branding.show', ['tab' => 'portal']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Customer Portal</a>
            <a href="{{ route('branding.show', ['tab' => 'preview']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Live Preview</a>
        </div>

        <div id="branding-tab-preview" class="branding-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Live Preview (Client-Side Simulation)</h3>
            <div class="bg-gray-100 p-6 rounded-lg shadow-inner border border-gray-200">
                <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Simulated Customer Portal View</h4>
                <div id="previewArea" class="bg-white p-6 rounded-md shadow-md" style="border: 1px dashed #ccc;">
                    <div id="previewHeader" class="flex items-center pb-4 mb-4 border-b">
                        <img id="previewLogo" src="" alt="Preview Logo" class="h-8 mr-3">
                        <span id="previewSiteName" class="font-bold text-lg text-gray-800">{{ $vendor->company_name ?? 'Your Company Rentals' }}</span>
                        <button class="ml-auto px-3 py-1 rounded-md text-sm text-white" id="previewButton">Dashboard</button>
                    </div>
                    <p id="previewBanner" class="text-lg font-semibold text-gray-700 mb-4"></p>
                    <div class="p-4 rounded-md text-sm" id="previewCard">
                        This is a sample card reflecting your primary color choice.
                    </div>
                    <div class="mt-4 text-sm" id="previewFooterText">
                        Footer content here.
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-4 italic">
                    Note: This live preview updates elements directly within this module. It does not apply changes to the main dashboard UI, nor does it persist after page reload. For full system-wide rebranding, a backend integration would be required.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data passed from Laravel Controller
            const brandingSettings = @json($brandingSettings);
            const vendorCompanyName = @json($vendor->company_name ?? 'Your Company Rentals');

            // Get preview elements
            const previewArea = document.getElementById('previewArea');
            const previewHeader = document.getElementById('previewHeader');
            const previewLogo = document.getElementById('previewLogo');
            const previewSiteName = document.getElementById('previewSiteName');
            const previewButton = document.getElementById('previewButton');
            const previewBanner = document.getElementById('previewBanner');
            const previewCard = document.getElementById('previewCard');
            const previewFooterText = document.getElementById('previewFooterText'); // Added this ID for easier targeting

            // Create dynamic style tag for custom CSS if not already present
            let customPreviewCssTag = document.getElementById('customPreviewCss');
            if (!customPreviewCssTag) {
                customPreviewCssTag = document.createElement('style');
                customPreviewCssTag.id = 'customPreviewCss';
                document.head.appendChild(customPreviewCssTag);
            }

            // Function to apply branding settings to the preview area
            function updateLivePreview() {
                // Apply general branding
                previewLogo.src = brandingSettings.logoUrl || 'https://via.placeholder.com/80x30/E0E0E0/6C757D?text=Logo';
                previewSiteName.textContent = vendorCompanyName; // Using vendor's company name
                previewButton.style.backgroundColor = brandingSettings.primaryColor;
                previewHeader.style.borderColor = brandingSettings.primaryColor;

                // Apply colors via CSS variables for flexibility
                previewArea.style.setProperty('--primary-color', brandingSettings.primaryColor);
                previewArea.style.setProperty('--secondary-color', brandingSettings.secondaryColor);

                // Example: apply primary color to button, secondary to card
                previewButton.style.backgroundColor = 'var(--primary-color, #EA3A26)';
                previewCard.style.backgroundColor = 'var(--secondary-color, #FF8600)20'; // 20 for opacity
                previewCard.style.color = 'var(--secondary-color, #FF8600)'; // Text color on card

                // Apply customer portal text
                previewBanner.textContent = brandingSettings.portalBannerText || "Welcome to Your Company's Rental Portal!";

                // Apply custom CSS
                customPreviewCssTag.innerHTML = brandingSettings.customCss || '';

                // Custom JS is conceptual; running it here could be risky.
                // For a real preview, you'd embed it in an iframe or use a sandbox.
                // For now, we'll just log its presence.
                if (brandingSettings.customJs) {
                    console.log("Custom JavaScript is present. (Would be executed in a live environment)");
                }
            }

            // Initial render on page load
            updateLivePreview();
        });
    </script>
</body>
</html>