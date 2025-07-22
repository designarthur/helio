@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Analytics & Reporting Overview</h2>

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
        {{-- Analytics Tabs --}}
        <span class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="overview">Overview</span>
        <a href="{{ route('analytics.customer_insights') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="customer_insights">Customer Insights</a>
        <a href="{{ route('analytics.equipment_performance') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="equipment_performance">Equipment Performance</a>
        <a href="{{ route('analytics.job_efficiency') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="job_efficiency">Job Efficiency</a>
    </div>

    <div id="analytics-tab-content-overview" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Key Performance Indicators (KPIs) --}}
            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Total Bookings</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">{{ $totalBookings }}</p>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <i class="fas fa-arrow-up text-xs"></i> +5% this month
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-2xl text-blue-600"></i>
                </div>
            </div>

            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Customer Satisfaction</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($customerSatisfaction, 1) }}%</p>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <i class="fas fa-smile text-xs"></i> Great!
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-star text-2xl text-green-600"></i>
                </div>
            </div>

            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Equipment Utilization</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($equipmentUtilization, 1) }}%</p>
                    <span class="text-sm text-orange-600 flex items-center gap-1">
                        <i class="fas fa-truck-ramp-box text-xs"></i> Room for improvement
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-percent text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Charts --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Booking Trends by Month</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">Export Chart</button>
                </div>
                <canvas id="bookingTrendChart" class="max-h-[300px]"></canvas>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Top Performing Equipment</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">Export Chart</button>
                </div>
                <canvas id="topEquipmentChart" class="max-h-[300px]"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl text-gray-800 font-semibold pb-3 mb-4 border-b border-gray-200">Recent Activity Log</h3>
            <div class="space-y-4">
                @forelse($recentActivity as $activity)
                    <div class="flex items-center space-x-3 pb-4 border-b border-dashed border-gray-200 last:border-b-0">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 text-sm">
                            <i class="{{ $activity['icon'] }}"></i>
                        </div>
                        <div>
                            <strong class="block text-gray-800">{{ $activity['description'] }}</strong>
                            <span class="text-gray-500 text-sm">{{ $activity['timestamp']->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No recent activity.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data passed from Laravel Controller
        const bookingTrendChartData = @json($bookingTrendChartData);
        const topEquipmentChartData = @json($topEquipmentChartData);

        let bookingTrendChartInstance = null;
        let topEquipmentChartInstance = null;

        function renderAnalyticsCharts() {
            const bookingCtx = document.getElementById('bookingTrendChart');
            const equipmentCtx = document.getElementById('topEquipmentChart');

            if (bookingTrendChartInstance) bookingTrendChartInstance.destroy();
            if (topEquipmentChartInstance) topEquipmentChartInstance.destroy();

            if (bookingCtx) {
                bookingTrendChartInstance = new Chart(bookingCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: bookingTrendChartData.labels,
                        datasets: [{
                            label: 'Number of Bookings',
                            data: bookingTrendChartData.datasets[0].data,
                            borderColor: 'rgba(234, 58, 38, 1)', // Chili Red
                            backgroundColor: 'rgba(234, 58, 38, 0.2)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: false,
                                text: 'Booking Trends by Month'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Bookings'
                                }
                            }
                        }
                    }
                });
            } else {
                console.error("Booking Trend chart canvas element not found.");
            }

            if (equipmentCtx) {
                topEquipmentChartInstance = new Chart(equipmentCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: topEquipmentChartData.labels,
                        datasets: [{
                            label: 'Bookings',
                            data: topEquipmentChartData.datasets[0].data,
                            backgroundColor: [
                                '#EA3A26', '#FF8600', '#F54F1D', '#4CAF50', '#2196F3' // Various colors
                            ],
                            borderColor: [
                                '#EA3A26', '#FF8600', '#F54F1D', '#4CAF50', '#2196F3'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Horizontal bars
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: false,
                                text: 'Top Performing Equipment'
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Bookings'
                                }
                            }
                        }
                    }
                });
            } else {
                console.error("Top Equipment chart canvas element not found.");
            }
        }

        // Set active analytics tab
        document.addEventListener('DOMContentLoaded', () => {
            renderAnalyticsCharts();

            const analyticsTabs = document.querySelectorAll('.analytics-tab');
            analyticsTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Assuming 'overview' is the default active tab for analytics
            document.querySelector('.analytics-tab[data-tab="overview"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.analytics-tab[data-tab="overview"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection