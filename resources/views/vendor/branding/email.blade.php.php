@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Branding Settings: Email Templates</h2>

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
        <span class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="email">Email Templates</span>
        <a href="{{ route('branding.portal') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="portal">Customer Portal</a>
        <a href="{{ route('branding.preview') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="preview">Preview</a>
    </div>

    <div id="branding-tab-content-email" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Email Template Customization</h3>
        <p class="text-gray-600 mb-6">
            Customize the appearance of your automated emails sent to customers and drivers.
        </p>

        <form action="{{ route('branding.updateEmailTemplates') }}" method="POST">
            @csrf
            @method('PUT') {{-- Or PATCH, depending on your route definition --}}

            <div class="mb-6">
                <label for="email_header_text" class="block text-sm font-medium text-gray-700 mb-1">Email Header Text</label>
                <input type="text" name="email_header_text" id="email_header_text"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('email_header_text', $brandingSettings->email_header_text ?? 'Your Company Name') }}">
                <p class="mt-2 text-xs text-gray-500">
                    This text will appear at the top of your emails.
                </p>
            </div>

            <div class="mb-6">
                <label for="email_footer_text" class="block text-sm font-medium text-gray-700 mb-1">Email Footer Text</label>
                <textarea name="email_footer_text" id="email_footer_text" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                          placeholder="e.g., &copy; 2024 Your Company Name. All rights reserved.">{{ old('email_footer_text', $brandingSettings->email_footer_text ?? '') }}</textarea>
                <p class="mt-2 text-xs text-gray-500">
                    This text will appear at the bottom of your emails. HTML is supported.
                </p>
            </div>

            <div class="mb-6">
                <label for="email_accent_color" class="block text-sm font-medium text-gray-700 mb-1">Email Accent Color</label>
                <input type="color" name="email_accent_color" id="email_accent_color"
                       class="mt-1 block w-24 h-10 border border-gray-300 rounded-md shadow-sm cursor-pointer"
                       value="{{ old('email_accent_color', $brandingSettings->email_accent_color ?? '#EA3A26') }}">
                <p class="mt-2 text-xs text-gray-500">
                    This color will be used for buttons and key links within your emails.
                </p>
            </div>

            <div class="mb-6">
                <label for="email_logo" class="block text-sm font-medium text-gray-700 mb-1">Email Logo (optional)</label>
                <input type="file" name="email_logo" id="email_logo"
                       class="mt-1 block w-full text-sm text-gray-500
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-md file:border-0
                       file:text-sm file:font-semibold
                       file:bg-chili-red file:text-white
                       hover:file:bg-tangelo">
                @if (isset($brandingSettings->email_logo_url) && $brandingSettings->email_logo_url)
                    <p class="mt-2 text-sm text-gray-500">Current Email Logo:</p>
                    <img src="{{ $brandingSettings->email_logo_url }}" alt="Email Logo" class="mt-2 h-16 w-auto object-contain">
                @endif
                <p class="mt-2 text-xs text-gray-500">Recommended size: 150x40px for best display in emails.</p>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    Save Email Settings
                </button>
            </div>
        </form>

        <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-10">Email Template Preview</h3>
        <p class="text-gray-600 mb-4">
            A basic preview of how your emails might look with the current branding settings.
        </p>
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div style="background-color: #f8f8f8; padding: 20px; text-align: center;">
                @if (isset($brandingSettings->email_logo_url) && $brandingSettings->email_logo_url)
                    <img src="{{ $brandingSettings->email_logo_url }}" alt="Email Logo" style="max-height: 40px; margin-bottom: 15px;">
                @else
                    <h1 style="color: #333; font-size: 24px; margin-bottom: 15px;">{{ $brandingSettings->company_name ?? 'Your Company' }}</h1>
                @endif
            </div>
            <div style="background-color: #ffffff; padding: 20px; font-family: sans-serif; font-size: 14px; line-height: 1.6; color: #333;">
                <p style="margin-bottom: 15px;">Dear Customer,</p>
                <p style="margin-bottom: 15px;">
                    This is an example of an email notification. Your settings will influence the logo, header text, accent colors, and footer.
                </p>
                <a href="#" style="display: inline-block; padding: 10px 20px; background-color: {{ $brandingSettings->email_accent_color ?? '#EA3A26' }}; color: #ffffff; text-decoration: none; border-radius: 5px;">
                    View Details
                </a>
                <p style="margin-top: 20px; font-size: 12px; color: #777;">
                    {!! nl2br(e($brandingSettings->email_footer_text ?? '© 2024 Your Company. All rights reserved.')) !!}
                </p>
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
            // Set 'email' as active
            document.querySelector('.branding-tab[data-tab="email"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.branding-tab[data-tab="email"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection