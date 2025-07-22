<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Company Profile Settings</title>
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
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Settings</h2>

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
        @if (session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Info:</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
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
            <a href="{{ route('settings.show', ['tab' => 'profile']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">My Company Profile</a>
            <a href="{{ route('settings.show', ['tab' => 'notifications']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Notification Settings</a>
            <a href="{{ route('settings.show', ['tab' => 'users']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Manage Users</a>
            <a href="{{ route('settings.show', ['tab' => 'integrations']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Integrations (Conceptual)</a>
            <a href="{{ route('settings.show', ['tab' => 'subscription']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Subscription (Conceptual)</a>
        </div>

        <div id="settings-tab-profile" class="settings-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">My Company Profile</h3>
            <form action="{{ route('settings.updateProfile') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-6">
                @csrf
                @method('POST') {{-- Use POST method as per route for simplicity here --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Business Name:</label>
                        <input type="text" id="company_name" name="company_name" placeholder="Your Company Rentals"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                               value="{{ old('company_name', $vendorSettings['company_name'] ?? '') }}">
                        @error('company_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Contact Email:</label>
                        <input type="email" id="contact_email" name="contact_email" placeholder="info@yourcompany.com"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                               value="{{ old('contact_email', $vendorSettings['email'] ?? '') }}">
                        @error('contact_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone:</label>
                        <input type="tel" id="contact_phone" name="contact_phone" placeholder="(123) 456-7890"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                               value="{{ old('contact_phone', $vendorSettings['phone'] ?? '') }}">
                        @error('contact_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="primary_address" class="block text-sm font-medium text-gray-700 mb-1">Primary Business Address:</label>
                        <input type="text" id="primary_address" name="primary_address" placeholder="123 Corporate Blvd, City, State Zip"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                               value="{{ old('primary_address', $vendorSettings['primary_address'] ?? '') }}">
                        @error('primary_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="operating_hours" class="block text-sm font-medium text-gray-700 mb-1">Operating Hours:</label>
                        <input type="text" id="operating_hours" name="operating_hours" placeholder="Mon-Fri: 8AM-5PM, Sat: 9AM-1PM"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                               value="{{ old('operating_hours', $vendorSettings['operating_hours'] ?? '') }}">
                        @error('operating_hours')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="service_areas" class="block text-sm font-medium text-gray-700 mb-1">Service Areas (Zip codes/Cities, comma-separated):</label>
                        <textarea id="service_areas" name="service_areas" rows="2" placeholder="12345, 67890, Anytown, Othercity"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                            {{ old('service_areas', is_array($vendorSettings['service_areas'] ?? null) ? implode(', ', $vendorSettings['service_areas']) : ($vendorSettings['service_areas'] ?? '')) }}
                        </textarea>
                        @error('service_areas')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">Save Profile</button>
            </form>
        </div>
    </div>
</body>
</html>