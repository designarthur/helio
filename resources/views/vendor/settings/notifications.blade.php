<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Notification Settings</title>
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
            <a href="{{ route('settings.show', ['tab' => 'notifications']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Notification Settings</a>
            <a href="{{ route('settings.show', ['tab' => 'users']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Manage Users</a>
            <a href="{{ route('settings.show', ['tab' => 'integrations']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Integrations (Conceptual)</a>
            <a href="{{ route('settings.show', ['tab' => 'subscription']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Subscription (Conceptual)</a>
        </div>

        <div id="settings-tab-notifications" class="settings-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Notification Settings</h3>
            <form action="{{ route('settings.updateNotifications') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-6">
                @csrf
                @method('POST') {{-- Use POST method as per route --}}

                <p class="text-gray-700">Configure which automated notifications you receive and how.</p>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="notifyNewBooking" name="notifyNewBooking" value="1" {{ old('notifyNewBooking', $vendorSettings['notifyNewBooking'] ?? false) ? 'checked' : '' }} class="h-4 w-4 text-chili-red focus:ring-chili-red border-gray-300 rounded">
                        <label for="notifyNewBooking" class="ml-2 block text-sm text-gray-900">Email me on New Booking Request</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="notifyPaymentReceived" name="notifyPaymentReceived" value="1" {{ old('notifyPaymentReceived', $vendorSettings['notifyPaymentReceived'] ?? false) ? 'checked' : '' }} class="h-4 w-4 text-chili-red focus:ring-chili-red border-gray-300 rounded">
                        <label for="notifyPaymentReceived" class="ml-2 block text-sm text-gray-900">Email me when Payment is Received</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="notifyOverdueInvoice" name="notifyOverdueInvoice" value="1" {{ old('notifyOverdueInvoice', $vendorSettings['notifyOverdueInvoice'] ?? false) ? 'checked' : '' }} class="h-4 w-4 text-chili-red focus:ring-chili-red border-gray-300 rounded">
                        <label for="notifyOverdueInvoice" class="ml-2 block text-sm text-gray-900">Email me for Overdue Invoices</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="notifyDriverIssues" name="notifyDriverIssues" value="1" {{ old('notifyDriverIssues', $vendorSettings['notifyDriverIssues'] ?? false) ? 'checked' : '' }} class="h-4 w-4 text-chili-red focus:ring-chili-red border-gray-300 rounded">
                        <label for="notifyDriverIssues" class="ml-2 block text-sm text-gray-900">Alert me on Driver Issues (e.g., delays, breakdowns)</label>
                    </div>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">Save Settings</button>
            </form>
        </div>
    </div>
</body>
</html>