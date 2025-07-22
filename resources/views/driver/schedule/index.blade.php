<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Driver Schedule</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' /> {{-- FullCalendar CSS --}}
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
        /* Custom styles for FullCalendar */
        .fc .fc-button-primary {
            background-color: #EA3A26; /* chili-red */
            border-color: #EA3A26;
        }
        .fc .fc-button-primary:hover {
            background-color: #F54F1D; /* tangelo */
            border-color: #F54F1D;
        }
        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #FF8600; /* ut-orange */
            border-color: #FF8600;
        }
        /* Event styling based on type - customize these as needed for your events */
        .fc-event.delivery {
            background-color: #3B82F6; /* blue-500 */
            border-color: #2563EB; /* blue-600 */
        }
        .fc-event.pickup {
            background-color: #F59E0B; /* amber-500 */
            border-color: #D97706; /* amber-600 */
        }
        .fc-event.service { /* For toilet service jobs */
            background-color: #8B5CF6; /* purple-500 */
            border-color: #7C3AED; /* purple-600 */
        }
        .fc-event.shift { /* For driver shifts (background event) */
            background-color: #10B981; /* green-500 */
            border-color: #059669; /* green-600 */
        }
        .fc-event.time-off { /* For time-off requests */
            background-color: #EF4444; /* red-500 */
            border-color: #DC2626; /* red-600 */
        }
        .fc-event-main-frame {
            padding: 2px;
        }
        .fc-daygrid-event-dot {
            border-color: currentColor; /* Matches event text color */
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
                    <a href="{{ route('driver.schedule.index') }}" class="nav-link active bg-gradient-to-r from-chili-red to-tangelo border-ut-orange transform translate-x-2 flex items-center px-6 py-4 text-white transition-all duration-300 rounded-lg border-l-4 relative">
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
                    <h2 class="text-chili-red text-2xl lg:text-3xl font-bold mb-2">My Schedule</h2>
                    <p class="text-gray-600 text-lg">View your shifts, assigned jobs, and manage availability</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <button onclick="openTimeOffRequestModal()" class="btn-primary bg-gradient-to-r from-red-500 to-rose-500 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-calendar-minus mr-2"></i>
                        Request Time Off
                    </button>
                    <button onclick="toggleAvailability()" id="availabilityToggleBtn" class="btn-outline border-2 border-green-500 text-green-700 px-6 py-3 rounded-lg font-semibold hover:bg-green-500 hover:text-white transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-toggle-on mr-2"></i>
                        Set Available
                    </button>
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

            <section class="bg-white rounded-2xl shadow-lg p-6 lg:p-8">
                <div id="calendar" class="h-auto"></div>
            </section>
        </main>
    </div>

    {{-- Time Off Request Modal --}}
    <div id="timeOffModal" class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center opacity-0 invisible transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-screen overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-chili-red">Request Time Off</h3>
                    <button onclick="closeConceptualModal('timeOffModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="timeOffForm" action="{{ route('driver.schedule.submitTimeOffRequest') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="timeOffStartDate" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" id="timeOffStartDate" name="start_date" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" required value="{{ old('start_date') }}">
                        @error('start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="timeOffEndDate" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" id="timeOffEndDate" name="end_date" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" required value="{{ old('end_date') }}">
                        @error('end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-6">
                        <label for="timeOffReason" class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                        <textarea id="timeOffReason" name="reason" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" placeholder="e.g., Vacation, Medical Leave, Personal Appointment">{{ old('reason') }}</textarea>
                        @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeConceptualModal('timeOffModal')" class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors duration-300">Cancel</button>
                        <button type="submit" class="bg-chili-red text-white px-6 py-3 rounded-lg hover:bg-chili-red-2 transition-colors duration-300">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Event Detail Modal --}}
    <div id="eventDetailModal" class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center opacity-0 invisible transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-screen overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-chili-red" id="eventDetailTitle">Event Details</h3>
                    <button onclick="closeConceptualModal('eventDetailModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6" id="eventDetailContent">
                </div>
        </div>
    </div>


    <div id="notificationContainer" class="fixed top-4 right-4 z-50"></div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script> {{-- FullCalendar JS --}}
    <script>
        let calendar; // Global calendar instance
        let isAvailable = true; // Initial availability status (conceptual for button)

        // --- FullCalendar Initialization ---
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: false, // Drivers shouldn't drag/drop events
                selectable: true,
                eventClick: function(info) {
                    openEventDetailModal(info.event);
                },
                dateClick: function(info) {
                    // console.log('Date clicked: ' + info.dateStr);
                    // Optional: open a modal to request a new time off for that date
                    // For example: openTimeOffRequestModal(info.dateStr);
                },
                events: @json($events), // Pass events array from controller to JS
                eventDidMount: function(info) {
                    // Optional: customize event rendering or add tooltips
                },
                // Responsive options for calendar view
                views: {
                    dayGridMonth: {
                        dayMaxEvents: true // popover will appear if more than this many events are in a day
                    },
                    timeGridWeek: {
                        dayMaxEvents: 3 // adjust to your preference
                    },
                    timeGridDay: {
                        dayMaxEvents: 3 // adjust to your preference
                    }
                }
            });
            calendar.render();
            updateAvailabilityToggle(); // Set initial button state based on JS variable
        });

        // --- Availability Toggle Functionality ---
        function toggleAvailability() {
            isAvailable = !isAvailable;
            updateAvailabilityToggle();
            if (isAvailable) {
                showNotification('Your availability is now set to Available.', 'success');
            } else {
                showNotification('Your availability is now set to Unavailable.', 'info');
            }
            // In a real app, send update to backend via AJAX or form submission
        }

        function updateAvailabilityToggle() {
            const btn = document.getElementById('availabilityToggleBtn');
            btn.innerHTML = `<i class="fas ${isAvailable ? 'fa-toggle-on' : 'fa-toggle-off'} mr-2"></i> ${isAvailable ? 'Set Unavailable' : 'Set Available'}`;
            if (isAvailable) {
                btn.classList.remove('border-red-500', 'text-red-700', 'hover:bg-red-500', 'hover:text-white');
                btn.classList.add('border-green-500', 'text-green-700', 'hover:bg-green-500', 'hover:text-white');
            } else {
                btn.classList.remove('border-green-500', 'text-green-700', 'hover:bg-green-500', 'hover:text-white');
                btn.classList.add('border-red-500', 'text-red-700', 'hover:bg-red-500', 'hover:text-white');
            }
        }

        // --- Time Off Request Modal Functions ---
        function openTimeOffRequestModal() {
            document.getElementById('timeOffForm').reset();
            // Pre-fill dates if a date was clicked, e.g., if you added a date parameter to this function
            document.getElementById('timeOffModal').classList.remove('opacity-0', 'invisible');
            document.getElementById('timeOffModal').classList.add('opacity-100', 'visible');
        }

        function closeConceptualModal(modalId) {
            document.getElementById(modalId).classList.add('opacity-0', 'invisible');
            document.getElementById(modalId).classList.remove('opacity-100', 'visible');
        }

        // --- Event Details Modal Functions ---
        function openEventDetailModal(event) {
            const modalTitle = document.getElementById('eventDetailTitle');
            const modalContent = document.getElementById('eventDetailContent');
            
            modalTitle.textContent = event.title;

            let contentHtml = `<p class="text-gray-700 mb-2"><strong>Type:</strong> ${event.extendedProps.type.charAt(0).toUpperCase() + event.extendedProps.type.slice(1).replace('-', ' ')}</p>`;

            if (event.start) {
                contentHtml += `<p class="text-gray-700 mb-2"><strong>Start:</strong> ${new Date(event.start).toLocaleString()}</p>`;
            }
            if (event.end && !event.allDay) {
                 contentHtml += `<p class="text-gray-700 mb-2"><strong>End:</strong> ${new Date(event.end).toLocaleString()}</p>`;
            } else if (event.end && event.allDay) {
                // For all-day events, FullCalendar end is exclusive. Adjust for display.
                const adjustedEndDate = new Date(new Date(event.end).getTime() - (24 * 60 * 60 * 1000));
                contentHtml += `<p class="text-gray-700 mb-2"><strong>End:</strong> ${adjustedEndDate.toLocaleDateString()}</p>`;
            }
            // Add specific details based on event type
            if (event.extendedProps.type === 'delivery' || event.extendedProps.type === 'pickup' || event.extendedProps.type === 'service') {
                contentHtml += `<p class="text-gray-700 mb-2"><strong>Customer:</strong> ${event.extendedProps.customer || 'N/A'}</p>`;
                contentHtml += `<p class="text-gray-700 mb-2"><strong>Address:</strong> ${event.extendedProps.address || 'N/A'}</p>`;
                contentHtml += `<p class="text-gray-700 mb-2"><strong>Details:</strong> ${event.extendedProps.details || 'N/A'}</p>`;
            } else if (event.extendedProps.type === 'time-off') {
                 contentHtml += `<p class="text-gray-700 mb-2"><strong>Reason:</strong> ${event.extendedProps.reason || 'N/A'}</p>`;
                 if (event.extendedProps.status) {
                    contentHtml += `<p class="text-gray-700 mb-2"><strong>Status:</strong> ${event.extendedProps.status.charAt(0).toUpperCase() + event.extendedProps.status.slice(1).replace('-', ' ')}</p>`;
                 }
            } else if (event.extendedProps.type === 'shift') {
                 contentHtml += `<p class="text-gray-700 mb-2"><strong>Details:</strong> ${event.extendedProps.details || 'N/A'}</p>`;
            }

            modalContent.innerHTML = contentHtml;
            openModal('eventDetailModal');
        }

        // Notification system (copied for consistency)
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

        // Initial load functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation state for Schedule
            document.querySelector('.nav-link.active').classList.add('bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
        });
    </script>
</body>
</html>