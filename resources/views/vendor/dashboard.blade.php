<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Vendor Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'chili-red': '#EA3A26',
                        'ut-orange': '#FF8600',
                        'chili-red-2': '#EA3D2A',
                        'chili-red-3': '#EA3D24',
                        'tangelo': '#F54F1D',
                        'custom-red': '#FF2424', // Use sparingly, perhaps for alerts
                        'custom-red-2': '#FF0000', // Use sparingly, perhaps for alerts
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'pulse-custom': 'pulse 0.5s ease-in-out',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom animations for consistency */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-backdrop {
            backdrop-filter: blur(4px);
        }
        /* Styles for dynamic tab content in modules (if they use these classes) */
        .tab-button.active, .finance-tab.active, .analytics-tab.active, .settings-tab.active, .dispatch-tab.active, .branding-tab.active {
            border-bottom-color: #EA3A26;
            color: #EA3A26;
        }
        /* Ensure dynamic content uses full height available */
        .module-content-wrapper {
            min-height: calc(100vh - 120px); /* Adjust based on header height, etc. */
            /* This height will ensure charts/tables render correctly without collapsing */
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen flex flex-col lg:flex-row">

    {{-- Mobile Navigation Toggle --}}
    <button
        id="mobileNavToggle"
        class="lg:hidden fixed top-4 left-4 z-50 bg-chili-red text-white p-3 rounded-lg shadow-lg hover:bg-chili-red-2 transition-colors duration-300"
    >
        <i class="fas fa-bars text-lg"></i>
    </button>

    {{-- Sidebar Overlay for Mobile --}}
    <div
        id="sidebarOverlay"
        class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 opacity-0 invisible transition-all duration-300"
    ></div>

    {{-- Sidebar Navigation --}}
    <aside
        id="sidebar"
        class="fixed lg:relative bg-gradient-to-b from-black to-gray-800 w-72 h-screen overflow-y-auto z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
    >
        <div class="text-center py-8 px-6">
            <h1 class="text-chili-red text-3xl font-bold drop-shadow-lg">
                <i class="fas fa-truck mr-2"></i>Helly
            </h1>
            <p class="text-gray-400 text-sm">Vendor Dashboard</p>
        </div>

        <nav class="main-nav">
            <ul class="space-y-2 px-4">
                <li><a href="#" data-module="dashboard" class="nav-link active flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-home mr-4 text-lg"></i> Dashboard</a></li>
                <li><a href="{{ route('bookings.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-calendar-alt mr-4 text-lg"></i> Booking & Scheduling</a></li>
                <li><a href="{{ route('equipment.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-dumpster mr-4 text-lg"></i> Equipment Management</a></li>
                <li><a href="{{ route('customers.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-users mr-4 text-lg"></i> Customer Management</a></li>
                <li><a href="{{ route('drivers.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-id-card-alt mr-4 text-lg"></i> Driver Management</a></li>
                <li><a href="{{ route('dispatching.show') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-truck-moving mr-4 text-lg"></i> Dispatching</a></li>
                <li><a href="{{ route('junk_removal_jobs.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-trash-alt mr-4 text-lg"></i> Junk Removal</a></li>
                <li><a href="{{ route('quotes.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-file-invoice-dollar mr-4 text-lg"></i> Quotes & Invoices</a></li>
                <li><a href="{{ route('financials.overview') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-chart-line mr-4 text-lg"></i> Financials</a></li>
                <li><a href="{{ route('analytics.overview') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-chart-pie mr-4 text-lg"></i> Analytics & Reporting</a></li>
                <li><a href="{{ route('branding.show') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-paint-brush mr-4 text-lg"></i> Rebranding</a></li>
                <li><a href="{{ route('settings.show') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-cog mr-4 text-lg"></i> Settings</a></li>
                <li class="mt-8">
                    <form action="{{ route('vendor.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link w-full text-left flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
                            <i class="fas fa-sign-out-alt mr-4 text-lg"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer mt-auto py-4 text-center text-gray-500 text-xs">
            <p>&copy; 2025 Helly</p>
        </div>
    </aside>

    <main class="flex-1 flex flex-col">
        <header class="bg-white p-4 lg:p-6 shadow-md flex justify-between items-center z-30">
            <div class="search-bar flex items-center bg-gray-100 rounded-full px-4 py-2 w-full max-w-sm">
                <i class="fas fa-search text-gray-400 mr-2"></i>
                <input type="text" placeholder="Search here..." class="bg-transparent outline-none text-gray-700 w-full">
            </div>
            <div class="user-profile flex items-center ml-4">
                <span class="font-semibold text-gray-800 hidden md:block">Ahmad Khan</span> {{-- Dynamically populate --}}
                <span class="text-gray-500 text-sm mx-2 hidden md:block">Super Admin</span> {{-- Dynamically populate --}}
                <div class="w-10 h-10 rounded-full bg-chili-red text-white flex items-center justify-center font-bold text-base ml-2">AK</div> {{-- Dynamically populate --}}
            </div>
        </header>

        {{-- Main content area from dashboard.html, now dynamic --}}
        <div id="module-container" class="module-content-wrapper flex-1 p-4 lg:p-8 overflow-y-auto">
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
        </div>
    </main>

    <div id="notificationContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        // Data passed from Laravel Controller
        const monthlyRevenueBookingsChartData = @json($monthlyRevenueBookingsChartData);
        const equipmentTypeBookingsChartData = @json($equipmentTypeBookingsChartData);

        // Chart instances
        let monthlyRevenueBookingsChartInstance = null;
        let equipmentTypeBookingsChartInstance = null;

        // Function to render charts
        function renderCharts() {
            console.log("Attempting to render charts...");
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
            statusSpan.classList.remove('bg-[#FF8600]', 'bg-green-600', 'bg-red-600');
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

        // --- Mobile Navigation Functionality (from main index.html) ---
        const mobileNavToggle = document.getElementById('mobileNavToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleMobileNav() {
            sidebar.classList.toggle('translate-x-0');
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('opacity-0');
            sidebarOverlay.classList.toggle('invisible');
            sidebarOverlay.classList.toggle('opacity-100');
            sidebarOverlay.classList.toggle('visible');

            const icon = mobileNavToggle.querySelector('i');
            if (sidebar.classList.contains('translate-x-0')) {
                icon.className = 'fas fa-times text-lg';
            } else {
                icon.className = 'fas fa-bars text-lg';
            }
        }

        mobileNavToggle.addEventListener('click', toggleMobileNav);
        sidebarOverlay.addEventListener('click', toggleMobileNav); // Click overlay to close

        // Handle window resize to ensure sidebar is visible on large screens
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                sidebarOverlay.classList.add('opacity-0', 'invisible');
                sidebarOverlay.classList.remove('opacity-100', 'visible');
                mobileNavToggle.querySelector('i').className = 'fas fa-bars text-lg';
            }
        });

        // Initial load logic
        document.addEventListener('DOMContentLoaded', () => {
            renderCharts(); // Render charts on initial page load

            // Set active navigation link
            const currentPath = window.location.pathname;
            document.querySelectorAll('.main-nav a').forEach(link => {
                // Adjust this logic if your routes are more complex or nested
                if (link.getAttribute('href') === currentPath || 
                    (currentPath.startsWith('/financials') && link.getAttribute('href').includes('/financials')) ||
                    (currentPath.startsWith('/analytics') && link.getAttribute('href').includes('/analytics')) ||
                    (currentPath.startsWith('/settings') && link.getAttribute('href').includes('/settings')) ||
                    (currentPath.startsWith('/branding') && link.getAttribute('href').includes('/branding')) ||
                    (currentPath === '/login' && link.getAttribute('data-module') === 'dashboard') // Default case for /login redirect to dashboard
                ) {
                    link.classList.add('active', 'bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
                } else {
                    link.classList.remove('active', 'bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
                }
            });
        });
    </script>
</body>
</html>