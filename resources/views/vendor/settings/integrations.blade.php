@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Integrations Settings</h2>

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
        <a href="{{ route('settings.notifications') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="notifications">Notifications</a>
        <span class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="integrations">Integrations</span>
        <a href="{{ route('settings.users.index') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="users">Users & Roles</a>
        <a href="{{ route('settings.subscription') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="subscription">Subscription</a>
    </div>

    <div id="settings-tab-content-integrations" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('vendor.integrations.update') }}" method="POST">
            @csrf
            @method('PUT') {{-- Or PATCH, depending on your route definition --}}

            <div class="space-y-8">
                {{-- Payment Gateway Integration (Stripe Example) --}}
                <fieldset>
                    <legend class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fab fa-stripe text-purple-600 text-2xl"></i> Stripe Integration
                    </legend>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="stripe_enabled" class="text-gray-700">Enable Stripe Payments</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="stripe_enabled" id="stripe_enabled" class="sr-only peer"
                                    {{ old('stripe_enabled', $integrationSettings->stripe_enabled ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="stripe_publishable_key" class="block text-sm font-medium text-gray-700 mb-1">Stripe Publishable Key</label>
                            <input type="text" name="stripe_publishable_key" id="stripe_publishable_key"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                                value="{{ old('stripe_publishable_key', $integrationSettings->stripe_publishable_key ?? '') }}">
                        </div>
                        <div class="mb-4">
                            <label for="stripe_secret_key" class="block text-sm font-medium text-gray-700 mb-1">Stripe Secret Key</label>
                            <input type="password" name="stripe_secret_key" id="stripe_secret_key"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                                value="{{ old('stripe_secret_key', $integrationSettings->stripe_secret_key ?? '') }}">
                        </div>
                    </div>
                </fieldset>

                {{-- Mapping/Geolocation Service Integration (Google Maps Example) --}}
                <fieldset class="pt-8 border-t border-gray-200">
                    <legend class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-green-600 text-2xl"></i> Google Maps Integration
                    </legend>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="google_maps_enabled" class="text-gray-700">Enable Google Maps</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="google_maps_enabled" id="google_maps_enabled" class="sr-only peer"
                                    {{ old('google_maps_enabled', $integrationSettings->google_maps_enabled ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="google_maps_api_key" class="block text-sm font-medium text-gray-700 mb-1">Google Maps API Key</label>
                            <input type="text" name="google_maps_api_key" id="google_maps_api_key"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                                value="{{ old('google_maps_api_key', $integrationSettings->google_maps_api_key ?? '') }}">
                        </div>
                    </div>
                </fieldset>

                {{-- SMS Gateway Integration (Twilio Example) --}}
                <fieldset class="pt-8 border-t border-gray-200">
                    <legend class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-sms text-blue-500 text-2xl"></i> Twilio SMS
                    </legend>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="twilio_enabled" class="text-gray-700">Enable Twilio SMS</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="twilio_enabled" id="twilio_enabled" class="sr-only peer"
                                    {{ old('twilio_enabled', $integrationSettings->twilio_enabled ?? false) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-chili-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-chili-red"></div>
                            </label>
                        </div>
                        <div class="mb-4">
                            <label for="twilio_sid" class="block text-sm font-medium text-gray-700 mb-1">Twilio Account SID</label>
                            <input type="text" name="twilio_sid" id="twilio_sid"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                                value="{{ old('twilio_sid', $integrationSettings->twilio_sid ?? '') }}">
                        </div>
                        <div class="mb-4">
                            <label for="twilio_auth_token" class="block text-sm font-medium text-gray-700 mb-1">Twilio Auth Token</label>
                            <input type="password" name="twilio_auth_token" id="twilio_auth_token"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                                value="{{ old('twilio_auth_token', $integrationSettings->twilio_auth_token ?? '') }}">
                        </div>
                        <div class="mb-4">
                            <label for="twilio_phone_number" class="block text-sm font-medium text-gray-700 mb-1">Twilio Phone Number</label>
                            <input type="text" name="twilio_phone_number" id="twilio_phone_number"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                                value="{{ old('twilio_phone_number', $integrationSettings->twilio_phone_number ?? '') }}">
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    Save Integrations
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
            // Set 'integrations' as active
            document.querySelector('.settings-tab[data-tab="integrations"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.settings-tab[data-tab="integrations"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection