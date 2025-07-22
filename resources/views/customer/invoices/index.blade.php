<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - My Invoices</title>
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
                    <a href="{{ route('customer.invoices.index') }}" class="nav-link active bg-gradient-to-r from-chili-red to-tangelo border-ut-orange transform translate-x-2 flex items-center px-6 py-4 text-white transition-all duration-300 rounded-lg border-l-4 relative">
                        <i class="fas fa-file-invoice mr-4 text-lg"></i>
                        Invoices
                        <span class="absolute -top-1 -right-1 bg-custom-red-2 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $pendingCount + $overdueCount }}</span>
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
                        <button type="submit" class="nav-link w-full text-left flex items-center px-6 py-4 text-white hover:bg-gradient-to-r hover:from-chili-red hover:to-tangelo hover:transform hover:-translate-x-2 transition-all duration-300 rounded-lg border-l-4 border-transparent hover:border-ut-orange">
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
                        <h1 class="text-chili-red text-3xl lg:text-4xl font-bold mb-2">My Invoices</h1>
                        <p class="text-gray-600 text-lg">View and manage your past and pending invoices</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <button onclick="showConceptualAction('Export Invoices', 'Generating invoice report...')" class="bg-gradient-to-r from-chili-red to-tangelo text-white px-6 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-file-export mr-2"></i>
                            Export Invoices
                        </button>
                    </div>
                </div>
            </header>

            <div class="bg-white rounded-2xl shadow-lg mb-8">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6 lg:px-8">
                        <a href="{{ route('customer.invoices.index', ['filter' => 'all']) }}" id="allInvoicesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'all' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            All Invoices
                            <span class="ml-2 bg-chili-red text-white text-xs px-2 py-1 rounded-full">{{ $allCount }}</span>
                        </a>
                        <a href="{{ route('customer.invoices.index', ['filter' => 'pending']) }}" id="pendingInvoicesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'pending' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Pending
                            <span class="ml-2 bg-ut-orange text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                        </a>
                        <a href="{{ route('customer.invoices.index', ['filter' => 'overdue']) }}" id="overdueInvoicesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'overdue' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Overdue
                            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $overdueCount }}</span>
                        </a>
                        <a href="{{ route('customer.invoices.index', ['filter' => 'paid']) }}" id="paidInvoicesTab" class="tab-button py-4 px-2 border-b-2 font-semibold {{ $filter == 'paid' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            Paid
                            <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">{{ $paidCount }}</span>
                        </a>
                    </nav>
                </div>

                <div class="p-6 lg:p-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance Due</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="invoiceTableBody">
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->issue_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->due_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">${{ number_format($invoice->balance_due, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($invoice->status == 'Paid') bg-green-100 text-green-800
                                                @elseif($invoice->status == 'Partially Paid') bg-ut-orange-100 text-ut-orange-800 {{-- Custom color, adjust if needed --}}
                                                @elseif($invoice->status == 'Overdue') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif
                                            ">{{ $invoice->status }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('customer.invoices.show', $invoice->id) }}" class="text-chili-red hover:text-chili-red-2 mr-3">View</a>
                                            @if($invoice->balance_due > 0)
                                                <a href="{{ route('customer.payment_methods.index', ['invoice_id' => $invoice->id, 'amount' => $invoice->balance_due]) }}" class="bg-chili-red text-white px-3 py-1 rounded-md text-xs hover:bg-chili-red-2">Pay Now</a>
                                            @endif
                                            <button onclick="showConceptualAction('Download Invoice', 'Downloading invoice PDF...')" class="text-gray-500 hover:text-gray-700 ml-3">Download</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No invoices found for this category.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
        // Tab switching functionality
        function switchTab(tabName) {
            // This client-side switch is overridden by Laravel's route-based filtering.
            // The active state is set by Blade based on the 'filter' variable.
            console.log(`Attempted client-side tab switch to: ${tabName}. Laravel handles routing.`);
        }

        // Conceptual actions from original HTML
        function showConceptualAction(title, message) {
            alert(`${title}: ${message}`);
        }

        function openInvoiceDetailsModal(invoiceId) {
            alert(`Opening invoice details for ${invoiceId}.\n(In a real app, this would fetch and display data from your backend.)`);
            // This function from the original HTML will be replaced by direct Laravel route link.
        }

        function payInvoice(invoiceId, amount) {
            alert(`Initiating payment for Invoice #${invoiceId} of $${amount.toFixed(2)}.\n(In a real app, this would redirect to payment gateway or dedicated payment form.)`);
            // This function will be replaced by a link to payment methods.
        }

        function downloadInvoice(invoiceId) {
            alert(`Downloading Invoice #${invoiceId} PDF.\n(In a real app, this would trigger a backend PDF generation and download.)`);
        }

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
            // No client-side rendering functions needed here, as Laravel Blade renders the table.
            // The active tab is set directly by Blade: {{ $filter == 'all' ? 'border-chili-red text-chili-red' : 'border-transparent text-gray-500 hover:text-gray-700' }}
        });
    </script>
</body>
</html>