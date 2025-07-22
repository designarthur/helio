@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Branding Settings: Customer Portal</h2>

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
        <span class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="portal">Customer Portal</span>
        <a href="{{ route('branding.preview') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="preview">Preview</a>
    </div>

    <div id="branding-tab-content-portal" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Customer Portal Appearance</h3>
        <p class="text-gray-600 mb-6">
            Customize the look and feel of the customer-facing portal where your customers can manage their bookings, invoices, and quotes.
        </p>

        <form action="{{ route('branding.updatePortal') }}" method="POST">
            @csrf
            @method('PUT') {{-- Or PATCH, depending on your route definition --}}

            <div class="mb-6">
                <label for="portal_hero_text" class="block text-sm font-medium text-gray-700 mb-1">Portal Hero Text</label>
                <input type="text" name="portal_hero_text" id="portal_hero_text"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('portal_hero_text', $brandingSettings->portal_hero_text ?? 'Welcome to Our Customer Portal!') }}">
                <p class="mt-2 text-xs text-gray-500">
                    This text will be prominently displayed on the customer portal's main page.
                </p>
            </div>

            <div class="mb-6">
                <label for="portal_background_color" class="block text-sm font-medium text-gray-700 mb-1">Portal Background Color</label>
                <input type="color" name="portal_background_color" id="portal_background_color"
                       class="mt-1 block w-24 h-10 border border-gray-300 rounded-md shadow-sm cursor-pointer"
                       value="{{ old('portal_background_color', $brandingSettings->portal_background_color ?? '#f3f4f6') }}">
                <p class="mt-2 text-xs text-gray-500">
                    Choose the main background color for the customer portal.
                </p>
            </div>

            <div class="mb-6">
                <label for="portal_text_color" class="block text-sm font-medium text-gray-700 mb-1">Portal Text Color</label>
                <input type="color" name="portal_text_color" id="portal_text_color"
                       class="mt-1 block w-24 h-10 border border-gray-300 rounded-md shadow-sm cursor-pointer"
                       value="{{ old('portal_text_color', $brandingSettings->portal_text_color ?? '#1f2937') }}">
                <p class="mt-2 text-xs text-gray-500">
                    Choose the default text color for the customer portal.
                </p>
            </div>

            <div class="mb-6">
                <label for="portal_cta_button_text" class="block text-sm font-medium text-gray-700 mb-1">Call-to-Action Button Text</label>
                <input type="text" name="portal_cta_button_text" id="portal_cta_button_text"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('portal_cta_button_text', $brandingSettings->portal_cta_button_text ?? 'Request a Quote') }}">
                <p class="mt-2 text-xs text-gray-500">
                    Text for the main call-to-action button on the portal.
                </p>
            </div>

            <div class="mb-6">
                <label for="portal_cta_button_color" class="block text-sm font-medium text-gray-700 mb-1">Call-to-Action Button Color</label>
                <input type="color" name="portal_cta_button_color" id="portal_cta_button_color"
                       class="mt-1 block w-24 h-10 border border-gray-300 rounded-md shadow-sm cursor-pointer"
                       value="{{ old('portal_cta_button_color', $brandingSettings->portal_cta_button_color ?? '#EA3A26') }}">
                <p class="mt-2 text-xs text-gray-500">
                    Background color for the main call-to-action button.
                </p>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    Save Portal Settings
                </button>
            </div>
        </form>
    </div>

    <script>
        // Set active branding tab
        document.addEventListener('DOMContentLoaded', () => {
            const brandingTabs = document.querySelectorAll('.branding-tab');
            brandingTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Set 'portal' as active
            document.querySelector('.branding-tab[data-tab="portal"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.branding-tab[data-tab="portal"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection