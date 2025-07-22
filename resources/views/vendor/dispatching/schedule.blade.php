<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Driver Schedule</title>
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
            <a href="{{ route('dispatching.show', ['tab' => 'map']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Map View (Conceptual)</a>
            <a href="{{ route('dispatching.show', ['tab' => 'schedule']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Driver Schedule (Conceptual)</a>
        </div>

        <div id="dispatch-view-calendar" class="dispatch-content-view">
            <div class="bg-white p-8 rounded-lg shadow-md text-center h-full flex flex-col items-center justify-center">
                <i class="fas fa-calendar-day text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Driver Schedule / Timeline View (Gantt-style)</h3>
                <p class="text-gray-600 max-w-xl mx-auto mb-6">
                    This view provides a visual timeline for comprehensive scheduling and workload management:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto text-left">
                    <div class="p-4 bg-gray-50 rounded-md shadow-sm">
                        <h4 class="font-semibold text-lg text-blue-700 mb-2">Detailed Scheduling</h4>
                        <ul class="list-disc list-inside ml-4 text-sm space-y-1">
                            <li><strong>Visual Timeline:</strong> Each driver as a row, showing assigned routes, breaks, and availability across a time axis.</li>
                            <li><strong>Workload Balancing:</strong> Easily identify over- or under-utilized drivers.</li>
                            <li><strong>Conflict Detection:</strong> Spot scheduling conflicts or overlapping jobs.</li>
                        </ul>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-md shadow-sm">
                        <h4 class="font-semibold text-lg text-blue-700 mb-2">Availability & Compliance</h4>
                        <ul class="list-disc list-inside ml-4 text-sm space-y-1">
                            <li><strong>Driver Availability:</strong> Visually manage driver shifts, time-off requests, and current availability status.</li>
                            <li><strong>Hours of Service (HOS) Tracking:</strong> Monitor compliance with regulations to prevent fatigue and ensure legal operation.</li>
                        </ul>
                    </div>
                </div>
                <img src="https://via.placeholder.com/800x450/E0E0E0/6C757D?text=Placeholder+Gantt+Chart" alt="Gantt Chart Placeholder" class="mt-8 rounded-lg shadow-lg border border-gray-200">
                <p class="text-gray-600 text-sm mt-4">
                    (This feature is typically built with specialized Gantt chart libraries or robust frontend frameworks for complex interactive timelines.)
                </p>
            </div>
        </div>
    </div>
</body>
</html>