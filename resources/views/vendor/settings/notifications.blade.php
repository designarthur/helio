@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Notification Settings</h2>

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
        {{-- Settings Tabs --}}
        <a href="{{ route('settings.show') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="general">General</a>
        <a href="{{ route('settings.profile') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="profile">Profile</a>
        <span class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="notifications">Notifications</span>
        <a href="{{ route('settings.integrations') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="integrations">Integrations</a>
        <a href="{{ route('settings.users.index') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="users">Users & Roles</a>
        <a href="{{ route('settings.subscription') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="subscription">Subscription</a>
    </div>

    <div id="settings-tab-content-notifications" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('vendor.notifications.update') }}" method="POST">
            @csrf
            @method('PUT') {{-- Or PATCH, depending on your route definition --}}

            <div class="space-y-6">
                {{-- Email Notifications --}}
                <fieldset>
                    <legend class="text-xl font-semibold text-gray-800 mb-4">Email Notifications</legend>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="email_new_booking" class="text-gray-700">New Booking Confirmation</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_new_booking" id="email_new_booking" class="sr-only peer"
                                    {{ old('email_new_booking', $notificationSettings->email_new_booking ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <label for="email_booking_cancellation" class="text-gray-700">Booking Cancellation</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_booking_cancellation" id="email_booking_cancellation" class="sr-only peer"
                                    {{ old('email_booking_cancellation', $notificationSettings->email_booking_cancellation ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <label for="email_payment_received" class="text-gray-700">Payment Received</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_payment_received" id="email_payment_received" class="sr-only peer"
                                    {{ old('email_payment_received', $notificationSettings->email_payment_received ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <label for="email_new_quote_request" class="text-gray-700">New Quote Request</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_new_quote_request" id="email_new_quote_request" class="sr-only peer"
                                    {{ old('email_new_quote_request', $notificationSettings->email_new_quote_request ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                    </div>
                </fieldset>

                {{-- In-App Notifications --}}
                <fieldset class="pt-6 border-t border-gray-200">
                    <legend class="text-xl font-semibold text-gray-800 mb-4">In-App Notifications</legend>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="inapp_new_booking" class="text-gray-700">New Booking Confirmation</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="inapp_new_booking" id="inapp_new_booking" class="sr-only peer"
                                    {{ old('inapp_new_booking', $notificationSettings->inapp_new_booking ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <label for="inapp_booking_update" class="text-gray-700">Booking Updates</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="inapp_booking_update" id="inapp_booking_update" class="sr-only peer"
                                    {{ old('inapp_booking_update', $notificationSettings->inapp_booking_update ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <label for="inapp_system_alerts" class="text-gray-700">System Alerts & Announcements</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="inapp_system_alerts" id="inapp_system_alerts" class="sr-only peer"
                                    {{ old('inapp_system_alerts', $notificationSettings->inapp_system_alerts ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                    </div>
                </fieldset>

                {{-- SMS Notifications (if applicable) --}}
                <fieldset class="pt-6 border-t border-gray-200">
                    <legend class="text-xl font-semibold text-gray-800 mb-4">SMS Notifications (if configured)</legend>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="sms_booking_reminders" class="text-gray-700">Booking Reminders</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sms_booking_reminders" id="sms_booking_reminders" class="sr-only peer"
                                    {{ old('sms_booking_reminders', $notificationSettings->sms_booking_reminders ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <label for="sms_dispatch_updates" class="text-gray-700">Dispatch Updates</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sms_dispatch_updates" id="sms_dispatch_updates" class="sr-only peer"
                                    {{ old('sms_dispatch_updates', $notificationSettings->sms_dispatch_updates ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    Save Notification Preferences
                </button>
            </div>
        </form>
    </div>

    <script>
        // Set active settings tab
        document.addEventListener('DOMContentLoaded', () => {
            const settingsTabs = document.querySelectorAll('.settings-tab');
            settingsTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Set 'notifications' as active
            document.querySelector('.settings-tab[data-tab="notifications"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.settings-tab[data-tab="notifications"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection