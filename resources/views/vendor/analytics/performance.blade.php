<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Analytics Performance</title>
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
    <style>
        /* Existing custom styles from your HTML, if any */
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen">

    {{-- Main content wrapper - for now, this will be a full page, later part of a layout --}}
    <div class="p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Analytics & Reporting</h2>

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
            <a href="{{ route('analytics.overview') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Overview</a>
            <a href="{{ route('analytics.trends') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Trends</a>
            <a href="{{ route('analytics.reports') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Reports</a>
            <a href="{{ route('analytics.performance') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Performance (Conceptual)</a>
        </div>

        <div id="analytics-tab-performance" class="analytics-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Performance Insights (Conceptual)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Driver Performance</h4>
                    <p class="text-gray-600">
                        This section would provide detailed metrics on individual driver performance, including:
                        <ul class="list-disc list-inside ml-4 text-sm mt-2">
                            <li>On-time delivery rates</li>
                            <li>Average time per job</li>
                            <li>Fuel efficiency comparisons</li>
                            <li>Customer satisfaction scores related to driver interaction</li>
                        </ul>
                        (Requires integration with Dispatching and potentially driver mobile apps.)
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Equipment Performance & ROI</h4>
                    <p class="text-gray-600">
                        Track the return on investment for each equipment type and individual units:
                        <ul class="list-disc list-inside ml-4 text-sm mt-2">
                            <li>Total revenue generated per unit/type</li>
                            <li>Maintenance costs vs. revenue</li>
                            <li>Downtime analysis</li>
                            <li>Break-even analysis for equipment purchases</li>
                        </ul>
                        (Requires detailed cost tracking and revenue attribution.)
                    </p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md col-span-1 md:col-span-2">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Junk Removal Profitability</h4>
                    <p class="text-gray-600">
                        Analyze the profitability of your junk removal services:
                        <ul class="list-disc list-inside ml-4 text-sm mt-2">
                            <li>Average revenue per junk removal job</li>
                            <li>Disposal costs vs. revenue</li>
                            <li>Customer acquisition cost for junk removal leads</li>
                            <li>Breakdown by junk type or volume.</li>
                        </ul>
                        (Requires detailed tracking within the Junk Removal module.)
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>