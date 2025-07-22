<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - My Quotes</title>
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
        .modal-backdrop {
            backdrop-filter: blur(4px);
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
                    <a href="{{ route('customer.quotes.index') }}" class="nav-link active bg-gradient-to-r from-chili-red to-tangelo border-ut-orange transform translate-x-2 flex items-center px-6 py-4 text-white transition-all duration-300 rounded-lg border-l-4 relative">
                        <i class="fas fa-quote-left mr-4 text-lg"></i>
                        Quotes
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $pendingCount }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('customer.notifications.index') }}" class="nav-link flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange relative">
                        <i class="fas fa-bell mr-4 text-lg"></i>
                        Notifications
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span> {{-- Will be dynamically updated --}}
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
                        <h1 class="text-chili-red text-3xl lg:text-4xl font-bold mb-2">My Quotes</h1>
                        <p class="text-gray-600 text-lg">Review and manage your equipment rental quotes</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <a href="{{ route('customer.quotes.create') }}" class="bg-gradient-to-r from-chili-red to-tangelo text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Request New Quote
                        </a>
                    </div>
                </div>
            </header>

            <div class="bg-white rounded-2xl shadow-lg mb-8">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6 lg:px-8">
                        <a href="{{ route('customer.quotes.index', ['filter' => 'all']) }}" id="allQuotesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'all' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            All Quotes
                            <span class="ml-2 bg-chili-red text-white text-xs px-2 py-1 rounded-full">{{ $allCount }}</span>
                        </a>
                        <a href="{{ route('customer.quotes.index', ['filter' => 'pending']) }}" id="pendingQuotesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'pending' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Pending
                            <span class="ml-2 bg-ut-orange text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                        </a>
                        <a href="{{ route('customer.quotes.index', ['filter' => 'accepted']) }}" id="acceptedQuotesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'accepted' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Accepted
                            <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">{{ $acceptedCount }}</span>
                        </a>
                        <a href="{{ route('customer.quotes.index', ['filter' => 'expired']) }}" id="expiredQuotesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'expired' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Expired
                            <span class="ml-2 bg-gray-500 text-white text-xs px-2 py-1 rounded-full">{{ $expiredCount }}</span>
                        </a>
                        <a href="{{ route('customer.quotes.index', ['filter' => 'rejected']) }}" id="rejectedQuotesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'rejected' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Rejected
                            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $rejectedCount }}</span>
                        </a>
                    </nav>
                </div>

                <div id="tabContent" class="p-6 lg:p-8">
                    <div class="space-y-6" id="quoteListContent">
                        @forelse($quotes as $quote)
                            @php
                                $statusClass = '';
                                $statusBg = '';
                                $statusText = $quote->status;
                                $iconClass = 'fas fa-quote-left'; // Default icon
                                
                                if ($quote->status == 'Accepted') {
                                    $statusClass = 'bg-green-100 border-green-200 from-green-50 to-emerald-50';
                                    $statusBg = 'bg-green-500';
                                    $iconClass = 'fas fa-check-circle';
                                } elseif ($quote->status == 'Pending' || $quote->status == 'Draft' || $quote->status == 'Sent') {
                                    $statusClass = 'bg-yellow-100 border-yellow-200 from-yellow-50 to-amber-50';
                                    $statusBg = 'bg-ut-orange'; // UT orange
                                    $statusText = $quote->status == 'Pending' ? 'Pending Review' : $quote->status;
                                    $iconClass = 'fas fa-clock';
                                } elseif ($quote->status == 'Expired' || $quote->status == 'Rejected') {
                                    $statusClass = 'bg-gray-100 border-gray-200 from-gray-50 to-slate-50';
                                    $statusBg = 'bg-gray-500';
                                    $iconClass = 'fas fa-hourglass-end';
                                }
                            @endphp
                            <div class="dashboard-card bg-gradient-to-r {{ $statusClass }} rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4">
                                    <div class="flex items-center mb-4 lg:mb-0">
                                        <div class="w-12 h-12 {{ $statusBg }} rounded-lg flex items-center justify-center text-white mr-4">
                                            <i class="{{ $iconClass }} text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-800">Quote #{{ $quote->id }} - {{ $quote->items[0]['description'] ?? 'N/A' }}</h3>
                                            <p class="text-gray-600">Requested: {{ $quote->quote_date->format('M d, Y') }} @if($quote->status == 'Accepted') • Accepted: {{ $quote->updated_at->format('M d, Y') }} @elseif($quote->expiry_date) • Expires: {{ $quote->expiry_date->format('M d, Y') }} @endif</p>
                                        </div>
                                    </div>
                                    <span class="{{ $statusBg }} text-white px-4 py-2 rounded-full text-sm font-semibold">
                                        <i class="{{ $iconClass }} mr-2"></i>{{ $statusText }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                    <div class="bg-white p-4 rounded-lg">
                                        <div class="text-sm text-gray-500 mb-1">Items Quoted</div>
                                        <div class="font-semibold">{{ count($quote->items) }} Item(s)</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg">
                                        <div class="text-sm text-gray-500 mb-1">Delivery Location</div>
                                        <div class="font-semibold">{{ $quote->customer->billing_address ?? 'N/A' }}</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-lg">
                                        <div class="text-sm text-gray-500 mb-1">Estimated Total</div>
                                        <div class="font-semibold text-xl text-chili-red">${{ number_format($quote->total_amount, 2) }}</div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('customer.quotes.show', $quote->id) }}" class="bg-chili-red text-white px-4 py-2 rounded-lg hover:bg-chili-red-2 transition-colors duration-300 flex items-center">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </a>
                                    @if(in_array($quote->status, ['Draft', 'Sent']))
                                        <form action="{{ route('customer.quotes.accept', $quote->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to accept Quote #{{ $quote->id }}? This will notify the vendor and initiate the booking process.');">
                                            @csrf
                                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors duration-300 flex items-center">
                                                <i class="fas fa-check mr-2"></i>Accept Quote
                                            </button>
                                        </form>
                                        <form action="{{ route('customer.quotes.reject', $quote->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to reject Quote #{{ $quote->id }}?');">
                                            @csrf
                                            <button type="submit" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors duration-300 flex items-center">
                                                <i class="fas fa-times mr-2"></i>Reject
                                            </button>
                                        </form>
                                        <button onclick="showConceptualAction('Request Revision', 'Requesting revision for Quote #{{ $quote->id }}. The vendor will contact you.')" class="border border-blue-300 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors duration-300 flex items-center">
                                            <i class="fas fa-pencil-alt mr-2"></i>Request Revision
                                        </button>
                                    @elseif($quote->status == 'Accepted')
                                        @if($quote->linked_booking_id)
                                            <a href="{{ route('customer.bookings.show', $quote->linked_booking_id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-300 flex items-center">
                                                <i class="fas fa-calendar-check mr-2"></i>View Booking
                                            </a>
                                        @else
                                             <button onclick="showConceptualAction('View Booking', 'Booking not yet created for Quote #{{ $quote->id }}. Please contact vendor.')" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-300 flex items-center">
                                                <i class="fas fa-calendar-check mr-2"></i>View Booking
                                            </button>
                                        @endif
                                    @elseif(in_array($quote->status, ['Expired', 'Rejected']))
                                        <a href="{{ route('customer.quotes.create') }}" class="border border-chili-red text-chili-red px-4 py-2 rounded-lg hover:bg-chili-red hover:text-white transition-colors duration-300 flex items-center">
                                            <i class="fas fa-redo mr-2"></i>Request New Quote
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8">No quotes found for this category.</p>
                        @endforelse
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-4">
                        {{ $quotes->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Conceptual Service Request Modal (from original HTML) --}}
    <div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center opacity-0 invisible transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-chili-red">Request Service (Conceptual)</h3>
                    <button onclick="closeConceptualModal('serviceModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 text-gray-700">
                <p class="mb-4">This modal would contain dynamic forms for different service requests (e.g., swap, pickup, extend, relocate, extra cleaning), pre-filled with booking details.</p>
                <p><strong>Service Type:</strong> <span id="conceptualServiceType"></span></p>
                <p><strong>Booking ID:</strong> <span id="conceptualServiceBookingId"></span></p>
                <div class="mt-6 flex justify-end">
                    <button onclick="closeConceptualModal('serviceModal')" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</button>
                </div>
            </div>
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

        // Add interactive functionality (Conceptual actions)
        function showConceptualAction(title, message) {
            alert(`${title}: ${message}`);
        }
        function closeConceptualModal(modalId) {
            document.getElementById(modalId).classList.add('opacity-0', 'invisible');
            document.getElementById(modalId).classList.remove('opacity-100', 'visible');
        }

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

        // Set active navigation state (assuming this script loads on each page)
        document.addEventListener('DOMContentLoaded', function() {
            // The active class is now set dynamically by Blade based on the route
        });
    </script>
</body>
</html>