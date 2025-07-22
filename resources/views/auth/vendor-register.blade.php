<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Vendor Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'chili-red': '#EA3A26',
                        'chili-red-2': '#EA3D2A',
                        'chili-red-3': '#EA3D24',
                        'ut-orange': '#FF8600',
                        'tangelo': '#F54F1D',
                        'red': '#FF2424',
                        'red-2': '#FF0000'
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #EA3A26, #FF8600, #F54F1D);
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .input-group {
            position: relative;
        }
        .input-group input:focus + label,
        .input-group input:valid + label,
        .input-group input:not(:placeholder-shown) + label,
        .input-group select:focus + label,
        .input-group select:valid + label,
        .input-group select:not([value=""]) + label, /* For select elements */
        .input-group textarea:focus + label,
        .input-group textarea:valid + label,
        .input-group textarea:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 12px;
            color: #EA3A26; /* Chili Red for focused label */
        }
        .input-group label {
            position: absolute;
            top: 12px;
            left: 16px;
            color: #ccc; /* Lighter color for initial label */
            transition: all 0.3s ease;
            pointer-events: none;
        }
        /* Adjust input/select/textarea padding to account for floating label */
        .input-group input,
        .input-group select,
        .input-group textarea {
            padding-top: 20px; 
            padding-bottom: 8px;
            /* Placeholder needs to be transparent for floating label effect */
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }
        /* Specific placeholder color for initial state */
        .input-group input::placeholder,
        .input-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        /* Ensure select options text color */
        .input-group select option {
            background-color: #333; /* Dark background for options in dark select */
            color: white;
        }
    </style>
</head>
<body class="min-h-screen gradient-bg">
    <nav class="absolute top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-white">Helly</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-white hover:text-gray-200 transition">Back to Home</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center px-4 py-20">
        <div class="absolute inset-0 overflow-hidden">
            <div class="floating-animation absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full"></div>
            <div class="floating-animation absolute top-40 right-20 w-32 h-32 bg-white/10 rounded-full" style="animation-delay: -2s;"></div>
            <div class="floating-animation absolute bottom-40 left-1/4 w-16 h-16 bg-white/10 rounded-full" style="animation-delay: -4s;"></div>
        </div>

        <div class="w-full max-w-2xl relative z-10 slide-in">
            <div class="glass-morphism rounded-2xl p-8 shadow-2xl">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white mb-2">Vendor Registration</h2>
                    <p class="text-white/80">Join Helly today and manage your business</p>
                </div>

                {{-- Display Success/Error Messages from Controller --}}
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
                {{-- Display Validation Errors --}}
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Whoops!</strong>
                        <span class="block sm:inline">There were some problems with your input.</span>
                        <ul class="mt-3 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('vendor.register.store') }}" class="space-y-4">
                    @csrf {{-- CSRF token for security --}}

                    <div class="bg-white/5 p-4 rounded-lg mb-4">
                        <h4 class="text-white font-semibold mb-3">Business Information</h4>
                        <div class="space-y-4">
                            <div class="input-group">
                                <input type="text" id="vendorCompanyName" name="company_name" required placeholder=" "
                                       class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition"
                                       value="{{ old('company_name') }}">
                                <label for="vendorCompanyName">Company Name</label>
                            </div>

                            <div class="input-group">
                                <input type="text" id="vendorEIN" name="ein" placeholder=" "
                                       class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition"
                                       value="{{ old('ein') }}">
                                <label for="vendorEIN">EIN/Tax ID (Optional)</label>
                                @error('ein')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="input-group">
                                <select id="vendorBusinessType" name="business_type" 
                                        class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition">
                                    <option value="">Select Business Type (Optional)</option>
                                    <option value="llc" {{ old('business_type') == 'llc' ? 'selected' : '' }}>LLC</option>
                                    <option value="corporation" {{ old('business_type') == 'corporation' ? 'selected' : '' }}>Corporation</option>
                                    <option value="partnership" {{ old('business_type') == 'partnership' ? 'selected' : '' }}>Partnership</option>
                                    <option value="sole_proprietorship" {{ old('business_type') == 'sole_proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                </select>
                                <label for="vendorBusinessType">Business Type</label>
                                @error('business_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="input-group">
                                <input type="text" id="vendorBusinessAddress" name="primary_address" required placeholder=" "
                                       class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition"
                                       value="{{ old('primary_address') }}">
                                <label for="vendorBusinessAddress">Business Address</label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="input-group">
                                    <input type="text" id="vendorCity" name="city" required placeholder=" "
                                           class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition"
                                           value="{{ old('city') }}">
                                    <label for="vendorCity">City</label>
                                    @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="input-group">
                                    <select id="vendorState" name="state" required 
                                            class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition">
                                        <option value="">Select State</option>
                                        {{-- States will be populated by JS --}}
                                    </select>
                                    <label for="vendorState">State</label>
                                    @error('state')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="input-group">
                                    <input type="text" id="vendorZip" name="zip" required placeholder=" "
                                           class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition">
                                    <label for="vendorZip">ZIP Code</label>
                                    @error('zip')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 p-4 rounded-lg mb-4">
                        <h4 class="text-white font-semibold mb-3">Primary Contact (Your Account)</h4>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="input-group">
                                    <input type="text" id="vendorFirstName" name="first_name" required placeholder=" "
                                           class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition"
                                           value="{{ old('first_name') }}">
                                    <label for="vendorFirstName">First Name</label>
                                    @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="input-group">
                                    <input type="text" id="vendorLastName" name="last_name" required placeholder=" "
                                           class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition"
                                           value="{{ old('last_name') }}">
                                    <label for="vendorLastName">Last Name</label>
                                    @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="input-group">
                                    <input type="email" id="vendorEmail" name="email" required placeholder=" "
                                           class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition"
                                           value="{{ old('email') }}">
                                    <label for="vendorEmail">Email Address</label>
                                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="input-group">
                                    <input type="tel" id="vendorPhone" name="phone" required placeholder=" "
                                           class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition"
                                           value="{{ old('phone') }}">
                                    <label for="vendorPhone">Phone Number</label>
                                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 p-4 rounded-lg mb-4">
                        <h4 class="text-white font-semibold mb-3">Services Offered</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center text-white/80">
                                <input type="checkbox" name="services[]" value="dumpsters" class="mr-3 rounded" {{ in_array('dumpsters', old('services', [])) ? 'checked' : '' }}>
                                <i class="fas fa-dumpster mr-2"></i>
                                Dumpster Rentals
                            </label>
                            <label class="flex items-center text-white/80">
                                <input type="checkbox" name="services[]" value="containers" class="mr-3 rounded" {{ in_array('containers', old('services', [])) ? 'checked' : '' }}>
                                <i class="fas fa-cube mr-2"></i>
                                Storage Containers
                            </label>
                            <label class="flex items-center text-white/80">
                                <input type="checkbox" name="services[]" value="toilets" class="mr-3 rounded" {{ in_array('toilets', old('services', [])) ? 'checked' : '' }}>
                                <i class="fas fa-restroom mr-2"></i>
                                Portable Toilets
                            </label>
                            <label class="flex items-center text-white/80">
                                <input type="checkbox" name="services[]" value="junk_removal" class="mr-3 rounded" {{ in_array('junk_removal', old('services', [])) ? 'checked' : '' }}>
                                <i class="fas fa-trash mr-2"></i>
                                Junk Removal
                            </label>
                        </div>
                        @error('services')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-4">
                        <div class="input-group">
                            <input type="password" id="vendorPassword" name="password" required placeholder=" "
                                   class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition">
                            <label for="vendorPassword">Password</label>
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="input-group">
                            <input type="password" id="vendorConfirmPassword" name="password_confirmation" required placeholder=" "
                                   class="w-full px-4 py-3 focus:outline-none focus:border-white focus:bg-white/30 transition">
                            <label for="vendorConfirmPassword">Confirm Password</label>
                            @error('password_confirmation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="vendorTerms" name="terms_accepted" value="1" required class="mr-3 rounded" {{ old('terms_accepted') ? 'checked' : '' }}>
                            <label for="vendorTerms" class="text-white/80 text-sm">
                                I agree to the <a href="#" class="text-white underline">Terms of Service</a> and 
                                <a href="#" class="text-white underline">Privacy Policy</a>
                            </label>
                            @error('terms_accepted')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="vendorMarketing" name="marketing_opt_in" value="1" class="mr-3 rounded" {{ old('marketing_opt_in') ? 'checked' : '' }}>
                            <label for="vendorMarketing" class="text-white/80 text-sm">
                                I'd like to receive marketing updates and product news
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-white text-chili-red py-3 rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105">
                        Create Vendor Account
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-white/80 text-sm">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">Sign in here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- Floating Label Logic ---
        document.querySelectorAll('.input-group input, .input-group select, .input-group textarea').forEach(input => {
            // Check on load if input already has value (e.g., old() helper)
            if (input.value.trim() !== '' && input.tagName.toLowerCase() !== 'select') {
                input.classList.add('has-value'); // Not directly used in CSS but can be if needed
                const label = input.nextElementSibling;
                if (label && label.tagName === 'LABEL') {
                    label.style.top = '-10px';
                    label.style.fontSize = '12px';
                    label.style.color = '#EA3A26';
                }
            }
            if (input.tagName.toLowerCase() === 'select' && input.value !== '') {
                 const label = input.nextElementSibling;
                if (label && label.tagName === 'LABEL') {
                    label.style.top = '-10px';
                    label.style.fontSize = '12px';
                    label.style.color = '#EA3A26';
                }
            }


            input.addEventListener('focus', () => {
                const label = input.nextElementSibling;
                if (label && label.tagName === 'LABEL') {
                    label.style.top = '-10px';
                    label.style.fontSize = '12px';
                    label.style.color = '#EA3A26';
                }
            });
            input.addEventListener('blur', () => {
                const label = input.nextElementSibling;
                if (label && label.tagName === 'LABEL' && (input.value.trim() === '' || input.value === null || input.value === undefined || (input.tagName.toLowerCase() === 'select' && input.value === ''))) {
                    label.style.top = '12px';
                    label.style.fontSize = '1rem';
                    label.style.color = '#ccc';
                }
            });
        });

        // --- Form Validation & Formatting ---

        // Auto-populate US state options
        const states = [
            { code: 'AL', name: 'Alabama' }, { code: 'AK', name: 'Alaska' }, { code: 'AZ', name: 'Arizona' }, { code: 'AR', name: 'Arkansas' }, { code: 'CA', name: 'California' }, { code: 'CO', name: 'Colorado' }, { code: 'CT', name: 'Connecticut' }, { code: 'DE', name: 'Delaware' }, { code: 'FL', name: 'Florida' }, { code: 'GA', name: 'Georgia' }, { code: 'HI', name: 'Hawaii' }, { code: 'ID', name: 'Idaho' }, { code: 'IL', name: 'Illinois' }, { code: 'IN', name: 'Indiana' }, { code: 'IA', name: 'Iowa' }, { code: 'KS', name: 'Kansas' }, { code: 'KY', name: 'Kentucky' }, { code: 'LA', name: 'Louisiana' }, { code: 'ME', name: 'Maine' }, { code: 'MD', name: 'Maryland' }, { code: 'MA', name: 'Massachusetts' }, { code: 'MI', name: 'Michigan' }, { code: 'MN', name: 'Minnesota' }, { code: 'MS', name: 'Mississippi' }, { code: 'MO', name: 'Missouri' }, { code: 'MT', name: 'Montana' }, { code: 'NE', name: 'Nebraska' }, { code: 'NV', name: 'Nevada' }, { code: 'NH', name: 'New Hampshire' }, { code: 'NJ', name: 'New Jersey' }, { code: 'NM', name: 'New Mexico' }, { code: 'NY', name: 'New York' }, { code: 'NC', name: 'North Carolina' }, { code: 'ND', name: 'North Dakota' }, { code: 'OH', name: 'Ohio' }, { code: 'OK', name: 'Oklahoma' }, { code: 'OR', name: 'Oregon' }, { code: 'PA', name: 'Pennsylvania' }, { code: 'RI', name: 'Rhode Island' }, { code: 'SC', name: 'South Carolina' }, { code: 'SD', name: 'South Dakota' }, { code: 'TN', name: 'Tennessee' }, { code: 'TX', name: 'Texas' }, { code: 'UT', name: 'Utah' }, { code: 'VT', name: 'Vermont' }, { code: 'VA', name: 'Virginia' }, { code: 'WA', name: 'Washington' }, { code: 'WV', name: 'West Virginia' }, { code: 'WI', name: 'Wisconsin' }, { code: 'WY', name: 'Wyoming' }
        ];

        document.querySelectorAll('select[id$="State"]').forEach(select => {
            states.forEach(state => {
                const option = document.createElement('option');
                option.value = state.code;
                option.textContent = state.name;
                select.appendChild(option);
            });
            // Set old value if exists
            const oldValue = select.getAttribute('data-old-value'); // We will add this data-attribute in Blade
            if (oldValue) {
                select.value = oldValue;
            }
        });

        // Phone number formatting
        document.querySelectorAll('input[type="tel"]').forEach(input => {
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 6) {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                } else if (value.length >= 3) {
                    value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
                }
                e.target.value = value;
            });
        });

        // EIN formatting
        document.getElementById('vendorEIN')?.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.replace(/(\d{2})(\d{0,7})/, '$1-$2');
            }
            e.target.value = value;
        });

        // ZIP code validation (simple 5 digits)
        document.querySelectorAll('input[id$="Zip"]').forEach(input => {
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 5) {
                    value = value.substring(0, 5);
                }
                e.target.value = value;
            });
        });

        // Real-time password strength indicator (conceptual)
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }

        document.querySelectorAll('input[type="password"]').forEach(input => {
            input.addEventListener('input', () => {
                const strength = checkPasswordStrength(input.value);
                const parent = input.parentElement;
                
                // Remove existing strength indicators
                const existingIndicator = parent.querySelector('.strength-indicator');
                if (existingIndicator) {
                    existingIndicator.remove();
                }
                
                if (input.value.length > 0) {
                    const indicator = document.createElement('div');
                    indicator.className = 'strength-indicator mt-2';
                    
                    const colors = ['bg-red-500', 'bg-red-400', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
                    const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
                    
                    indicator.innerHTML = `
                        <div class="flex space-x-1 mb-1">
                            ${Array.from({length: 5}, (_, i) => 
                                `<div class="h-1 flex-1 rounded ${i < strength ? colors[strength-1] : 'bg-white/20'}"></div>`
                            ).join('')}
                        </div>
                        <div class="text-xs text-white/70">${labels[strength-1] || 'Very Weak'}</div>
                    `;
                    
                    parent.appendChild(indicator);
                }
            });
        });

    </script>
</body>
</html>