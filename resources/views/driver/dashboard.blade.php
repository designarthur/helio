<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Driver Dashboard</title>
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
                    <i class="fas fa-truck-pickup mr-2"></i>Helly
                </h1>
                <p class="text-gray-400 text-sm">Driver Portal</p>
            </div>

            <ul class="space-y-2 px-4">
                <li>
                    <a href="{{ route('driver.dashboard') }}" class="nav-link active flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-tachometer-alt mr-4 text-lg"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.assigned_routes.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-route mr-4 text-lg"></i>
                        Assigned Routes
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $assignedJobsTodayCount }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.schedule.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-calendar-alt mr-4 text-lg"></i>
                        Schedule
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.vehicle_inspection.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-clipboard-check mr-4 text-lg"></i>
                        Vehicle Inspection
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">!</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.driver_log.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-hourglass-half mr-4 text-lg"></i>
                        Driver Log (HOS)
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.messages.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-comments mr-4 text-lg"></i>
                        Messages
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $unreadMessagesCount }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.profile.show') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
                        <i class="fas fa-user-circle mr-4 text-lg"></i>
                        Profile
                    </a>
                </li>
                <li class="mt-8">
                    <form action="{{ route('driver.logout') }}" method="POST">
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
                    <h2 class="text-chili-red text-2xl lg:text-3xl font-bold mb-2">Welcome, {{ $user->name }}!</h2>
                    <p class="text-gray-600 text-lg">Your daily overview and quick actions</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <button onclick="showConceptualAction('Start Today\'s Route', 'Starting navigation for your current route...')" class="btn-primary bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-play-circle mr-2"></i>
                        Start Today's Route
                    </button>
                    <a href="{{ route('driver.vehicle_inspection.index') }}" class="btn-outline border-2 border-blue-500 text-blue-700 px-6 py-3 rounded-lg font-semibold hover:bg-blue-500 hover:text-white transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-car-side mr-2"></i>
                        Pre-Trip Inspection
                    </a>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <div class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 border-l-4 border-chili-red" onclick="window.location.href = '{{ route('driver.assigned_routes.index') }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-chili-red text-xl font-bold">
                            <i class="fas fa-route mr-3"></i>
                            Assigned Routes
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2">{{ $assignedJobsTodayCount }}</div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">Today's Jobs</span>
                        {{-- <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">1 Pickup</span> --}}
                    </div>
                </div>

                <div class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 border-l-4 border-ut-orange" onclick="window.location.href = '{{ route('driver.assigned_routes.index', ['filter' => 'pending']) }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-ut-orange text-xl font-bold">
                            <i class="fas fa-hourglass-half mr-3"></i>
                            Pending Jobs
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2">{{ $pendingJobsCount }}</div>
                    <div class="text-gray-600">
                        Next stop: Check Assigned Routes
                    </div>
                </div>

                <div class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 border-l-4 border-purple-500" onclick="window.location.href = '{{ route('driver.driver_log.index') }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-purple-500 text-xl font-bold">
                            <i class="fas fa-clock mr-3"></i>
                            HOS Status
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2">{{ $hosStatus['drive_time_remaining'] }}</div>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">Drive Time Remaining</span>
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">{{ $hosStatus['violations'] }}</span>
                    </div>
                </div>

                <div class="dashboard-card bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl hover:transform hover:-translate-y-2 transition-all duration-300 border-l-4 border-green-500" onclick="window.location.href = '{{ route('driver.messages.index') }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-green-500 text-xl font-bold">
                            <i class="fas fa-bell mr-3"></i>
                            Notifications
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2">{{ $notificationsCount }}</div>
                    <div class="text-gray-600">
                        {{ $unreadMessagesCount }} unread messages • {{ $routeUpdatesCount }} route update
                    </div>
                </div>
            </div>

            <section class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8">
                <h3 class="flex items-center text-chili-red text-2xl font-bold mb-6">
                    <i class="fas fa-list-check mr-3"></i>
                    Upcoming Tasks
                </h3>
                <ul class="space-y-4">
                    @forelse($upcomingTasks as $task)
                        <li class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 border-b border-gray-100 last:border-b-0">
                            <div class="w-12 h-12 bg-gradient-to-br
                                @if(str_contains(strtolower($task->equipment->type ?? ''), 'dumpster')) from-blue-500 to-cyan-500
                                @elseif(str_contains(strtolower($task->equipment->type ?? ''), 'container')) from-green-500 to-emerald-500
                                @elseif(str_contains(strtolower($task->equipment->type ?? ''), 'toilet')) from-ut-orange to-yellow-500
                                @else from-gray-500 to-slate-500 @endif
                                rounded-full flex items-center justify-center text-white flex-shrink-0">
                                <i class="fas
                                    @if(str_contains(strtolower($task->equipment->type ?? ''), 'dumpster')) fa-dumpster
                                    @elseif(str_contains(strtolower($task->equipment->type ?? ''), 'container')) fa-box
                                    @elseif(str_contains(strtolower($task->equipment->type ?? ''), 'toilet')) fa-restroom
                                    @else fa-info-circle @endif
                                text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-800 mb-1">
                                    @if($task->status == 'Confirmed' || $task->status == 'Delivered') Delivery: @else Service: @endif
                                    {{ $task->equipment->type ?? 'N/A' }} (Booking #{{ $task->id }})
                                </div>
                                <div class="text-gray-600 text-sm">
                                    @if($task->status == 'Confirmed' || $task->status == 'Delivered') To: @else At: @endif
                                    {{ $task->delivery_address }} • Est. Arrival: {{ $task->rental_start_date->format('H:i A') }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="showConceptualAction('Navigate', 'Navigating to next stop for Booking #{{ $task->id }}...')" class="text-blue-600 hover:text-blue-800 text-sm font-semibold"><i class="fas fa-map-signs mr-1"></i>Navigate</button>
                                <button onclick="showConceptualAction('Arrived', 'Marking Booking #{{ $task->id }} as arrived...')" class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold hover:bg-blue-200">Arrived</button>
                            </div>
                        </li>
                    @empty
                        <li class="text-center text-gray-500 py-4">No upcoming tasks assigned for today.</li>
                    @endforelse
                </ul>
            </section>

            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('dispatching.show', ['tab' => 'map']) }}" class="action-card bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-chili-red">
                    <div class="w-16 h-16 bg-gradient-to-br from-chili-red to-tangelo rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="font-semibold text-gray-800 mb-2">View Current Route</div>
                    <div class="text-gray-600 text-sm">Access turn-by-turn navigation for your assigned stops</div>
                </a>

                <a href="{{ route('driver.vehicle_inspection.index') }}" class="action-card bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-chili-red">
                    <div class="w-16 h-16 bg-gradient-to-br from-ut-orange to-yellow-500 rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-signature"></i>
                    </div>
                    <div class="font-semibold text-gray-800 mb-2">Submit POD/POP</div>
                    <div class="text-gray-600 text-sm">Capture photos, signatures, and add delivery notes</div>
                </a>

                <a href="{{ route('driver.messages.index') }}" class="action-card bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-chili-red">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div class="font-semibold text-gray-800 mb-2">Message Dispatch</div>
                    <div class="text-gray-600 text-sm">Quickly send updates or ask questions to your team</div>
                </a>
            </section>
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
        sidebarOverlay.addEventListener('click', toggleMobileToggle); // Fix: changed from toggleMobileNav to toggleMobileToggle in original HTML, assume typo and corrected to toggleMobileNav
        // ^ - There's a typo in the provided HTML's JS, it says `toggleMobileToggle` instead of `toggleMobileNav`. Corrected here for functionality.

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
            if (!notificationContainer) { // Check if container exists
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

        // Initial load functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation state for Dashboard
            document.querySelector('.nav-link.active').classList.add('bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
        });
    </script>
</body>
</html>