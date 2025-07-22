@extends('layouts.vendor-app')

@section('content')
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h2>
        <p class="text-gray-600 text-sm">Track your progress and monitor your rental business performance</p>
    </div>

    <div class="flex border-b border-gray-200 mb-8 space-x-6">
        {{-- Tabs - set active dynamically by controller/Blade --}}
        <span class="dashboard-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="overview">Overview</span>
        <a href="{{ route('settings.show') }}" class="dashboard-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="settings">Settings</a>
        <a href="{{ route('analytics.overview') }}" class="dashboard-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="analytics">Analytics</a>
        <a href="{{ route('quotes.index') }}" class="dashboard-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="requests">Requests</a>
        <a href="{{ route('financials.overview') }}" class="dashboard-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="financials">Financials</a>
    </div>

    <div id="tab-content-overview" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Metric Cards - Dynamic Data --}}
            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer" onclick="showAlert('Total Revenue')">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Total Revenue</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">${{ number_format($totalRevenue, 2) }}</p>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <i class="fas fa-arrow-up text-xs"></i> +12% from last month
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-2xl text-[#EA3A26]"></i>
                </div>
            </div>

            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer" onclick="showAlert('Bookings')">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Bookings</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">{{ $totalBookings }}</p>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <i class="fas fa-arrow-up text-xs"></i> +5% from last month
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-2xl text-black"></i>
                </div>
            </div>

            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer" onclick="showAlert('Outstanding A/R')">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Outstanding A/R</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">${{ number_format($outstandingAR, 2) }}</p>
                    <span class="text-sm text-red-600 flex items-center gap-1">
                        <i class="fas fa-arrow-down text-xs"></i> -0.5% from last month
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-hand-holding-dollar text-2xl text-[#FF8600]"></i>
                </div>
            </div>

            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer" onclick="showAlert('Total Expenses')">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Total Expenses</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">${{ number_format($totalExpenses, 2) }}</p>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <i class="fas fa-arrow-up text-xs"></i> +8% from last month
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-money-bill-transfer text-2xl text-[#FF2424]"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Charts - Dynamic Data --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Monthly Revenue & Bookings</h3>
                    <button class="px-4 py-2 bg-[#EA3A26] text-white rounded-md text-sm font-semibold hover:bg-[#F54F1D] transition-colors duration-200" onclick="showAlert('Exporting Monthly Revenue & Bookings data...')">Export</button>
                </div>
                <canvas id="monthlyRevenueBookingsChart" class="max-h-[300px]"></canvas>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Equipment Type Bookings</h3>
                    <button class="px-4 py-2 bg-[#EA3A26] text-white rounded-md text-sm font-semibold hover:bg-[#F54F1D] transition-colors duration-200" onclick="showAlert('Exporting Equipment Type Bookings data...')">Export</button>
                </div>
                <canvas id="equipmentTypeBookingsChart" class="max-h-[300px]"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl text-gray-800 font-semibold pb-3 mb-4 border-b border-gray-200">Pending Requests</h3>
            <div class="space-y-4">
                @forelse($pendingRequests as $request)
                    <div class="request-item flex justify-between items-center pb-4 border-b border-dashed border-gray-200 last:border-b-0 cursor-pointer" onclick="showRequestDetails('{{ $request['type'] }}', '{{ $request['customer_info'] }}', '{{ $request['description'] }}', '{{ $request['status'] }}')">
                        <div>
                            <strong class="block text-gray-800 mb-1">{{ $request['description'] }}</strong>
                            <span class="text-gray-500 text-sm">{{ $request['customer_info'] }} - Due: {{ $request['due_date'] }}</span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase text-white {{ $request['status_class'] }}">{{ $request['status'] }}</span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No pending requests at this time.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Request Detail Modal --}}
    <div id="requestDetailModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-lg relative">
            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl font-bold" onclick="closeModal('requestDetailModal')">&times;</button>
            <h3 class="text-2xl font-bold text-[#EA3A26] mb-4 border-b pb-2 border-gray-200" id="requestModalTitle">Request Details</h3>
            <div class="space-y-3 text-gray-700">
                <p><strong>Type:</strong> <span id="modalRequestType"></span></p>
                <p><strong>Customer/Item:</strong> <span id="modalRequestCustomerItem"></span></p>
                <p><strong>Description:</strong> <span id="modalRequestDescription"></span></p>
                <p><strong>Status:</strong> <span id="modalRequestStatus" class="px-3 py-1 rounded-full text-xs font-bold uppercase text-white"></span></p>
            </div>
            <div class="mt-6 flex justify-end">
                <button class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200" onclick="closeModal('requestDetailModal')">Close</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data passed from Laravel Controller
        const monthlyRevenueBookingsChartData = @json($monthlyRevenueBookingsChartData);
        const equipmentTypeBookingsChartData = @json($equipmentTypeBookingsChartData);

        // Chart instances
        let monthlyRevenueBookingsChartInstance = null;
        let equipmentTypeBookingsChartInstance = null;

        // Function to render charts
        function renderCharts() {
            const monthlyCtx = document.getElementById('monthlyRevenueBookingsChart');
            const equipmentTypeCtx = document.getElementById('equipmentTypeBookingsChart');

            // Destroy existing chart instances to prevent duplicates on re-render
            if (monthlyRevenueBookingsChartInstance) {
                monthlyRevenueBookingsChartInstance.destroy();
            }
            if (equipmentTypeBookingsChartInstance) {
                equipmentTypeBookingsChartInstance.destroy();
            }

            if (monthlyCtx) {
                monthlyRevenueBookingsChartInstance = new Chart(monthlyCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: monthlyRevenueBookingsChartData.labels,
                        datasets: [
                            {
                                label: 'Total Revenue',
                                data: monthlyRevenueBookingsChartData.datasets[0].data,
                                borderColor: 'rgba(234, 58, 38, 1)', // Chili Red
                                backgroundColor: 'rgba(234, 58, 38, 0.2)',
                                fill: true,
                                tension: 0.4,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Bookings Count',
                                data: monthlyRevenueBookingsChartData.datasets[1].data,
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
            } else {
                console.error("Monthly chart canvas element not found.");
            }

            if (equipmentTypeCtx) {
                equipmentTypeBookingsChartInstance = new Chart(equipmentTypeCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: equipmentTypeBookingsChartData.labels,
                        datasets: [{
                            data: equipmentTypeBookingsChartData.datasets[0].data,
                            backgroundColor: [
                                '#EA3A26', // Chili Red
                                '#FF8600', // UT Orange
                                '#F54F1D', // Tangelo
                                '#4CAF50', // Green
                                '#2196F3', // Blue
                                '#FFC107'  // Amber
                            ],
                            hoverOffset: 4
                        }]
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
                                text: 'Equipment Type Bookings'
                            }
                        }
                    }
                });
            } else {
                console.error("Equipment Type chart canvas element not found.");
            }

            console.log("Charts rendered successfully.");
        }

        // --- Dashboard Specific Functions ---

        // Function to show a simple alert for metric cards/exports
        function showAlert(message) {
            alert("Action: " + message + "\n(In a real app, this would lead to a detailed report or download.)");
        }

        // Function to open a modal
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        // Function to close a modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Function to show details for a pending request in a modal
        function showRequestDetails(type, customerItem, description, status) {
            document.getElementById('modalRequestType').textContent = type;
            document.getElementById('modalRequestCustomerItem').textContent = customerItem;
            document.getElementById('modalRequestDescription').textContent = description;

            const statusSpan = document.getElementById('modalRequestStatus');
            statusSpan.textContent = status;
            // Clear previous status classes
            statusSpan.classList.remove('bg-[#FF8600]', 'bg-green-600', 'bg-red-600', 'bg-yellow-500'); // Add yellow-500 for quotes
            // Apply new status class
            if (status === 'Pending' || status === 'Sent') {
                statusSpan.classList.add('bg-[#FF8600]');
            } else if (status === 'Accepted' || status === 'Confirmed') {
                statusSpan.classList.add('bg-green-600');
            } else if (status === 'Rejected' || status === 'Expired' || status === 'Cancelled') {
                statusSpan.classList.add('bg-red-600');
            }
            // Add more conditions for other statuses if needed

            openModal('requestDetailModal');
        }

        // Initial load logic
        document.addEventListener('DOMContentLoaded', () => {
            renderCharts(); // Render charts on initial page load

            // Set active dashboard tab
            const dashboardTabs = document.querySelectorAll('.dashboard-tab');
            dashboardTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Assuming 'overview' is the default active tab for the dashboard
            document.querySelector('.dashboard-tab[data-tab="overview"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.dashboard-tab[data-tab="overview"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>