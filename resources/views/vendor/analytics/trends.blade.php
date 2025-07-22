<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Analytics Trends</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> {{-- Chart.js CDN --}}
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
            <a href="{{ route('analytics.trends') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Trends</a>
            <a href="{{ route('analytics.reports') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Reports</a>
            <a href="{{ route('analytics.performance') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Performance (Conceptual)</a>
        </div>

        <div id="analytics-tab-trends" class="analytics-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Performance Trends</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                        <h4 class="text-xl text-gray-800 font-semibold m-0">Daily Engagement & Bookings</h4>
                        <button onclick="alert('Simulating export of daily trend data...')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 transition-colors">Export</button>
                    </div>
                    <canvas id="dailyAnalyticsChartAnalytics" class="max-h-[300px]"></canvas>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                        <h4 class="text-xl text-gray-800 font-semibold m-0">Monthly Revenue & Bookings</h4>
                        <button onclick="alert('Simulating export of monthly trend data...')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 transition-colors">Export</button>
                    </div>
                    <canvas id="monthlyRevenueBookingsChart" class="max-h-[300px]"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data passed from Laravel Controller
            const dailyAnalyticsData = @json($dailyAnalyticsChartData);
            const monthlyRevenueBookingsData = @json($monthlyRevenueBookingsChartData);

            // Chart instances
            let dailyAnalyticsChartInstance = null;
            let monthlyRevenueBookingsChartInstance = null;

            function renderCharts() {
                const dailyCtx = document.getElementById('dailyAnalyticsChartAnalytics');
                const monthlyCtx = document.getElementById('monthlyRevenueBookingsChart');

                // Destroy existing chart instances to prevent duplicates on re-render
                if (dailyAnalyticsChartInstance) {
                    dailyAnalyticsChartInstance.destroy();
                }
                if (monthlyRevenueBookingsChartInstance) {
                    monthlyRevenueBookingsChartInstance.destroy();
                }

                if (dailyCtx && monthlyCtx) {
                    // Daily Analytics Chart (Bar Chart)
                    dailyAnalyticsChartInstance = new Chart(dailyCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: dailyAnalyticsData.labels,
                            datasets: [
                                {
                                    label: dailyAnalyticsData.datasets[0].label,
                                    data: dailyAnalyticsData.datasets[0].data,
                                    backgroundColor: 'rgba(0, 0, 0, 0.7)', // Black
                                    borderColor: 'rgba(0, 0, 0, 1)',
                                    borderWidth: 1,
                                    borderRadius: 5
                                },
                                {
                                    label: dailyAnalyticsData.datasets[1].label,
                                    data: dailyAnalyticsData.datasets[1].data,
                                    backgroundColor: 'rgba(234, 58, 38, 0.7)', // Chili Red
                                    borderColor: 'rgba(234, 58, 38, 1)',
                                    borderWidth: 1,
                                    borderRadius: 5
                                },
                                {
                                    label: dailyAnalyticsData.datasets[2].label,
                                    data: dailyAnalyticsData.datasets[2].data,
                                    backgroundColor: 'rgba(255, 134, 0, 0.7)', // UT Orange
                                    borderColor: 'rgba(255, 134, 0, 1)',
                                    borderWidth: 1,
                                    borderRadius: 5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                title: {
                                    display: false,
                                    text: 'Daily Analytics'
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                }
                            }
                        }
                    });

                    // Monthly Revenue & Bookings Chart (Line Chart)
                    monthlyRevenueBookingsChartInstance = new Chart(monthlyCtx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: monthlyRevenueBookingsData.labels,
                            datasets: [
                                {
                                    label: monthlyRevenueBookingsData.datasets[0].label,
                                    data: monthlyRevenueBookingsData.datasets[0].data,
                                    borderColor: 'rgba(234, 58, 38, 1)', // Chili Red
                                    backgroundColor: 'rgba(234, 58, 38, 0.2)',
                                    fill: true,
                                    tension: 0.4,
                                    yAxisID: 'y'
                                },
                                {
                                    label: monthlyRevenueBookingsData.datasets[1].label,
                                    data: monthlyRevenueBookingsData.datasets[1].data,
                                    borderColor: 'rgba(0, 0, 0, 1)', // Black
                                    backgroundColor: 'rgba(0, 0, 0, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    yAxisID: 'y1' // Use a second Y-axis
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                title: {
                                    display: false,
                                    text: 'Monthly Revenue & Bookings'
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Revenue ($)'
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Bookings'
                                    },
                                    grid: {
                                        drawOnChartArea: false // Only draw grids for the left Y axis
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Render charts when the page loads
            renderCharts();
        });
    </script>
</body>
</html>