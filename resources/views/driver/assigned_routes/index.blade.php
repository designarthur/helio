<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Assigned Routes</title>
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
        .stop-card.completed {
            background-color: #f0fdf4; /* Light green for completed */
            border-left-color: #22c55e; /* Tailwind green-500 */
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
                    <a href="{{ route('driver.assigned_routes.index') }}" class="nav-link active bg-gradient-to-r from-chili-red to-tangelo border-ut-orange transform translate-x-2 flex items-center px-6 py-4 text-white transition-all duration-300 rounded-lg border-l-4 relative">
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
                    <h2 class="text-chili-red text-2xl lg:text-3xl font-bold mb-2">Today's Routes</h2>
                    <p class="text-gray-600 text-lg">Manage your deliveries and pickups for today</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <button onclick="showConceptualAction('Start Navigation', 'Starting navigation for your current route...')" class="btn-primary bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-compass mr-2"></i>
                        Start Navigation
                    </button>
                    <a href="{{ route('dispatching.show', ['tab' => 'map']) }}" class="btn-outline border-2 border-chili-red text-chili-red px-6 py-3 rounded-lg font-semibold hover:bg-chili-red hover:text-white transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-map mr-2"></i>
                        View Full Route Map
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

            <div class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8">
                <h3 class="flex items-center text-chili-red text-xl font-bold mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    Route Summary
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-700 text-sm">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-semibold text-gray-600">Total Stops:</span> <span id="totalStops">0</span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-semibold text-gray-600">Estimated Duration:</span> <span id="estimatedDuration">0h 0m</span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="font-semibold text-gray-600">Estimated Mileage:</span> <span id="estimatedMileage">0 miles</span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg md:col-span-3">
                        <span class="font-semibold text-gray-600">Assigned Vehicle:</span> <span id="assignedVehicle">N/A</span>
                        <span class="ml-4 font-semibold text-gray-600">Driver:</span> <span id="assignedDriver">{{ $user->name }}</span>
                    </div>
                </div>
            </div>

            <section class="bg-white rounded-2xl shadow-lg p-6 lg:p-8">
                <h3 class="flex items-center text-chili-red text-2xl font-bold mb-6">
                    <i class="fas fa-location-dot mr-3"></i>
                    Your Stops
                </h3>
                <div class="space-y-6" id="routeStopsList">
                    @forelse($assignedBookings as $booking)
                        @php
                            $equipment = $booking->equipment;
                            $customer = $booking->customer;
                            $stopIconClass = 'fas fa-question-circle';
                            $actionType = ''; // 'delivery', 'pickup', 'service'

                            if ($equipment) {
                                if (str_contains(strtolower($equipment->type), 'dumpster')) {
                                    $stopIconClass = 'fas fa-dumpster';
                                    $actionType = 'delivery'; // Assuming all dumpster jobs are delivery/pickup
                                } elseif (str_contains(strtolower($equipment->type), 'container')) {
                                    $stopIconClass = 'fas fa-cube';
                                    $actionType = 'delivery'; // Assuming container jobs are delivery/pickup
                                } elseif (str_contains(strtolower($equipment->type), 'toilet')) {
                                    $stopIconClass = 'fas fa-restroom';
                                    $actionType = 'service'; // Assuming toilet jobs are services
                                }
                            }
                            // Default stop number
                            $stopNumber = $loop->index + 1; // Laravel loop variable provides index
                        @endphp
                        <div class="stop-card bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300" data-booking-id="{{ $booking->id }}" data-status="{{ $booking->status }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="bg-chili-red text-white text-lg font-bold w-10 h-10 flex items-center justify-center rounded-full mr-4">{{ $stopNumber }}</span>
                                    <div>
                                        <h4 class="text-xl font-bold text-gray-800">
                                            @if($actionType == 'delivery') Delivery:
                                            @elseif($actionType == 'pickup') Pickup:
                                            @elseif($actionType == 'service') Service:
                                            @endif
                                            {{ $equipment->type ?? 'N/A' }} ({{ $equipment->size ?? '' }})
                                        </h4>
                                        <p class="text-gray-600">Booking #{{ $booking->id }}</p>
                                    </div>
                                </div>
                                <span class="px-4 py-2 rounded-full text-sm font-semibold status-badge text-white
                                    @if($booking->status == 'Pending' || $booking->status == 'Confirmed') bg-yellow-500
                                    @elseif($booking->status == 'Delivered') bg-blue-500
                                    @elseif($booking->status == 'Completed') bg-green-500
                                    @elseif($booking->status == 'Cancelled') bg-red-500
                                    @endif
                                ">
                                    <i class="fas fa-truck-loading mr-2"></i>{{ $booking->status }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm text-gray-500 mb-1">Customer</div>
                                    <div class="font-semibold">{{ $customer->name ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm text-gray-500 mb-1">Contact</div>
                                    <div class="font-semibold">{{ $customer->phone ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg md:col-span-2">
                                    <div class="text-sm text-gray-500 mb-1">Address</div>
                                    <div class="font-semibold">{{ $booking->delivery_address }}</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm text-gray-500 mb-1">Equipment</div>
                                    <div class="font-semibold">{{ $equipment->type ?? 'N/A' }} (Unit #{{ $equipment->internal_id ?? 'N/A' }})</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm text-gray-500 mb-1">Due Time</div>
                                    <div class="font-semibold">{{ $booking->rental_start_date->format('H:i A') }}</div>
                                </div>
                            </div>
                            @if($booking->booking_notes)
                            <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-800 p-3 rounded-lg mb-4">
                                <i class="fas fa-info-circle mr-2"></i>Special Instructions: {{ $booking->booking_notes }}
                            </div>
                            @endif
                            <div class="flex flex-wrap gap-3 action-buttons">
                                @if(!in_array($booking->status, ['Completed', 'Cancelled']))
                                    <button onclick="showConceptualAction('Navigate', 'Navigating to stop #{{ $stopNumber }} for Booking #{{ $booking->id }}...')" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-300 flex items-center">
                                        <i class="fas fa-compass mr-2"></i>Navigate
                                    </button>
                                    <form action="{{ route('driver.assigned_routes.markArrived', $booking->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Mark Booking #{{ $booking->id }} as arrived?');">
                                        @csrf
                                        <button type="submit" class="bg-ut-orange text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition-colors duration-300 flex items-center">
                                            <i class="fas fa-flag-checkered mr-2"></i>Arrived
                                        </button>
                                    </form>
                                    <form action="{{ route('driver.assigned_routes.completeJob', $booking->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Complete Booking #{{ $booking->id }}? This will mark the job as completed and update its status.');">
                                        @csrf
                                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-300 flex items-center">
                                            <i class="fas fa-signature mr-2"></i>Complete Job
                                        </button>
                                    </form>
                                    <form action="{{ route('driver.assigned_routes.reportProblem', $booking->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Report a problem for Booking #{{ $booking->id }}? This will notify dispatch.');">
                                        @csrf
                                        <button type="submit" class="border border-red-500 text-red-500 px-4 py-2 rounded-lg hover:bg-red-500 hover:text-white transition-colors duration-300 flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>Problem
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500 text-sm italic">Job {{ $booking->status }}</span>
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="text-blue-600 hover:underline text-sm font-semibold ml-auto">View Details</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">No assigned routes or jobs for today.</p>
                    @endforelse
                </div>
            </section>
        </main>
    </div>

    {{-- Conceptual Modals --}}
    <div id="podModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-2xl relative">
            <h3 class="text-2xl font-bold text-chili-red mb-6 border-b pb-3 border-gray-200">Complete Job for Stop #<span id="podStopId"></span></h3>
            <p>This modal would typically contain forms for:</p>
            <ul class="list-disc list-inside ml-4">
                <li>Customer Signature capture (using a canvas)</li>
                <li>Photo uploads (Proof of Delivery/Pickup)</li>
                <li>Final notes/site conditions</li>
            </ul>
            <button onclick="closeConceptualModal('podModal')" class="mt-6 bg-chili-red text-white px-6 py-2 rounded-lg">Close</button>
        </div>
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

        // Conceptual actions from original HTML
        function showConceptualAction(title, message) {
            alert(`${title}: ${message}`);
        }

        function openRouteMap() {
            // This button now links to a route, this JS is for conceptual alert if no route is defined yet
            // showConceptualAction('View Full Route Map', 'Opening real-time map with route highlights...');
        }

        function openProofOfDeliveryModal(bookingId) {
            alert(`Opening Proof of Delivery/Pickup form for Booking #${bookingId}.\n(In a real app, this would open a form to capture signature, photos, notes.)`);
            // This function is still here for conceptual alerts. Actual POD submission is via form.
            // If you implement a real POD modal, replace this with openModal('podModal');
        }
        
        function closeConceptualModal(modalId) {
            document.getElementById(modalId).classList.add('opacity-0', 'invisible');
            document.getElementById(modalId).classList.remove('opacity-100', 'visible');
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

        // Initial load functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation state for Assigned Routes
            document.querySelector('.nav-link.active').classList.add('bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
        });
    </script>
</body>
</html>