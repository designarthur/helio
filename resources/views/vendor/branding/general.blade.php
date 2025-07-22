<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - General Branding</title>
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
            <a href="{{ route('branding.show') }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">General Branding</a>
            <a href="{{ route('branding.show', ['tab' => 'domain']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Custom Domain</a>
            <a href="{{ route('branding.show', ['tab' => 'email']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Email Templates</a>
            <a href="{{ route('branding.show', ['tab' => 'portal']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Customer Portal</a>
            <a href="{{ route('branding.show', ['tab' => 'preview']) }}" class="branding-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Live Preview</a>
        </div>

        <div id="branding-tab-general" class="branding-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">General Branding Settings</h3>
            <form action="{{ route('branding.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md space-y-6">
                @csrf
                @method('POST') {{-- Use POST method as per route, and handle files with enctype --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="logoUpload" class="block text-sm font-medium text-gray-700 mb-1">Company Logo:</label>
                        <input type="file" id="logoUpload" name="logoUpload" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-chili-red file:text-white hover:file:bg-tangelo"/>
                        <p class="text-xs text-gray-500 mt-1">Upload your primary logo (PNG, JPG, SVG recommended).</p>
                        <div class="mt-3 p-2 border border-gray-200 rounded-md bg-gray-50 flex items-center justify-center h-24">
                            <img id="logoPreview" src="{{ $brandingSettings['logoUrl'] ?? 'https://via.placeholder.com/100x40/E0E0E0/6C757D?text=Logo+Preview' }}" alt="Logo Preview" class="max-h-full max-w-full object-contain">
                        </div>
                        <input type="hidden" name="logo_cleared" id="logoCleared" value="0">
                        <button type="button" onclick="clearImage('logoPreview', 'logoUpload', 'logoCleared', '{{ 'https://via.placeholder.com/100x40/E0E0E0/6C757D?text=Logo+Preview' }}')" class="text-red-500 hover:text-red-700 text-sm mt-2">Clear Logo</button>
                        @error('logoUpload')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="faviconUpload" class="block text-sm font-medium text-gray-700 mb-1">Favicon:</label>
                        <input type="file" id="faviconUpload" name="faviconUpload" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-chili-red file:text-white hover:file:bg-tangelo"/>
                        <p class="text-xs text-gray-500 mt-1">Upload your favicon (e.g., .ico, PNG 32x32).</p>
                        <div class="mt-3 p-2 border border-gray-200 rounded-md bg-gray-50 flex items-center justify-center h-10 w-10">
                            <img id="faviconPreview" src="{{ $brandingSettings['faviconUrl'] ?? 'https://via.placeholder.com/32x32/E0E0E0/6C757D?text=Fav' }}" alt="Favicon Preview" class="max-h-full max-w-full object-contain">
                        </div>
                        <input type="hidden" name="favicon_cleared" id="faviconCleared" value="0">
                        <button type="button" onclick="clearImage('faviconPreview', 'faviconUpload', 'faviconCleared', '{{ 'https://via.placeholder.com/32x32/E0E0E0/6C757D?text=Fav' }}')" class="text-red-500 hover:text-red-700 text-sm mt-2">Clear Favicon</button>
                        @error('faviconUpload')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="primaryColor" class="block text-sm font-medium text-gray-700 mb-1">Primary Brand Color:</label>
                        <input type="color" id="primaryColor" name="primaryColor" value="{{ old('primaryColor', $brandingSettings['primaryColor'] ?? '#EA3A26') }}" class="w-full h-10 border border-gray-300 rounded-md cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1">Used for main accents, buttons.</p>
                        @error('primaryColor')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="secondaryColor" class="block text-sm font-medium text-gray-700 mb-1">Secondary Brand Color:</label>
                        <input type="color" id="secondaryColor" name="secondaryColor" value="{{ old('secondaryColor', $brandingSettings['secondaryColor'] ?? '#FF8600') }}" class="w-full h-10 border border-gray-300 rounded-md cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1">Used for secondary elements, highlights.</p>
                        @error('secondaryColor')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to preview image immediately after selection
            function previewImage(input, previewId) {
                const preview = document.getElementById(previewId);
                const file = input.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }

            // Function to clear image and reset hidden input
            window.clearImage = function(previewId, inputId, clearedHiddenInputId, defaultSrc) {
                const preview = document.getElementById(previewId);
                const input = document.getElementById(inputId);
                const clearedHiddenInput = document.getElementById(clearedHiddenInputId);

                preview.src = defaultSrc; // Reset to default placeholder
                input.value = ''; // Clear the file input
                clearedHiddenInput.value = '1'; // Set hidden input to indicate clearance
            };

            // Attach event listeners to file inputs
            document.getElementById('logoUpload').addEventListener('change', function() {
                previewImage(this, 'logoPreview');
                document.getElementById('logoCleared').value = '0'; // If new file selected, it's not cleared
            });
            document.getElementById('faviconUpload').addEventListener('change', function() {
                previewImage(this, 'faviconPreview');
                document.getElementById('faviconCleared').value = '0'; // If new file selected, it's not cleared
            });
        });
    </script>
</body>
</html>