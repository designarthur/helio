<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Driver Profile: {{ $user->name }}</title>
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
                    <a href="{{ route('driver.profile.show') }}" class="nav-link active bg-gradient-to-r from-chili-red to-tangelo border-ut-orange transform translate-x-2 flex items-center px-6 py-4 text-white transition-all duration-300 rounded-lg border-l-4 relative">
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
            <header class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                    <div>
                        <h2 class="text-chili-red text-3xl lg:text-4xl font-bold mb-2">My Profile</h2>
                        <p class="text-gray-600 text-lg">View and update your personal and driving information</p>
                    </div>
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-chili-red text-2xl font-bold flex items-center">
                            <i class="fas fa-user mr-3"></i>Personal Information
                        </h3>
                        <button onclick="openEditProfileModal('personal')" class="text-blue-600 hover:text-blue-800 font-semibold">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                    <div class="space-y-3 text-gray-700">
                        <div><span class="font-semibold text-gray-600">Name:</span> <span id="profileName">{{ $user->name }}</span></div>
                        <div><span class="font-semibold text-gray-600">Email:</span> <span id="profileEmail">{{ $user->email }}</span></div>
                        <div><span class="font-semibold text-gray-600">Phone:</span> <span id="profilePhone">{{ $user->phone ?? 'N/A' }}</span></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-chili-red text-2xl font-bold flex items-center">
                            <i class="fas fa-id-card-alt mr-3"></i>License & Certifications
                        </h3>
                        <button onclick="openEditProfileModal('license')" class="text-blue-600 hover:text-blue-800 font-semibold">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                    </div>
                    <div class="space-y-3 text-gray-700">
                        <div><span class="font-semibold text-gray-600">License No.:</span> <span id="licenseNumber">{{ $user->license_number ?? 'N/A' }}</span></div>
                        <div><span class="font-semibold text-gray-600">Expiration:</span> <span id="licenseExpiry">{{ $user->license_expiry ? $user->license_expiry->format('Y-m-d') : 'N/A' }}</span></div>
                        <div><span class="font-semibold text-gray-600">Class:</span> <span id="licenseClass">{{ $user->cdl_class ?? 'N/A' }}</span></div>
                        <div><span class="font-semibold text-gray-600">Certifications:</span> <span id="certifications">{{ is_array($user->certifications) ? implode(', ', $user->certifications) : ($user->certifications ?? 'N/A') }}</span></div>
                    </div>
                </div>
            </div>

             <div class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-chili-red text-2xl font-bold flex items-center">
                        <i class="fas fa-comments mr-3"></i>Contact Preferences
                    </h3>
                    <button onclick="showConceptualAction('Contact Preferences', 'Managing contact preferences is a conceptual feature.')" class="text-blue-600 hover:text-blue-800 font-semibold">
                        <i class="fas fa-cog mr-1"></i>Manage
                    </button>
                </div>
                <div class="space-y-3 text-gray-700">
                    <div><span class="font-semibold text-gray-600">Preferred Notification:</span> <span id="prefNotification">SMS & In-App Alerts (Conceptual)</span></div>
                    <div><span class="font-semibold text-gray-600">Emergency Contact:</span> <span id="emergencyContact">Not Configured (Conceptual)</span></div>
                </div>
            </div>
        </main>
    </div>

    {{-- Edit Profile Modal --}}
    <div id="editProfileModal" class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop z-50 flex items-center justify-center opacity-0 invisible transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-screen overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold text-chili-red" id="editProfileModalTitle">Edit Personal Information</h3>
                    <button onclick="closeConceptualModal('editProfileModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="profileForm" method="POST" action=""> {{-- Action set by JS --}}
                    @csrf
                    @method('POST') {{-- Method will be POST, type will indicate actual update route --}}
                    <input type="hidden" id="editProfileType" name="type"> {{-- 'personal' or 'license' --}}
                    
                    <div id="personalFields" class="space-y-4">
                        <div>
                            <label for="editName" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" id="editName" name="name" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" required value="{{ old('name', $user->name) }}">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="editEmail" name="email" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" required value="{{ old('email', $user->email) }}">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="editPhone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="editPhone" name="phone" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" value="{{ old('phone', $user->phone) }}">
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="editPassword" class="block text-sm font-medium text-gray-700 mb-2">New Password (Leave blank to keep current)</label>
                            <input type="password" id="editPassword" name="password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red">
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="editPasswordConfirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" id="editPasswordConfirmation" name="password_confirmation" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red">
                            @error('password_confirmation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div id="licenseFields" class="space-y-4 hidden">
                        <div>
                            <label for="editLicenseNumber" class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                            <input type="text" id="editLicenseNumber" name="license_number" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" value="{{ old('license_number', $user->license_number) }}">
                            @error('license_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="editLicenseExpiry" class="block text-sm font-medium text-gray-700 mb-2">Expiration Date</label>
                            <input type="date" id="editLicenseExpiry" name="license_expiry" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" value="{{ old('license_expiry', $user->license_expiry ? $user->license_expiry->format('Y-m-d') : '') }}">
                            @error('license_expiry')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="editCdlClass" class="block text-sm font-medium text-gray-700 mb-2">CDL Class (Optional):</label>
                            <input type="text" id="editCdlClass" name="cdl_class" placeholder="Class A, B, C" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red" value="{{ old('cdl_class', $user->cdl_class) }}">
                            @error('cdl_class')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="editCertifications" class="block text-sm font-medium text-gray-700 mb-2">Certifications (comma-separated)</label>
                            <textarea id="editCertifications" name="certifications" rows="2" placeholder="e.g., HazMat, Tanker" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red resize-y">{{ old('certifications', is_array($user->certifications) ? implode(', ', $user->certifications) : $user->certifications) }}</textarea>
                            @error('certifications')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" onclick="closeConceptualModal('editProfileModal')" class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-colors duration-300">Cancel</button>
                        <button type="submit" class="bg-chili-red text-white px-6 py-3 rounded-lg hover:bg-chili-red-2 transition-colors duration-300">Save Changes</button>
                    </div>
                </form>
            </div>
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

        // Conceptual actions
        function showConceptualAction(title, message) {
            alert(`${title}: ${message}`);
        }
        function closeConceptualModal(modalId) {
            document.getElementById(modalId).classList.add('opacity-0', 'invisible');
            document.getElementById(modalId).classList.remove('opacity-100', 'visible');
        }

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

        // --- Profile Specific Logic ---
        function openEditProfileModal(type) {
            const modal = document.getElementById('editProfileModal');
            document.getElementById('editProfileType').value = type;

            document.getElementById('personalFields').classList.add('hidden');
            document.getElementById('licenseFields').classList.add('hidden');
            document.getElementById('profileForm').action = ''; // Clear action, set by JS

            if (type === 'personal') {
                document.getElementById('editProfileModalTitle').textContent = 'Edit Personal Information';
                document.getElementById('personalFields').classList.remove('hidden');
                document.getElementById('profileForm').action = '{{ route('driver.profile.updatePersonal') }}';
                // Pre-fill values using current $user data from Blade
                document.getElementById('editName').value = "{{ $user->name }}";
                document.getElementById('editEmail').value = "{{ $user->email }}";
                document.getElementById('editPhone').value = "{{ $user->phone ?? '' }}";
            } else if (type === 'license') {
                document.getElementById('editProfileModalTitle').textContent = 'Edit License & Certifications';
                document.getElementById('licenseFields').classList.remove('hidden');
                document.getElementById('profileForm').action = '{{ route('driver.profile.updateLicense') }}';
                // Pre-fill values
                document.getElementById('editLicenseNumber').value = "{{ $user->license_number ?? '' }}";
                document.getElementById('editLicenseExpiry').value = "{{ $user->license_expiry ? $user->license_expiry->format('Y-m-d') : '' }}";
                document.getElementById('editCdlClass').value = "{{ $user->cdl_class ?? '' }}";
                document.getElementById('editCertifications').value = "{{ is_array($user->certifications) ? implode(', ', $user->certifications) : ($user->certifications ?? '') }}";
            }

            openModal('editProfileModal');
        }

        // Initial load functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation state for Profile
            document.querySelector('.nav-link.active').classList.add('bg-gradient-to-r', 'from-chili-red', 'to-tangelo', 'border-ut-orange', 'transform', 'translate-x-2');
        });
    </script>
</body>
</html>