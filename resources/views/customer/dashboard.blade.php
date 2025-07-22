<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Customer Dashboard</title>
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
                        'tangelo': '#F54F1D',
                        'custom-red-2': '#FF0000', // For notification badges
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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }
        .ripple-effect {
            animation: ripple 0.6s linear;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans">
    <button 
        id="mobileNavToggle" 
        class="lg:hidden fixed top-4 left-4 z-50 bg-chili-red text-white p-3 rounded-lg shadow-lg hover:bg-chili-red-2 transition-colors duration-300"
    >
        <i class="fas fa-bars text-lg"></i>
    </button>

    <div 
        id="sidebarOverlay" 
        class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 opacity-0 invisible transition-all duration-300"
    ></div>

    <div class="flex min-h-screen">
        <nav 
            id="sidebar" 
            class="fixed lg:relative bg-gradient-to-b from-black to-gray-800 w-72 h-screen overflow-y-auto z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
        >
            <div class="text-center py-8 px-6">
                <h1 class="text-chili-red text-3xl font-bold drop-shadow-lg">
                    <i class="fas fa-truck mr-2"></i>Helly
                </h1>
            </div>

            <ul class="space-y-2 px-4">
                <li>
                    <a href="{{ route('customer.dashboard') }}" class="nav-link active flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-tachometer-alt mr-4 text-lg"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.bookings.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-calendar-alt mr-4 text-lg"></i>
                        Bookings
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" id="bookingsNavBadge">{{ $upcomingBookingsCount }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.invoices.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-file-invoice mr-4 text-lg"></i>
                        Invoices
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" id="invoicesNavBadge">{{ $pendingInvoicesCount + $overdueInvoicesCount }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.quotes.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-quote-left mr-4 text-lg"></i>
                        Quotes
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" id="quotesNavBadge">0</span> {{-- Will be dynamically updated --}}
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.notifications.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-bell mr-4 text-lg"></i>
                        Notifications
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" id="notificationsNavBadge">0</span> {{-- Will be dynamically updated --}}
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.payment_methods.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
                        <i class="fas fa-credit-card mr-4 text-lg"></i>
                        Payment Methods
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.profile.show') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
                        <i class="fas fa-user-circle mr-4 text-lg"></i>
                        Profile
                    </a>
                </li>
                <li class="mt-8">
                    <form action="{{ route('customer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link w-full text-left flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
                            <i class="fas fa-sign-out-alt mr-4 text-lg"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <main class="flex-1 lg:ml-0 p-4 lg:p-8 mt-16 lg:mt-0">
            <header class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
                <div class="text-center lg:text-left">
                    <h2 class="text-chili-red text-2xl lg:text-3xl font-bold mb-2">{{ $portalBannerText ?? 'Welcome back,' }} {{ $user->name }}!</h2>
                    <p class="text-gray-600 text-lg">Here's what's happening with your rentals today</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <a href="{{ route('customer.bookings.create') }}" class="btn-primary bg-gradient-to-r from-chili-red to-tangelo text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        New Rental Request
                    </a>
                    <button onclick="showConceptualAction('Contact Support', 'Opening a support ticket or live chat...')" class="btn-outline border-2 border-chili-red text-chili-red px-6 py-3 rounded-lg font-semibold hover:bg-chili-red hover:text-white transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-headset mr-2"></i>
                        Contact Support
                    </button>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <div class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 border-l-4 border-chili-red" onclick="window.location.href = '{{ route('customer.bookings.index', ['filter' => 'active']) }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-chili-red text-xl font-bold">
                            <i class="fas fa-box mr-3"></i>
                            Current Rentals
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2">{{ $currentRentalsCount }}</div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">Active</span>
                    </div>
                </div>

                <div class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 border-l-4 border-chili-red" onclick="window.location.href = '{{ route('customer.bookings.index', ['filter' => 'upcoming']) }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-chili-red text-xl font-bold">
                            <i class="fas fa-calendar-check mr-3"></i>
                            Upcoming Bookings
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2">{{ $upcomingBookingsCount }}</div>
                    <div class="text-gray-600">
                        {{-- Logic to show next delivery date would be complex for a simple dashboard card --}}
                        Next delivery: Check Bookings
                    </div>
                </div>

                <div class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 border-l-4 border-chili-red" onclick="window.location.href = '{{ route('customer.invoices.index', ['filter' => 'pending']) }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-chili-red text-xl font-bold">
                            <i class="fas fa-dollar-sign mr-3"></i>
                            Outstanding Balance
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2">${{ number_format($outstandingBalance, 2) }}</div>
                    <div class="flex flex-wrap gap-2">
                        @if($overdueInvoicesCount > 0)
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $overdueInvoicesCount }} Overdue</span>
                        @endif
                        @if($pendingInvoicesCount > 0)
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $pendingInvoicesCount }} Pending</span>
                        @endif
                    </div>
                </div>

                <div class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 border-l-4 border-chili-red" onclick="showConceptualAction('Monthly Stats', 'Displaying monthly spending insights...')">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-chili-red text-xl font-bold">
                            <i class="fas fa-chart-line mr-3"></i>
                            This Month
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2">${{ number_format($totalSpentThisMonth, 2) }}</div>
                    <div class="text-gray-600">
                        Total spent • {{ $completedRentalsThisMonth }} completed rentals
                    </div>
                </div>
            </div>

            <section class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8">
                <h3 class="flex items-center text-chili-red text-2xl font-bold mb-6">
                    <i class="fas fa-clock mr-3"></i>
                    Recent Activity
                </h3>
                <ul class="space-y-4">
                    @forelse($recentActivity as $activity)
                        <li class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 border-b border-gray-100 last:border-b-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white flex-shrink-0
                                @if($activity['type'] == 'booking') bg-gradient-to-br from-green-500 to-emerald-500
                                @elseif($activity['type'] == 'invoice') bg-gradient-to-br from-chili-red to-tangelo
                                @elseif($activity['type'] == 'quote') bg-gradient-to-br from-ut-orange to-yellow-500
                                @else bg-gray-500 @endif">
                                <i class="fas
                                    @if($activity['type'] == 'booking') fa-truck
                                    @elseif($activity['type'] == 'invoice') fa-credit-card
                                    @elseif($activity['type'] == 'quote') fa-file-alt
                                    @else fa-info-circle @endif
                                text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800 mb-1">{{ $activity['display_text'] }}</div>
                                <div class="text-gray-600 text-sm">
                                    {{ $activity['date']->diffForHumans() }}
                                    @if(isset($activity['url']))
                                    <a href="{{ $activity['url'] }}" class="ml-2 text-blue-600 hover:underline">View Details</a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-center text-gray-500 py-4">No recent activity to display.</li>
                    @endforelse
                </ul>
            </section>

            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('customer.bookings.create') }}" class="action-card bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-chili-red">
                    <div class="w-16 h-16 bg-gradient-to-br from-chili-red to-tangelo rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="font-semibold text-gray-800 mb-2">Request New Rental</div>
                    <div class="text-gray-600 text-sm">Get a quote for dumpsters, containers, or toilets</div>
                </a>
                
                <a href="{{ route('customer.invoices.index', ['filter' => 'pending']) }}" class="action-card bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-chili-red">
                    <div class="w-16 h-16 bg-gradient-to-br from-chili-red to-tangelo rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="font-semibold text-gray-800 mb-2">Pay Invoice</div>
                    <div class="text-gray-600 text-sm">Pay outstanding invoices quickly and securely</div>
                </a>
                
                <button onclick="showConceptualAction('Request Service', 'Opening service request form...')" class="action-card bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-chili-red">
                    <div class="w-16 h-16 bg-gradient-to-br from-chili-red to-tangelo rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="font-semibold text-gray-800 mb-2">Request Service</div>
                    <div class="text-gray-600 text-sm">Schedule pickup, swap, or additional services</div>
                </button>
                
                <button onclick="showConceptualAction('Track Delivery', 'Opening real-time tracking map...')" class="action-card bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-chili-red">
                    <div class="w-16 h-16 bg-gradient-to-br from-chili-red to-tangelo rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="font-semibold text-gray-800 mb-2">Track Delivery</div>
                    <div class="text-gray-600 text-sm">See real-time location of your driver</div>
                </button>
            </section>
        </main>
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
            
            // Toggle hamburger icon
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
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleMobileNav();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                sidebarOverlay.classList.add('opacity-0', 'invisible');
                sidebarOverlay.classList.remove('opacity-100', 'visible');
                mobileNavToggle.querySelector('i').className = 'fas fa-bars text-lg';
            }
        });

        // Add interactive functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on load
            const cards = document.querySelectorAll('.dashboard-card, .action-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Add click effects to action cards
            const actionCards = document.querySelectorAll('.action-card');
            actionCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('div');
                    ripple.className = 'absolute rounded-full bg-chili-red bg-opacity-30 ripple-effect pointer-events-none';
                    
                    const rect = card.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
                    ripple.style.transform = 'scale(0)';
                    
                    card.style.position = 'relative';
                    card.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Update notification counts dynamically
            // updateNotificationCounts(); // This function relies on JS data, will be updated by Blade if data is from backend.
        });

        function showConceptualAction(title, message) {
            alert(`${title}: ${message}`);
        }

        // Notification system (copied from previous files for consistency)
        function showNotification(message, type = 'info') {
            const notificationContainer = document.getElementById('notificationContainer');
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
            }, 50);

            // Animate out and remove after a delay
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Set active navigation state
        document.querySelector('.nav-link.active').classList.add('bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
    </script>
</body>
</html>