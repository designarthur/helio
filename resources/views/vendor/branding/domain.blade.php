@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Branding Settings: Domain & DNS</h2>

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
        <span class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="domain">Domain & DNS</span>
        <a href="{{ route('branding.email') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="email">Email Templates</a>
        <a href="{{ route('branding.portal') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="portal">Customer Portal</a>
        <a href="{{ route('branding.preview') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="preview">Preview</a>
    </div>

    <div id="branding-tab-content-domain" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Custom Domain Setup</h3>
        <p class="text-gray-600 mb-6">
            Point your own domain (e.g., `dashboard.yourcompany.com`) to your vendor portal.
            You'll need to add a CNAME record in your DNS settings.
        </p>

        <form action="{{ route('branding.updateDomain') }}" method="POST">
            @csrf
            @method('PUT') {{-- Or PATCH, depending on your route definition --}}

            <div class="mb-6">
                <label for="custom_domain" class="block text-sm font-medium text-gray-700 mb-1">Custom Domain</label>
                <input type="text" name="custom_domain" id="custom_domain"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('custom_domain', $brandingSettings->custom_domain ?? '') }}"
                       placeholder="e.g., dashboard.yourcompany.com">
                <p class="mt-2 text-xs text-gray-500">
                    Enter the subdomain you wish to use for your vendor dashboard.
                </p>
            </div>

            <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h4 class="text-lg font-semibold text-blue-800 mb-2">DNS Record Instructions:</h4>
                <p class="text-blue-700 mb-2">
                    Please add the following **CNAME record** to your domain's DNS settings:
                </p>
                <div class="bg-blue-100 p-3 rounded-md font-mono text-sm text-blue-900 overflow-x-auto">
                    <p><strong>Type:</strong> CNAME</p>
                    <p><strong>Host/Name:</strong> {{ $brandingSettings->custom_domain ? Str::before($brandingSettings->custom_domain, '.') : 'your-subdomain' }}</p>
                    <p><strong>Value/Target:</strong> {{ config('app.app_domain') }}</p>
                    <p class="mt-2 text-xs text-blue-700">
                        (Replace `your-subdomain` with the value you entered above if `custom_domain` is empty)
                    </p>
                </div>
                <p class="mt-3 text-blue-700 text-sm">
                    DNS changes can take up to 24-48 hours to propagate. Your custom domain will become active once the DNS record is correctly set up and propagated.
                </p>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    Save Domain Settings
                </button>
            </div>
        </form>

        <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-10">SSL Certificate Status</h3>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <p class="text-gray-700 mb-2">
                SSL certificates are automatically provisioned and managed for your custom domain once it is correctly pointed to our servers.
            </p>
            <p class="text-sm">
                Current Status:
                @if($brandingSettings->ssl_status ?? false === 'active')
                    <span class="text-green-600 font-semibold">Active <i class="fas fa-check-circle"></i></span>
                @elseif($brandingSettings->ssl_status ?? false === 'pending')
                    <span class="text-orange-600 font-semibold">Pending <i class="fas fa-hourglass-half"></i></span>
                @else
                    <span class="text-red-600 font-semibold">Not Configured <i class="fas fa-times-circle"></i></span>
                @endif
            </p>
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
            // Set 'domain' as active
            document.querySelector('.branding-tab[data-tab="domain"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.branding-tab[data-tab="domain"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection