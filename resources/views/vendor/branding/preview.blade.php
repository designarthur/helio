@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Branding Settings: Preview</h2>

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
        {{-- Branding Tabs --}}
        <a href="{{ route('branding.show') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="general">General</a>
        <a href="{{ route('branding.domain') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="domain">Domain & DNS</a>
        <a href="{{ route('branding.email') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="email">Email Templates</a>
        <a href="{{ route('branding.portal') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="portal">Customer Portal</a>
        <span class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="preview">Preview</span>
    </div>

    <div id="branding-tab-content-preview" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Live Branding Preview</h3>
        <p class="text-gray-600 mb-6">
            This section shows a live preview of how your current branding settings (Logo, Colors, etc.) will appear across different parts of your system.
        </p>

        <div class="space-y-8">
            {{-- Preview: Header/Dashboard --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 border-b flex items-center justify-between" style="background-color: {{ $brandingSettings->primary_color ?? '#EA3A26' }}; color: white;">
                    <div class="flex items-center">
                        @if (isset($brandingSettings->company_logo_url) && $brandingSettings->company_logo_url)
                            <img src="{{ $brandingSettings->company_logo_url }}" alt="Company Logo" class="h-8 mr-3" style="filter: brightness(0) invert(1);">
                        @else
                            <h1 class="text-2xl font-bold">Helio</h1>
                        @endif
                        <span class="ml-2 text-sm">Vendor Dashboard</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-bell text-xl mx-4"></i>
                        <span class="font-semibold text-white hidden md:block">Ahmad Khan</span>
                    </div>
                </div>
                <div class="p-4">
                    <h4 class="text-lg font-semibold mb-2">Dashboard Elements</h4>
                    <button class="px-4 py-2 rounded-md font-semibold" style="background-color: {{ $brandingSettings->primary_color ?? '#EA3A26' }}; color: white;">Primary Button</button>
                    <button class="px-4 py-2 rounded-md font-semibold ml-3" style="background-color: {{ $brandingSettings->secondary_color ?? '#FF8600' }}; color: white;">Secondary Button</button>
                    <p class="mt-4 text-gray-700">This is some example text to show the default text color.</p>
                </div>
            </div>

            {{-- Preview: Customer Portal Login Page (Simplified) --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="p-6 text-center" style="background-color: {{ $brandingSettings->portal_background_color ?? '#f3f4f6' }}; color: {{ $brandingSettings->portal_text_color ?? '#1f2937' }};">
                    @if (isset($brandingSettings->company_logo_url) && $brandingSettings->company_logo_url)
                        <img src="{{ $brandingSettings->company_logo_url }}" alt="Company Logo" class="mx-auto h-12 mb-4">
                    @endif
                    <h4 class="text-2xl font-bold mb-3" style="color: {{ $brandingSettings->portal_text_color ?? '#1f2937' }};">{{ $brandingSettings->portal_hero_text ?? 'Welcome to Our Customer Portal!' }}</h4>
                    <p class="mb-6">Log in to manage your services.</p>
                    <button class="px-6 py-3 rounded-md font-semibold" style="background-color: {{ $brandingSettings->portal_cta_button_color ?? '#EA3A26' }}; color: white;">
                        {{ $brandingSettings->portal_cta_button_text ?? 'Request a Quote' }}
                    </button>
                    <p class="mt-4 text-sm">Don't have an account? <a href="#" style="color: {{ $brandingSettings->primary_color ?? '#EA3A26' }}; text-decoration: underline;">Register here</a></p>
                </div>
            </div>

            {{-- Preview: Email Template (Simplified) --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div style="background-color: #f8f8f8; padding: 20px; text-align: center;">
                    @if (isset($brandingSettings->email_logo_url) && $brandingSettings->email_logo_url)
                        <img src="{{ $brandingSettings->email_logo_url }}" alt="Email Logo" style="max-height: 40px; margin-bottom: 15px;">
                    @else
                        <h1 style="color: #333; font-size: 24px; margin-bottom: 15px;">{{ $brandingSettings->email_header_text ?? ($brandingSettings->company_name ?? 'Your Company') }}</h1>
                    @endif
                </div>
                <div style="background-color: #ffffff; padding: 20px; font-family: sans-serif; font-size: 14px; line-height: 1.6; color: #333;">
                    <p style="margin-bottom: 15px;">Dear Customer,</p>
                    <p style="margin-bottom: 15px;">
                        This is a preview of your customized email. The colors and text reflect your branding settings.
                    </p>
                    <a href="#" style="display: inline-block; padding: 10px 20px; background-color: {{ $brandingSettings->email_accent_color ?? '#EA3A26' }}; color: #ffffff; text-decoration: none; border-radius: 5px;">
                        Click Here
                    </a>
                    <p style="margin-top: 20px; font-size: 12px; color: #777;">
                        {!! nl2br(e($brandingSettings->email_footer_text ?? '© 2024 Your Company. All rights reserved.')) !!}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set active branding tab
        document.addEventListener('DOMContentLoaded', () => {
            const brandingTabs = document.querySelectorAll('.branding-tab');
            brandingTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Set 'preview' as active
            document.querySelector('.branding-tab[data-tab="preview"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.branding-tab[data-tab="preview"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection