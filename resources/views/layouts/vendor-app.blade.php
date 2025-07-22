<pre>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Vendor Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                <li><a href="{{ route('vendor.dashboard') }}" data-module="dashboard" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange"><i class="fas fa-home mr-4 text-lg"></i> Dashboard</a></li>
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
    </aside>

    {{-- Main content area container (updated to correctly flex) --}}
    <div class="flex-1 flex flex-col overflow-x-hidden"> {{-- This div will now correctly flex to fill the remaining space --}}
        <header class="bg-white p-4 lg:p-6 shadow-md flex justify-between items-center z-30">
            <div class="search-bar flex items-center bg-gray-100 rounded-full px-4 py-2 w-full max-w-sm">
                <i class="fas fa-search text-gray-400 mr-2"></i>
                <input type="text" placeholder="Search here..." class="bg-transparent outline-none text-gray-700 w-full">
            </div>
            <div class="user-profile flex items-center ml-4">
                <span class="font-semibold text-gray-800 hidden md:block">Ahmad Khan</span>
                <span class="text-gray-500 text-sm mx-2 hidden md:block">Super Admin</span>
                <div class="w-10 h-10 rounded-full bg-chili-red text-white flex items-center justify-center font-bold text-base ml-2">AK</div>
            </div>
        </header>

        {{-- Dynamic content from child views will be inserted here --}}
        {{-- Removed the <main> tag here, as it's now handled by the outer div --}}
        <div id="module-content" class="module-content-wrapper flex-1 p-4 lg:p-8 overflow-y-auto">
            @yield('content')
        </div>
    </div>
    <div id="notificationContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        // Mobile navigation functionality
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
        sidebarOverlay.addEventListener('click', toggleMobileNav);

        // Close mobile nav when clicking on nav links
        const navLinks = document.querySelectorAll('.main-nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleMobileNav();
                }
            });
        });

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

        // Set active navigation link based on current URL
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.main-nav a').forEach(link => {
                link.classList.remove('active', 'bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
                
                const linkHref = new URL(link.href).pathname;

                if (linkHref === currentPath || 
                    (currentPath.startsWith('/vendor/dashboard') && link.getAttribute('data-module') === 'dashboard') ||
                    (currentPath.startsWith('/bookings') && linkHref.includes('/bookings')) ||
                    (currentPath.startsWith('/equipment') && linkHref.includes('/equipment')) ||
                    (currentPath.startsWith('/customers') && linkHref.includes('/customers')) ||
                    (currentPath.startsWith('/drivers') && linkHref.includes('/drivers')) ||
                    (currentPath.startsWith('/dispatching') && linkHref.includes('/dispatching')) ||
                    (currentPath.startsWith('/junk_removal_jobs') && linkHref.includes('/junk_removal_jobs')) ||
                    (currentPath.startsWith('/quotes') && linkHref.includes('/quotes')) ||
                    (currentPath.startsWith('/invoices') && linkHref.includes('/invoices')) || // Add invoices separately if needed
                    (currentPath.startsWith('/payments') && linkHref.includes('/payments')) || // Add payments separately if needed
                    (currentPath.startsWith('/financials') && linkHref.includes('/financials')) ||
                    (currentPath.startsWith('/analytics') && linkHref.includes('/analytics')) ||
                    (currentPath.startsWith('/branding') && linkHref.includes('/branding')) ||
                    (currentPath.startsWith('/settings') && linkHref.includes('/settings'))
                ) {
                    link.classList.add('active', 'bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
                }
            });
        });

        // Notification system (example, ensure you have a container with id="notificationContainer")
        function showNotification(message, type = 'info') {
            const notificationContainer = document.getElementById('notificationContainer');
            if (!notificationContainer) {
                console.warn("Notification container not found. Cannot display notification.");
                return;
            }
            const notification = document.createElement('div');
            notification.className = `p-4 rounded-lg shadow-lg max-w-sm mt-2 transition-all duration-300 transform translate-x-full opacity-0`;

            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                info: 'bg-blue-500 text-white',
                warning: 'bg-yellow-500 text-white'
            };

            notification.className += ` ${colors[type]}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            notificationContainer.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
                notification.classList.add('translate-x-0', 'opacity-100');
            }, 50);

            // Animate out and remove after a delay
            setTimeout(() => {
                notification.classList.remove('translate-x-0', 'opacity-100');
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

    </script>
</body>
</html>
</pre>