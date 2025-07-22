@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Analytics & Reporting: Trends</h2>

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
        <span class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="trends">Trends</span>
        <a href="{{ route('analytics.reports') }}" class="analytics-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="reports">Reports</a>
    </div>

    <div id="analytics-tab-content-trends" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Historical Data Trends</h3>
        <p class="text-gray-600 mb-6">
            Analyze long-term trends in your business performance.
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Revenue Trend Chart --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Quarterly Revenue Trend</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">Export</button>
                </div>
                <canvas id="quarterlyRevenueTrendChart" class="max-h-[300px]"></canvas>
            </div>

            {{-- Booking Growth Chart --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Yearly Booking Growth</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">Export</button>
                </div>
                <canvas id="yearlyBookingGrowthChart" class="max-h-[300px]"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- New Customer Acquisition Trend --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">New Customer Acquisition</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">Export</button>
                </div>
                <canvas id="newCustomerAcquisitionChart" class="max-h-[300px]"></canvas>
            </div>

            {{-- Average Job Value Trend --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Average Job Value Trend</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">Export</button>
                </div>
                <canvas id="averageJobValueChart" class="max-h-[300px]"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Data passed from Laravel Controller (example structure)
            const quarterlyRevenueTrendData = {
                labels: ['Q1 2023', 'Q2 2023', 'Q3 2023', 'Q4 2023', 'Q1 2024'],
                datasets: [{
                    label: 'Revenue',
                    data: [30000, 32000, 35000, 33000, 38000],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            };

            const yearlyBookingGrowthData = {
                labels: ['2021', '2022', '2023', '2024'],
                datasets: [{
                    label: 'Bookings',
                    data: [150, 180, 220, 250],
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            };

            const newCustomerAcquisitionData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Customers',
                    data: [10, 12, 15, 11, 14, 16],
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            };

            const averageJobValueData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Avg. Job Value ($)',
                    data: [350, 360, 340, 370, 355, 380],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: false,
                    tension: 0.4
                }]
            };


            let quarterlyRevenueTrendChartInstance = null;
            let yearlyBookingGrowthChartInstance = null;
            let newCustomerAcquisitionChartInstance = null;
            let averageJobValueChartInstance = null;

            function renderTrendCharts() {
                const quarterlyRevenueCtx = document.getElementById('quarterlyRevenueTrendChart');
                const yearlyBookingGrowthCtx = document.getElementById('yearlyBookingGrowthChart');
                const newCustomerAcquisitionCtx = document.getElementById('newCustomerAcquisitionChart');
                const averageJobValueCtx = document.getElementById('averageJobValueChart');

                if (quarterlyRevenueTrendChartInstance) quarterlyRevenueTrendChartInstance.destroy();
                if (yearlyBookingGrowthChartInstance) yearlyBookingGrowthChartInstance.destroy();
                if (newCustomerAcquisitionChartInstance) newCustomerAcquisitionChartInstance.destroy();
                if (averageJobValueChartInstance) averageJobValueChartInstance.destroy();

                if (quarterlyRevenueCtx) {
                    quarterlyRevenueTrendChartInstance = new Chart(quarterlyRevenueCtx.getContext('2d'), {
                        type: 'line',
                        data: quarterlyRevenueTrendData,
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
                                        text: 'Revenue ($)'
                                    }
                                }
                            }
                        }
                    });
                }

                if (yearlyBookingGrowthCtx) {
                    yearlyBookingGrowthChartInstance = new Chart(yearlyBookingGrowthCtx.getContext('2d'), {
                        type: 'line',
                        data: yearlyBookingGrowthData,
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
                                        text: 'Bookings'
                                    }
                                }
                            }
                        }
                    });
                }

                if (newCustomerAcquisitionCtx) {
                    newCustomerAcquisitionChartInstance = new Chart(newCustomerAcquisitionCtx.getContext('2d'), {
                        type: 'bar',
                        data: newCustomerAcquisitionData,
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
                                        text: 'New Customers'
                                    }
                                }
                            }
                        }
                    });
                }

                if (averageJobValueCtx) {
                    averageJobValueChartInstance = new Chart(averageJobValueCtx.getContext('2d'), {
                        type: 'line',
                        data: averageJobValueData,
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
                                    beginAtZero: false, // Don't necessarily start at zero for average values
                                    title: {
                                        display: true,
                                        text: 'Amount ($)'
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Set active analytics tab and render charts on load
            document.addEventListener('DOMContentLoaded', () => {
                renderTrendCharts();

                const analyticsTabs = document.querySelectorAll('.analytics-tab');
                analyticsTabs.forEach(tab => {
                    tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                    tab.classList.add('text-gray-500', 'border-transparent');
                });
                // Set 'trends' as active
                document.querySelector('.analytics-tab[data-tab="trends"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
                document.querySelector('.analytics-tab[data-tab="trends"]').classList.remove('text-gray-500', 'border-transparent');
            });
        </script>
@endsection