@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Branding Settings: General</h2>

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
        <span class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="general">General</span>
        <a href="{{ route('branding.domain') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="domain">Domain & DNS</a>
        <a href="{{ route('branding.email') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="email">Email Templates</a>
        <a href="{{ route('branding.portal') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="portal">Customer Portal</a>
        <a href="{{ route('branding.preview') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="preview">Preview</a>
    </div>

    <div id="branding-tab-content-general" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('branding.updateGeneral') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Or PATCH, depending on your route definition --}}

            <div class="mb-6">
                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                <input type="text" name="company_name" id="company_name"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('company_name', $brandingSettings->company_name ?? '') }}">
            </div>

            <div class="mb-6">
                <label for="company_logo" class="block text-sm font-medium text-gray-700 mb-1">Company Logo</label>
                <input type="file" name="company_logo" id="company_logo"
                       class="mt-1 block w-full text-sm text-gray-500
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-md file:border-0
                       file:text-sm file:font-semibold
                       file:bg-chili-red file:text-white
                       hover:file:bg-tangelo">
                @if (isset($brandingSettings->company_logo_url) && $brandingSettings->company_logo_url)
                    <p class="mt-2 text-sm text-gray-500">Current Logo:</p>
                    <img src="{{ $brandingSettings->company_logo_url }}" alt="Company Logo" class="mt-2 h-20 w-auto object-contain">
                @endif
                <p class="mt-2 text-xs text-gray-500">Recommended size: 200x50px, PNG or JPG.</p>
            </div>

            <div class="mb-6">
                <label for="favicon" class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                <input type="file" name="favicon" id="favicon"
                       class="mt-1 block w-full text-sm text-gray-500
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-md file:border-0
                       file:text-sm file:font-semibold
                       file:bg-chili-red file:text-white
                       hover:file:bg-tangelo">
                @if (isset($brandingSettings->favicon_url) && $brandingSettings->favicon_url)
                    <p class="mt-2 text-sm text-gray-500">Current Favicon:</p>
                    <img src="{{ $brandingSettings->favicon_url }}" alt="Favicon" class="mt-2 h-10 w-auto object-contain">
                @endif
                <p class="mt-2 text-xs text-gray-500">Recommended size: 32x32px, ICO or PNG.</p>
            </div>

            <div class="mb-6">
                <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                <input type="color" name="primary_color" id="primary_color"
                       class="mt-1 block w-24 h-10 border border-gray-300 rounded-md shadow-sm cursor-pointer"
                       value="{{ old('primary_color', $brandingSettings->primary_color ?? '#EA3A26') }}">
                <p class="mt-2 text-xs text-gray-500">This color will be used for primary accents, buttons, etc.</p>
            </div>

            <div class="mb-6">
                <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                <input type="color" name="secondary_color" id="secondary_color"
                       class="mt-1 block w-24 h-10 border border-gray-300 rounded-md shadow-sm cursor-pointer"
                       value="{{ old('secondary_color', $brandingSettings->secondary_color ?? '#FF8600') }}">
                <p class="mt-2 text-xs text-gray-500">This color will be used for secondary accents, hover states, etc.</p>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    Save Changes
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
            // Set 'general' as active
            document.querySelector('.branding-tab[data-tab="general"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.branding-tab[data-tab="general"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection