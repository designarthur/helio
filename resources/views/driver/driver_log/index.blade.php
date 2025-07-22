<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Driver Log (HOS)</title>
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
        .status-button.active {
            background-color: #EA3A26; /* chili-red */
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .status-button:not(.active) {
            background-color: #f3f4f6; /* gray-100 */
            color: #4b5563; /* gray-700 */
        }
        /* Dynamic log entry styling based on status */
        .log-entry.DRIVING {
            background-color: #dbeafe; /* blue-100 */
            border-left-color: #3b82f6; /* blue-500 */
        }
        .log-entry.ON_DUTY_NOT_DRIVING {
            background-color: #fffbeb; /* yellow-100 */
            border-left-color: #f59e0b; /* yellow-500 */
        }
        .log-entry.OFF_DUTY {
            background-color: #ecfdf5; /* green-50 */
            border-left-color: #10b981; /* green-500 */
        }
        .log-entry.SLEEPER_BERTH {
            background-color: #e0f2f7; /* cyan-50 */
            border-left-color: #06b6d4; /* cyan-500 */
        }
        .log-entry.violation {
            background-color: #fee2e2; /* red-100 */
            border-left-color: #ef4444; /* red-500 */
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
                    <a href="{{ route('driver.dashboard') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-tachometer-alt mr-4 text-lg"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.assigned_routes.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-route mr-4 text-lg"></i>
                        Assigned Routes
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span> {{-- Will be dynamically updated --}}
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
                    <a href="{{ route('driver.driver_log.index') }}" class="nav-link active bg-gradient-to-r from-chili-red to-tangelo border-ut-orange transform translate-x-2 flex items-center px-6 py-4 text-white transition-all duration-300 rounded-lg border-l-4 relative">
                        <i class="fas fa-hourglass-half mr-4 text-lg"></i>
                        Driver Log (HOS)
                    </a>
                </li>
                <li>
                    <a href="{{ route('driver.messages.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-comments mr-4 text-lg"></i>
                        Messages
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span> {{-- Will be dynamically updated --}}
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
            <header class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8 flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div class="text-center lg:text-left">
                    <h2 class="text-chili-red text-2xl lg:text-3xl font-bold mb-2">Driver Log (HOS)</h2>
                    <p class="text-gray-600 text-lg">Track your Hours of Service for compliance</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <a href="{{ route('driver.driver_log.past_logs') }}" class="btn-outline border-2 border-blue-500 text-blue-700 px-6 py-3 rounded-lg font-semibold hover:bg-blue-500 hover:text-white transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-book mr-2"></i>
                        View Past Logs
                    </a>
                </div>
            </header>

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

            <section class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8">
                <h3 class="flex items-center text-chili-red text-2xl font-bold mb-6">
                    <i class="fas fa-chart-line mr-3"></i>
                    Current Status & Summary
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="text-center p-4 rounded-lg bg-gray-50 border border-gray-200">
                        <div class="text-sm text-gray-500 mb-1">Current Status</div>
                        <div id="currentStatusDisplay" class="text-2xl font-bold text-chili-red">{{ $currentHOS['current_status'] }}</div>
                    </div>
                    <div class="text-center p-4 rounded-lg bg-gray-50 border border-gray-200">
                        <div class="text-sm text-gray-500 mb-1">Drive Time Remaining</div>
                        <div id="driveTimeRemaining" class="text-2xl font-bold {{ $currentHOS['drive_time_remaining_class'] ?? 'text-green-600' }}">{{ $currentHOS['drive_time_remaining'] }}</div>
                    </div>
                    <div class="text-center p-4 rounded-lg bg-gray-50 border border-gray-200">
                        <div class="text-sm text-gray-500 mb-1">Shift Time Remaining</div>
                        <div id="shiftTimeRemaining" class="text-2xl font-bold {{ $currentHOS['shift_time_remaining_class'] ?? 'text-yellow-600' }}">{{ $currentHOS['shift_time_remaining'] }}</div>
                    </div>
                    <div class="text-center p-4 rounded-lg bg-gray-50 border border-gray-200">
                        <div class="text-sm text-gray-500 mb-1">Cycle Time Remaining</div>
                        <div id="cycleTimeRemaining" class="text-2xl font-bold {{ $currentHOS['cycle_time_remaining_class'] ?? 'text-blue-600' }}">{{ $currentHOS['cycle_time_remaining'] }}</div>
                    </div>
                </div>

                <form id="statusChangeForm" action="{{ route('driver.driver_log.store') }}" method="POST" class="flex flex-wrap justify-center gap-4 mb-6">
                    @csrf
                    <input type="hidden" name="status" id="newStatusInput">
                    <button type="submit" class="status-button px-6 py-3 rounded-lg font-semibold transition-colors duration-200" data-status="OFF_DUTY">
                        <i class="fas fa-bed mr-2"></i>OFF DUTY
                    </button>
                    <button type="submit" class="status-button px-6 py-3 rounded-lg font-semibold transition-colors duration-200" data-status="SLEEPER_BERTH">
                        <i class="fas fa-moon mr-2"></i>SLEEPER BERTH
                    </button>
                    <button type="submit" class="status-button px-6 py-3 rounded-lg font-semibold transition-colors duration-200" data-status="DRIVING">
                        <i class="fas fa-truck-moving mr-2"></i>DRIVING
                    </button>
                    <button type="submit" class="status-button px-6 py-3 rounded-lg font-semibold transition-colors duration-200" data-status="ON_DUTY_NOT_DRIVING">
                        <i class="fas fa-briefcase mr-2"></i>ON DUTY (Not Driving)
                    </button>
                </form>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Current Location (Auto-detected)</label>
                        <input type="text" id="location" name="location" value="" class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks (Optional)</label>
                        <input type="text" id="remarks" name="remarks" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" placeholder="e.g., Fueling, Customer Site">
                    </div>
                </div>

                @if($currentHOS['has_violations'])
                <div id="violationWarning" class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg mt-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span id="warningMessage">
                        @foreach($currentHOS['violations_messages'] as $violation)
                            {{ $violation }}<br>
                        @endforeach
                    </span>
                </div>
                @endif
            </section>

            <section class="bg-white rounded-2xl shadow-lg p-6 lg:p-8">
                <h3 class="flex items-center text-chili-red text-2xl font-bold mb-6">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    Daily Log
                </h3>
                <div id="logEntries" class="space-y-4">
                    @forelse($dailyLogEntries as $entry)
                        @php
                            $logClass = '';
                            if ($entry->status == 'DRIVING') $logClass = 'DRIVING';
                            elseif ($entry->status == 'ON_DUTY_NOT_DRIVING') $logClass = 'ON_DUTY_NOT_DRIVING';
                            elseif ($entry->status == 'OFF_DUTY') $logClass = 'OFF_DUTY';
                            elseif ($entry->status == 'SLEEPER_BERTH') $logClass = 'SLEEPER_BERTH';
                        @endphp
                        <div class="log-entry bg-gray-50 border-l-4 border-gray-200 p-4 rounded-xl shadow-sm {{ $logClass }}">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-semibold text-gray-800">{{ str_replace('_', ' ', $entry->status) }}</span>
                                <span class="text-sm text-gray-600">
                                    {{ $entry->start_time->format('H:i A') }} - {{ $entry->end_time ? $entry->end_time->format('H:i A') : 'Now' }}
                                    ({{ $entry->duration_minutes ? $controller->formatMinutesToHoursMinutes($entry->duration_minutes) : '...' }})
                                </span>
                            </div>
                            <p class="text-gray-700 text-sm">Location: {{ $entry->location ?? 'N/A' }} @if($entry->remarks) &bull; Remarks: {{ $entry->remarks }} @endif</p>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">No log entries for today.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>

    <div id="notificationContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        // Use Blade to pass initial HOS status from controller
        let currentStatus = '{{ $currentHOS['current_status'] }}';

        // Event listeners for status buttons
        document.querySelectorAll('.status-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent form submission directly
                const newStatus = this.dataset.status;
                
                document.getElementById('newStatusInput').value = newStatus;
                document.getElementById('statusChangeForm').submit(); // Submit the form
            });
        });

        // Set initial active status button on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation state for Driver Log
            document.querySelector('.nav-link.active').classList.add('bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
            
            // Set active status button
            const initialActiveButton = document.querySelector(`.status-button[data-status="${currentStatus.replace(' ', '_')}"]`);
            if (initialActiveButton) {
                initialActiveButton.classList.add('active');
            }
            
            // Autodetect location (conceptual)
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    // In a real app, you'd use a reverse geocoding API here (e.g., Google Maps Geocoding API)
                    // For now, a dummy city
                    document.getElementById('location').value = `Auto-detected: Lat ${lat.toFixed(2)}, Lon ${lon.toFixed(2)}`;
                    // For more user-friendly:
                    // document.getElementById('location').value = 'Springfield, IL (Auto-detected)';
                }, error => {
                    console.error("Geolocation error:", error);
                    document.getElementById('location').value = 'Location unknown';
                }, { enableHighAccuracy: false, timeout: 5000, maximumAge: 0 });
            } else {
                document.getElementById('location').value = 'Geolocation not supported';
            }
        });

        // Notification system (copied from previous files for consistency)
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

        // Mobile navigation functionality (copied for consistency)
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
    </script>
</body>
</html>