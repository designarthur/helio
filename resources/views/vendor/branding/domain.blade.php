<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Custom Domain</title>
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
            <a href="{{ route('branding.show', ['tab' => 'domain']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Custom Domain</a>
            <a href="{{ route('branding.show', ['tab' => 'email']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Email Templates</a>
            <a href="{{ route('branding.show', ['tab' => 'portal']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Customer Portal</a>
            <a href="{{ route('branding.show', ['tab' => 'preview']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Live Preview</a>
        </div>

        <div id="branding-tab-domain" class="branding-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Custom Domain & URL Masking (Conceptual)</h3>
            <form action="{{ route('branding.update') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-6">
                @csrf
                @method('POST') {{-- Use POST method as per route --}}

                <div>
                    <label for="customDomain" class="block text-sm font-medium text-gray-700 mb-1">Your Custom Domain:</label>
                    <input type="text" id="customDomain" name="customDomain" placeholder="rentals.yourcompany.com"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('customDomain', $brandingSettings['customDomain'] ?? '') }}">
                    <p class="text-xs text-gray-500 mt-1">This allows your customers to access the platform via your own domain name, completely masking the Helly URL.</p>
                    @error('customDomain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-md text-sm text-blue-800">
                    <h4 class="font-semibold mb-2">Setup Instructions:</h4>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Create a CNAME record in your domain registrar (e.g., GoDaddy, Cloudflare) pointing <code>rentals.yourcompany.com</code> to <code>your-vendor-id.helly.com</code>.</li>
                        <li>Verify DNS settings within the platform.</li>
                        <li>Our system will automatically provision and manage SSL certificates for your custom domain.</li>
                    </ul>
                    <p class="mt-3"><strong>Status:</strong> <span class="font-bold text-gray-700" id="domainStatus">{{ $brandingSettings['customDomain'] ? 'Configured (Needs DNS Verification)' : 'Not Configured' }}</span>
                        <button type="button" onclick="alert('Simulating domain verification process...')" class="ml-2 text-blue-600 hover:underline">Verify DNS</button></p>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>