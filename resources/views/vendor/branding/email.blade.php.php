<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Email Branding</title>
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
            <a href="{{ route('branding.show', ['tab' => 'email']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Email Templates</a>
            <a href="{{ route('branding.show', ['tab' => 'portal']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Customer Portal</a>
            <a href="{{ route('branding.show', ['tab' => 'preview']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Live Preview</a>
        </div>

        <div id="branding-tab-email" class="branding-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Email & Notification Branding</h3>
            <form action="{{ route('branding.update') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-6">
                @csrf
                @method('POST') {{-- Use POST method as per route --}}

                <div>
                    <label for="senderName" class="block text-sm font-medium text-gray-700 mb-1">Sender Name (for customer emails):</label>
                    <input type="text" id="senderName" name="senderName" placeholder="Your Company Rentals"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('senderName', $brandingSettings['senderName'] ?? '') }}">
                    <p class="text-xs text-gray-500 mt-1">This name will appear as the sender for all automated emails to your customers.</p>
                    @error('senderName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="replyToEmail" class="block text-sm font-medium text-gray-700 mb-1">Reply-To Email Address:</label>
                    <input type="email" id="replyToEmail" name="replyToEmail" placeholder="support@yourcompany.com"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('replyToEmail', $brandingSettings['replyToEmail'] ?? '') }}">
                    <p class="text-xs text-gray-500 mt-1">Customer replies to automated emails will be sent to this address.</p>
                    @error('replyToEmail')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="bg-gray-50 p-4 rounded-md text-sm text-gray-700">
                    <h4 class="font-semibold mb-2">Email Template Customization (Conceptual):</h4>
                    <p>In a full system, you would have access to customize the content and layout of various automated emails (Booking Confirmations, Invoice Reminders, Driver ETA Notifications) using a visual editor or HTML templates. You could add your logo, specific messaging, and social media links directly into the email body.</p>
                    <button type="button" onclick="alert('Simulating email template editor...')" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Customize Templates</button>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>