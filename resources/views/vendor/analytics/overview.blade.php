<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Analytics Overview</title>
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
            <a href="{{ route('analytics.overview') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Overview</a>
            <a href="{{ route('analytics.trends') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Trends</a>
            <a href="{{ route('analytics.reports') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Reports</a>
            <a href="{{ route('analytics.performance') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Performance (Conceptual)</a>
        </div>

        <div id="analytics-tab-overview" class="analytics-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Analytics Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h4 class="text-lg text-gray-500 mb-2">Total Bookings (YTD)</h4>
                    <p id="overviewTotalBookings" class="text-4xl font-bold text-blue-600">{{ $totalBookingsYTD }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h4 class="text-lg text-gray-500 mb-2">Avg. Booking Value</h4>
                    <p id="overviewAvgBookingValue" class="text-4xl font-bold text-purple-600">${{ number_format($avgBookingValue, 2) }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h4 class="text-lg text-gray-500 mb-2">New Customers (YTD)</h4>
                    <p id="overviewNewCustomers" class="text-4xl font-bold text-green-600">{{ $newCustomersYTD }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h4 class="text-lg text-gray-500 mb-2">Equipment Utilization</h4>
                    <p id="overviewEquipmentUtilization" class="text-4xl font-bold text-ut-orange">{{ number_format($equipmentUtilization, 1) }}%</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h4 class="text-lg text-gray-500 mb-2">Total Revenue (YTD)</h4>
                    <p id="overviewTotalRevenue" class="text-4xl font-bold text-green-600">${{ number_format($totalRevenueYTD, 2) }}</p>
                </div>
                 <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h4 class="text-lg text-gray-500 mb-2">Total Expenses (YTD)</h4>
                    <p id="overviewTotalExpenses" class="text-4xl font-bold text-red-600">${{ number_format($totalExpensesYTD, 2) }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Key Analytical Insights</h4>
                <p class="text-gray-600">
                    This section would offer a deeper dive into overall business performance using various metrics.
                    (In a real application, more complex charts, heatmaps, or executive summaries would be displayed here.)
                </p>
            </div>
        </div>
    </div>
</body>
</html>