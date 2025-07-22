@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Analytics & Reporting: Performance</h2>

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
        <a href="{{ route('analytics.overview') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="overview">Overview</a>
        <a href="{{ route('analytics.customer_insights') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="customer_insights">Customer Insights</a>
        <a href="{{ route('analytics.equipment_performance') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="equipment_performance">Equipment Performance</a>
        <a href="{{ route('analytics.job_efficiency') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="job_efficiency">Job Efficiency</a>
        <a href="{{ route('analytics.trends') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="trends">Trends</a>
        <span class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="performance">Performance</span>
    </div>

    <div id="analytics-tab-content-performance" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Operational Performance Metrics</h3>
        <p class="text-gray-600 mb-6">
            Evaluate the efficiency and performance of your operations.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Dispatch Efficiency --}}
            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Average Dispatch Time</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">{{ $avgDispatchTime ?? 'N/A' }} <span class="text-lg">mins</span></p>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <i class="fas fa-clock text-xs"></i> 10% faster this month
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-truck-fast text-2xl text-blue-600"></i>
                </div>
            </div>

            {{-- On-Time Completion Rate --}}
            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">On-Time Completion Rate</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($onTimeCompletionRate ?? 0, 1) }}%</p>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <i class="fas fa-check-circle text-xs"></i> Consistently High
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-percent text-2xl text-green-600"></i>
                </div>
            </div>

            {{-- Driver Utilization --}}
            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Driver Utilization</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($driverUtilization ?? 0, 1) }}%</p>
                    <span class="text-sm text-orange-600 flex items-center gap-1">
                        <i class="fas fa-chart-line text-xs"></i> Opportunities to optimize
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-id-card-alt text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Driver Performance Chart --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Driver Performance (Bookings Completed)</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">View Details</button>
                </div>
                <canvas id="driverPerformanceChart" class="max-h-[300px]"></canvas>
            </div>

            {{-- Service Request Resolution Time --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Service Request Resolution Time (Avg. Hours)</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">View Details</button>
                </div>
                <canvas id="serviceResolutionChart" class="max-h-[300px]"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Sample Data (replace with actual data from your controller)
            const driverPerformanceChartData = {
                labels: ['Driver A', 'Driver B', 'Driver C', 'Driver D', 'Driver E'],
                datasets: [{
                    label: 'Bookings Completed',
                    data: [45, 38, 52, 30, 48],
                    backgroundColor: [
                        'rgba(234, 58, 38, 0.6)', // Chili Red
                        'rgba(255, 159, 64, 0.6)', // Orange
                        'rgba(75, 192, 192, 0.6)', // Green
                        'rgba(54, 162, 235, 0.6)', // Blue
                        'rgba(153, 102, 255, 0.6)' // Purple
                    ],
                    borderColor: [
                        'rgba(234, 58, 38, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            };

            const serviceResolutionChartData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Avg. Resolution Time (Hours)',
                    data: [12, 10, 8, 9, 7, 6],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            };

            let driverPerformanceChartInstance = null;
            let serviceResolutionChartInstance = null;

            function renderPerformanceCharts() {
                const driverCtx = document.getElementById('driverPerformanceChart');
                const serviceCtx = document.getElementById('serviceResolutionChart');

                if (driverPerformanceChartInstance) driverPerformanceChartInstance.destroy();
                if (serviceResolutionChartInstance) serviceResolutionChartInstance.destroy();

                if (driverCtx) {
                    driverPerformanceChartInstance = new Chart(driverCtx.getContext('2d'), {
                        type: 'bar',
                        data: driverPerformanceChartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Bookings Completed'
                                    }
                                }
                            }
                        }
                    });
                }

                if (serviceCtx) {
                    serviceResolutionChartInstance = new Chart(serviceCtx.getContext('2d'), {
                        type: 'line',
                        data: serviceResolutionChartData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Hours'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Set active analytics tab and render charts on load
            document.addEventListener('DOMContentLoaded', () => {
                renderPerformanceCharts();

                const analyticsTabs = document.querySelectorAll('.analytics-tab');
                analyticsTabs.forEach(tab => {
                    tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                    tab.classList.add('text-gray-500', 'border-transparent');
                });
                // Set 'performance' as active
                document.querySelector('.analytics-tab[data-tab="performance"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
                document.querySelector('.analytics-tab[data-tab="performance"]').classList.remove('text-gray-500', 'border-transparent');
            });
        </script>
@endsection