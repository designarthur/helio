<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Dispatching Map View</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <style>
        /* Existing custom styles from your HTML, if any */
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen">

    {{-- Main content wrapper - for now, this will be a full page, later part of a layout --}}
    <div class="p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Dispatching & Route Optimization</h2>

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
            <a href="{{ route('dispatching.show', ['tab' => 'board']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Driver Board</a>
            <a href="{{ route('dispatching.show', ['tab' => 'list']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Job List</a>
            <a href="{{ route('dispatching.show', ['tab' => 'map']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Map View (Conceptual)</a>
            <a href="{{ route('dispatching.show', ['tab' => 'schedule']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Driver Schedule (Conceptual)</a>
        </div>

        <div id="dispatch-view-map" class="dispatch-content-view">
            <div class="bg-white p-8 rounded-lg shadow-md text-center h-full flex flex-col items-center justify-center">
                <i class="fas fa-map-marked-alt text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Map View - Intelligent Route Optimization & Real-time Tracking</h3>
                <p class="text-gray-600 max-w-2xl mx-auto mb-6">
                    This is the **live command center** of your dispatch operations. In a real system, powered by Google Maps Platform, it would offer:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto text-left">
                    <div class="p-4 bg-gray-50 rounded-md shadow-sm">
                        <h4 class="font-semibold text-lg text-blue-700 mb-2">Real-time Tracking & Visibility</h4>
                        <ul class="list-disc list-inside ml-4 text-sm space-y-1">
                            <li><strong>Live Map:</strong> Shows all active drivers, their current locations, and assigned routes.</li>
                            <li><strong>Driver & Job Status:</strong> Color-coded job pins (Red: Unassigned, Yellow: Pending, Blue: En Route, Green: Completed) and driver icons indicating status (On Route, Idle).</li>
                            <li><strong>Real-time Updates:</strong> Driver updates (e.g., "Arrived," "Job Complete") reflected instantly on the map.</li>
                            <li><strong>ETA & Progress:</strong> Track estimated time of arrival to next stop and overall route progress.</li>
                        </ul>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-md shadow-sm">
                        <h4 class="font-semibold text-lg text-blue-700 mb-2">Intelligent Route Optimization</h4>
                        <ul class="list-disc list-inside ml-4 text-sm space-y-1">
                            <li><strong>Multi-stop & Multi-driver Optimization:</strong> Automatically generates the most efficient routes.</li>
                            <li><strong>Constraint Consideration:</strong> Accounts for time windows, vehicle capacity, driver skills, traffic (real-time & historical), and road restrictions.</li>
                            <li><strong>Dynamic Adjustments:</strong> Ability to re-optimize routes on the fly for last-minute changes, new orders, or delays.</li>
                            <li><strong>Geofencing:</strong> Define virtual boundaries for arrival/departure alerts and time tracking at locations.</li>
                            <li><strong>Drag-and-and-Drop:</strong> Visually reassign jobs by dragging pins to different routes (in a truly interactive version).</li>
                        </ul>
                    </div>
                </div>
                <img src="https://via.placeholder.com/800x450/E0E0E0/6C757D?text=Placeholder+Map+View" alt="Map View Placeholder" class="mt-8 rounded-lg shadow-lg border border-gray-200">
                <p class="text-gray-600 text-sm mt-4">
                    (This view would be powered by direct integration with Google Maps Platform APIs - Geocoding, Distance Matrix, Routes API - requiring a backend and billing setup.)
                </p>
            </div>
        </div>
    </div>
</body>
</html>