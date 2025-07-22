<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Integration Settings</title>
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
            <a href="{{ route('settings.show', ['tab' => 'integrations']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Integrations (Conceptual)</a>
            <a href="{{ route('settings.show', ['tab' => 'subscription']) }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Subscription (Conceptual)</a>
        </div>

        <div id="settings-tab-integrations" class="settings-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Integrations (Conceptual)</h3>
            <form action="{{ route('settings.updateIntegrations') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-6">
                @csrf
                @method('POST') {{-- Use POST method as per route --}}

                <p class="text-gray-700">Connect Helly with your other essential business tools.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 rounded-md shadow-sm">
                        <h4 class="font-semibold text-lg text-blue-700 mb-2">SMS Gateway</h4>
                        <p class="text-gray-600 text-sm mb-3">Send automated SMS notifications to customers and drivers.</p>
                        <label for="smsApiKey" class="block text-sm font-medium text-gray-700 mb-1">SMS API Key:</label>
                        <input type="text" id="smsApiKey" name="smsApiKey" placeholder="sk_live_XXXXXXXXXXXXXXXXXXXX"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                               value="{{ old('smsApiKey', $vendorSettings['smsApiKey'] ?? '') }}">
                        @error('smsApiKey')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <button type="button" onclick="alert('Simulating SMS Gateway integration setup...')" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Connect SMS</button>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-md shadow-sm">
                        <h4 class="font-semibold text-lg text-blue-700 mb-2">Customer Support Chat</h4>
                        <p class="text-gray-600 text-sm mb-3">Integrate your live chat widget onto the customer portal.</p>
                        <label for="chatCode" class="block text-sm font-medium text-gray-700 mb-1">Chat Widget Code:</label>
                        <textarea id="chatCode" name="chatCode" rows="2" placeholder=""
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red font-mono text-sm resize-y">
                            {{ old('chatCode', $vendorSettings['chatCode'] ?? '') }}
                        </textarea>
                        @error('chatCode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <button type="button" onclick="alert('Simulating Chat Integration setup...')" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Chat</button>
                    </div>
                </div>
                <p class="text-gray-600 italic text-sm mt-6">More integrations (e.g., marketing automation, CRM, advanced analytics) would be available here.</p>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>