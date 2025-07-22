<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - My Notifications</title>
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
        .notification-item.unread {
            background-color: #fef2f2; /* Light red for unread */
            border-left: 4px solid #EA3A26; /* Chili red border */
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
                    <a href="{{ route('customer.dashboard') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
                        <i class="fas fa-tachometer-alt mr-4 text-lg"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.bookings.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-calendar-alt mr-4 text-lg"></i>
                        Bookings
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span> {{-- Will be dynamically updated --}}
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.invoices.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-file-invoice mr-4 text-lg"></i>
                        Invoices
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span> {{-- Will be dynamically updated --}}
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.quotes.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-quote-left mr-4 text-lg"></i>
                        Quotes
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span> {{-- Will be dynamically updated --}}
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.notifications.index') }}" class="nav-link active bg-gradient-to-r from-chili-red to-tangelo border-ut-orange transform translate-x-2 flex items-center px-6 py-4 text-white transition-all duration-300 rounded-lg border-l-4 relative">
                        <i class="fas fa-bell mr-4 text-lg"></i>
                        Notifications
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" id="notificationBadge">{{ $unreadCount }}</span>
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
            <header class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                    <div>
                        <h1 class="text-chili-red text-3xl lg:text-4xl font-bold mb-2">My Notifications</h1>
                        <p class="text-gray-600 text-lg">Stay updated on your rentals and account activity</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <form action="{{ route('customer.notifications.markAllAsRead') }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to mark all notifications as read?');">
                            @csrf
                            <button type="submit" class="btn-outline border-2 border-chili-red text-chili-red px-6 py-3 rounded-lg font-semibold shadow-md hover:bg-chili-red hover:text-white transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-check-double mr-2"></i>
                                Mark All As Read
                            </button>
                        </form>
                        <form action="{{ route('customer.notifications.clearAll') }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to clear all notifications? This cannot be undone.');">
                            @csrf
                            <button type="submit" class="btn-outline border-2 border-gray-400 text-gray-700 px-6 py-3 rounded-lg font-semibold shadow-md hover:bg-gray-500 hover:text-white transition-all duration-300 flex items-center justify-center">
                                <i class="fas fa-trash-alt mr-2"></i>
                                Clear All
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="bg-white rounded-2xl shadow-lg mb-8">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6 lg:px-8">
                        <a href="{{ route('customer.notifications.index', ['filter' => 'all']) }}" id="allNotificationsTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'all' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            All
                            <span class="ml-2 bg-chili-red text-white text-xs px-2 py-1 rounded-full">{{ $allCount }}</span>
                        </a>
                        <a href="{{ route('customer.notifications.index', ['filter' => 'unread']) }}" id="unreadNotificationsTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'unread' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Unread
                            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                        </a>
                        <a href="{{ route('customer.notifications.index', ['filter' => 'bookings']) }}" id="bookingsNotificationsTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'bookings' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Bookings
                            <span class="ml-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ $bookingsCount }}</span>
                        </a>
                        <a href="{{ route('customer.notifications.index', ['filter' => 'invoices']) }}" id="invoicesNotificationsTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'invoices' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Invoices
                            <span class="ml-2 bg-ut-orange text-white text-xs px-2 py-1 rounded-full">{{ $invoicesCount }}</span>
                        </a>
                        <a href="{{ route('customer.notifications.index', ['filter' => 'quotes']) }}" id="quotesNotificationsTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'quotes' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Quotes
                            <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">{{ $quotesCount }}</span>
                        </a>
                        <a href="{{ route('customer.notifications.index', ['filter' => 'alerts']) }}" id="alertsNotificationsTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'alerts' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Alerts
                            <span class="ml-2 bg-purple-500 text-white text-xs px-2 py-1 rounded-full">{{ $alertsCount }}</span>
                        </a>
                    </nav>
                </div>

                <div class="p-6 lg:p-8">
                    <div class="space-y-4" id="notificationList">
                        @forelse($notifications as $notification)
                            <div class="notification-item bg-white rounded-xl p-4 shadow-sm flex items-start space-x-4 {{ $notification['read'] ? '' : 'unread' }}">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-xl bg-gray-100
                                    @if($notification['category'] == 'Bookings') text-blue-500
                                    @elseif($notification['category'] == 'Invoices') text-ut-orange
                                    @elseif($notification['category'] == 'Quotes') text-green-500
                                    @elseif($notification['category'] == 'Alerts') text-chili-red
                                    @else text-gray-500 @endif">
                                    <i class="fas
                                        @if($notification['category'] == 'Bookings') fa-calendar-check
                                        @elseif($notification['category'] == 'Invoices') fa-file-invoice
                                        @elseif($notification['category'] == 'Quotes') fa-quote-left
                                        @elseif($notification['category'] == 'Alerts') fa-exclamation-triangle
                                        @else fa-info-circle @endif"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center mb-1">
                                        <h4 class="font-semibold text-gray-800">{{ $notification['category'] }}</h4>
                                        <span class="text-gray-500 text-xs">{{ $notification['time']->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700 text-sm">{{ $notification['message'] }}</p>
                                    <div class="mt-2 text-right">
                                        @if(!$notification['read'])
                                            <form action="{{ route('customer.notifications.markAsRead', $notification['id']) }}" method="POST" class="inline-block mr-3" onsubmit="return confirm('Mark this notification as read?');">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:underline text-xs">Mark as Read</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('customer.notifications.clearAll') }}" method="POST" class="inline-block" onsubmit="return confirm('Clear this notification? This cannot be undone.');">
                                            @csrf
                                            {{-- This button would ideally be for individual notification delete, not clear all --}}
                                            {{-- For a single notification delete, you'd need a specific route like `customer.notifications.destroy` --}}
                                            <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p id="noNotificationsMessage" class="text-center text-gray-500 py-8">You have no notifications for this category.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="notificationContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        // Mobile navigation functionality (reused for consistency)
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

        // Initial load functionality
        document.addEventListener('DOMContentLoaded', function() {
            // The active tab is now set directly by Blade based on the route
        });
    </script>
</body>
</html>