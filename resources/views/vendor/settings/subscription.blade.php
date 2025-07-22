<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Subscription Settings</title>
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
            <a href="{{ route('settings.show', ['tab' => 'profile']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">My Company Profile</a>
            <a href="{{ route('settings.show', ['tab' => 'notifications']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Notification Settings</a>
            <a href="{{ route('settings.show', ['tab' => 'users']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Manage Users</a>
            <a href="{{ route('settings.show', ['tab' => 'integrations']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Integrations (Conceptual)</a>
            <a href="{{ route('settings.show', ['tab' => 'subscription']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Subscription (Conceptual)</a>
        </div>

        <div id="settings-tab-subscription" class="settings-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Subscription & Billing</h3>
            <div class="bg-white p-6 rounded-lg shadow-md space-y-6">
                <p class="text-gray-700">Manage your Helly subscription plan and billing information.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold text-lg text-gray-800 mb-2">Current Plan:</h4>
                        <p class="text-gray-600"><strong>Professional Plan</strong></p>
                        <p class="text-gray-600 text-sm">Monthly Cost: $199/month</p>
                        <p class="text-gray-600 text-sm">Features: Unlimited Equipment, Unlimited Bookings, 5 Users, Standard Analytics</p>
                        <button type="button" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" onclick="alert('Simulating change plan options...')">Change Plan</button>
                    </div>
                    <div>
                        <h4 class="font-semibold text-lg text-gray-800 mb-2">Next Billing Date:</h4>
                        <p class="text-gray-600">August 22, 2025</p>
                        <h4 class="font-semibold text-lg text-gray-800 mt-4 mb-2">Payment Method:</h4>
                        <p class="text-gray-600">Visa ending in **** 1234</p>
                        <button type="button" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700" onclick="alert('Simulating update payment method...')">Update Payment Method</button>
                    </div>
                </div>
                <h4 class="font-semibold text-lg text-gray-800 mb-2 mt-6 border-b pb-2">Billing History (Conceptual)</h4>
                <p class="text-gray-600">
                    A list of all past invoices from Helly for your subscription, with options to download.
                </p>
                <button type="button" class="px-6 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700" onclick="alert('Simulating viewing billing history...')">View Billing History</button>
            </div>
        </div>
    </div>
</body>
</html>