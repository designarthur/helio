<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Complete Multi-Vendor Equipment Rental Platform</title>
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
        .hero-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(234, 58, 38, 0.15);
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-chili-red">Helly</h1>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-chili-red transition">Features</a>
                    <a href="#modules" class="text-gray-700 hover:text-chili-red transition">Modules</a>
                    <a href="#apps" class="text-gray-700 hover:text-chili-red transition">Mobile Apps</a>
                    <a href="#pricing" class="text-gray-700 hover:text-chili-red transition">Pricing</a>
                    <button onclick="alert('Get Started button clicked!')" class="bg-chili-red text-white px-6 py-2 rounded-lg hover:bg-chili-red-2 transition">Get Started</button>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <section class="gradient-bg text-white pt-20 pb-16 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 hero-animation">
                    Complete Equipment Rental Platform
                </h1>
                <p class="text-xl md:text-2xl mb-8 opacity-90 max-w-3xl mx-auto">
                    Streamline your dumpster, storage container, and portable toilet rental business with our comprehensive multi-vendor platform
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                    <button onclick="alert('Start Free Trial button clicked!')" class="bg-white text-chili-red px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition transform hover:scale-105">
                        Start Free Trial
                    </button>
                    <button onclick="alert('Watch Demo button clicked!')" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-chili-red transition transform hover:scale-105">
                        Watch Demo
                    </button>
                </div>
                <div class="hero-animation">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 max-w-4xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <i class="fas fa-dumpster text-4xl mb-3"></i>
                                <p class="font-semibold">Dumpster Rentals</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-cube text-4xl mb-3"></i>
                                <p class="font-semibold">Storage Containers</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-restroom text-4xl mb-3"></i>
                                <p class="font-semibold">Portable Toilets</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose Helly?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Built specifically for the US equipment rental market with advanced features and seamless integrations</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center fade-in card-hover bg-gray-50 p-6 rounded-xl">
                    <div class="bg-chili-red text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Multi-Vendor Ready</h3>
                    <p class="text-gray-600">Support unlimited vendors with complete brand customization and white-labeling</p>
                </div>
                <div class="text-center fade-in card-hover bg-gray-50 p-6 rounded-xl">
                    <div class="bg-ut-orange text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-route text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Smart Route Optimization</h3>
                    <p class="text-gray-600">AI-powered routing saves fuel costs and improves delivery efficiency</p>
                </div>
                <div class="text-center fade-in card-hover bg-gray-50 p-6 rounded-xl">
                    <div class="bg-tangelo text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-camera text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">AI Visual Quoting</h3>
                    <p class="text-gray-600">Advanced junk removal quotes using image and video analysis</p>
                </div>
                <div class="text-center fade-in card-hover bg-gray-50 p-6 rounded-xl">
                    <div class="bg-red text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Complete Mobile Suite</h3>
                    <p class="text-gray-600">Dedicated iOS & Android apps for customers, vendors, and drivers</p>
                </div>
            </div>
        </div>
    </section>

    <section id="modules" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Comprehensive Platform Modules</h2>
                <p class="text-xl text-gray-600">Everything you need to run your equipment rental business efficiently</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-lg card-hover fade-in">
                    <div class="flex items-center mb-6">
                        <div class="bg-chili-red text-white w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cogs text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold">Equipment Management</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-chili-red mr-2"></i>Real-time inventory tracking</li>
                        <li><i class="fas fa-check text-chili-red mr-2"></i>Availability calendars</li>
                        <li><i class="fas fa-check text-chili-red mr-2"></i>Maintenance scheduling</li>
                        <li><i class="fas fa-check text-chili-red mr-2"></i>GPS location tracking</li>
                        <li><i class="fas fa-check text-chili-red mr-2"></i>Photo documentation</li>
                    </ul>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg card-hover fade-in">
                    <div class="flex items-center mb-6">
                        <div class="bg-ut-orange text-white w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-address-book text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold">Customer Management</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-ut-orange mr-2"></i>Complete CRM system</li>
                        <li><i class="fas fa-check text-ut-orange mr-2"></i>Communication history</li>
                        <li><i class="fas fa-check text-ut-orange mr-2"></i>Payment method storage</li>
                        <li><i class="fas fa-check text-ut-orange mr-2"></i>Rental history tracking</li>
                        <li><i class="fas fa-check text-ut-orange mr-2"></i>Custom segmentation</li>
                    </ul>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg card-hover fade-in">
                    <div class="flex items-center mb-6">
                        <div class="bg-tangelo text-white w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-route text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold">Route Optimization</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-tangelo mr-2"></i>AI-powered route planning</li>
                        <li><i class="fas fa-check text-tangelo mr-2"></i>Real-time GPS tracking</li>
                        <li><i class="fas fa-check text-tangelo mr-2"></i>Driver mobile dispatch</li>
                        <li><i class="fas fa-check text-tangelo mr-2"></i>Fuel cost optimization</li>
                        <li><i class="fas fa-check text-tangelo mr-2"></i>Hours of Service tracking</li>
                    </ul>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg card-hover fade-in">
                    <div class="flex items-center mb-6">
                        <div class="bg-red text-white w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-file-invoice-dollar text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold">Quotes & Invoicing</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-red mr-2"></i>Professional quote generation</li>
                        <li><i class="fas fa-check text-red mr-2"></i>Automated invoicing</li>
                        <li><i class="fas fa-check text-red mr-2"></i>Multiple payment gateways</li>
                        <li><i class="fas fa-check text-red mr-2"></i>Recurring billing cycles</li>
                        <li><i class="fas fa-check text-red mr-2"></i>Tax calculation</li>
                    </ul>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg card-hover fade-in">
                    <div class="flex items-center mb-6">
                        <div class="bg-chili-red-2 text-white w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-chart-pie text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold">Financial Reports</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-chili-red-2 mr-2"></i>Revenue analytics</li>
                        <li><i class="fas fa-check text-chili-red-2 mr-2"></i>A/R aging reports</li>
                        <li><i class="fas fa-check text-chili-red-2 mr-2"></i>QuickBooks integration</li>
                        <li><i class="fas fa-check text-chili-red-2 mr-2"></i>Tax reporting</li>
                        <li><i class="fas fa-check text-chili-red-2 mr-2"></i>Profit analysis</li>
                    </ul>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-lg card-hover fade-in">
                    <div class="flex items-center mb-6">
                        <div class="bg-chili-red-3 text-white w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-palette text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold">White-Label Branding</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-chili-red-3 mr-2"></i>Custom logo & colors</li>
                        <li><i class="fas fa-check text-chili-red-3 mr-2"></i>Custom domain mapping</li>
                        <li><i class="fas fa-check text-chili-red-3 mr-2"></i>Branded email templates</li>
                        <li><i class="fas fa-check text-chili-red-3 mr-2"></i>CSS customization</li>
                        <li><i class="fas fa-check text-chili-red-3 mr-2"></i>Customer portal branding</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="fade-in">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Advanced Junk Removal Services</h2>
                    <p class="text-xl text-gray-600 mb-8">
                        Revolutionary visual quoting system that analyzes images and videos to provide accurate junk removal estimates
                    </p>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-chili-red text-white w-8 h-8 rounded-full flex items-center justify-center mr-4 mt-1">
                                <i class="fas fa-camera text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Visual Quote Generation</h3>
                                <p class="text-gray-600">Upload photos and videos for AI-powered volume and cost estimation</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-ut-orange text-white w-8 h-8 rounded-full flex items-center justify-center mr-4 mt-1">
                                <i class="fas fa-calculator text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Accurate Pricing</h3>
                                <p class="text-gray-600">Smart algorithms analyze content to provide fair, competitive quotes</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-tangelo text-white w-8 h-8 rounded-full flex items-center justify-center mr-4 mt-1">
                                <i class="fas fa-mobile-alt text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Seamless Workflow</h3>
                                <p class="text-gray-600">From quote to payment through your personalized dashboard</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-chili-red to-ut-orange p-8 rounded-2xl text-white fade-in hero-animation">
                    <div class="text-center">
                        <i class="fas fa-upload text-6xl mb-6 opacity-90"></i>
                        <h3 class="text-2xl font-bold mb-4">Upload & Get Quote</h3>
                        <p class="text-lg opacity-90 mb-6">Simply upload images and videos of your junk</p>
                        <button onclick="alert('Try Visual Quoting button clicked!')" class="bg-white text-chili-red px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Try Visual Quoting
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="apps" class="py-16 gradient-bg text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Complete Mobile App Suite</h2>
                <p class="text-xl opacity-90 max-w-3xl mx-auto">
                    Dedicated iOS and Android applications for every user type in your ecosystem
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-xl text-center card-hover fade-in">
                    <div class="bg-white text-chili-red w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Customer App</h3>
                    <ul class="text-left space-y-3 opacity-90">
                        <li><i class="fas fa-check mr-3"></i>Browse and book equipment</li>
                        <li><i class="fas fa-check mr-3"></i>Real-time delivery tracking</li>
                        <li><i class="fas fa-check mr-3"></i>Secure payment processing</li>
                        <li><i class="fas fa-check mr-3"></i>Visual junk removal quotes</li>
                        <li><i class="fas fa-check mr-3"></i>Rental history & invoices</li>
                        <li><i class="fas fa-check mr-3"></i>Push notifications</li>
                    </ul>
                    <div class="mt-6 flex justify-center space-x-4">
                        <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="Download on App Store" class="h-12">
                        <img src="https://play.google.com/intl/en_us/badges/images/generic/en-play-badge.png" alt="Get it on Google Play" class="h-12">
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-xl text-center card-hover fade-in">
                    <div class="bg-white text-ut-orange w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-store text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Vendor App</h3>
                    <ul class="text-left space-y-3 opacity-90">
                        <li><i class="fas fa-check mr-3"></i>Manage equipment inventory</li>
                        <li><i class="fas fa-check mr-3"></i>Create quotes & invoices</li>
                        <li><i class="fas fa-check mr-3"></i>Customer management</li>
                        <li><i class="fas fa-check mr-3"></i>Financial reporting</li>
                        <li><i class="fas fa-check mr-3"></i>Booking management</li>
                        <li><i class="fas fa-check mr-3"></i>Real-time analytics</li>
                    </ul>
                    <div class="mt-6 flex justify-center space-x-4">
                        <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="Download on App Store" class="h-12">
                        <img src="https://play.google.com/intl/en_us/badges/images/generic/en-play-badge.png" alt="Get it on Google Play" class="h-12">
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-xl text-center card-hover fade-in">
                    <div class="bg-white text-tangelo w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-truck text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Driver App</h3>
                    <ul class="text-left space-y-3 opacity-90">
                        <li><i class="fas fa-check mr-3"></i>Optimized route planning</li>
                        <li><i class="fas fa-check mr-3"></i>Turn-by-turn navigation</li>
                        <li><i class="fas fa-check mr-3"></i>Job status updates</li>
                        <li><i class="fas fa-check mr-3"></i>Proof of delivery photos</li>
                        <li><i class="fas fa-check mr-3"></i>Digital signatures</li>
                        <li><i class="fas fa-check mr-3"></i>Hours of service logging</li>
                    </ul>
                    <div class="mt-6 flex justify-center space-x-4">
                        <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="Download on App Store" class="h-12">
                        <img src="https://play.google.com/intl/en_us/badges/images/generic/en-play-badge.png" alt="Get it on Google Play" class="h-12">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="integrations" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Seamless Integrations</h2>
                <p class="text-xl text-gray-600">Connect with the tools you already use</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">
                <div class="text-center fade-in">
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <i class="fab fa-stripe text-4xl text-blue-500"></i>
                        <p class="mt-2 font-semibold">Stripe</p>
                    </div>
                </div>
                <div class="text-center fade-in">
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <i class="fab fa-paypal text-4xl text-blue-600"></i>
                        <p class="mt-2 font-semibold">PayPal</p>
                    </div>
                </div>
                <div class="text-center fade-in">
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <i class="fas fa-calculator text-4xl text-green-600"></i>
                        <p class="mt-2 font-semibold">QuickBooks</p>
                    </div>
                </div>
                <div class="text-center fade-in">
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <i class="fab fa-google text-4xl text-red-500"></i>
                        <p class="mt-2 font-semibold">Google Maps</p>
                    </div>
                </div>
                <div class="text-center fade-in">
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <i class="fas fa-envelope text-4xl text-gray-600"></i>
                        <p class="mt-2 font-semibold">Email</p>
                    </div>
                </div>
                <div class="text-center fade-in">
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <i class="fas fa-sms text-4xl text-purple-600"></i>
                        <p class="mt-2 font-semibold">SMS</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">What Our Clients Say</h2>
                <p class="text-xl text-gray-600">Trusted by equipment rental companies across the USA</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-xl fade-in card-hover">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">"Helly transformed our business. The route optimization alone saved us 30% on fuel costs, and the customer portal reduced our phone calls by 50%."</p>
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face" alt="Client" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="font-semibold">Mike Johnson</p>
                            <p class="text-sm text-gray-500">ABC Dumpster Rentals</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-8 rounded-xl fade-in card-hover">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">"The visual junk removal quotes are a game-changer. We can provide accurate estimates instantly, and customers love the transparency."</p>
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b977?w=100&h=100&fit=crop&crop=face" alt="Client" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="font-semibold">Sarah Martinez</p>
                            <p class="text-sm text-gray-500">Clean Sweep Services</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-8 rounded-xl fade-in card-hover">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">"White-labeling capabilities allowed us to maintain our brand identity while leveraging powerful technology. Our customers think it's our own system!"</p>
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face" alt="Client" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="font-semibold">David Chen</p>
                            <p class="text-sm text-gray-500">Metro Container Co.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 gradient-bg text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Proven Results</h2>
                <p class="text-xl opacity-90">Numbers that speak for themselves</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center fade-in">
                    <div class="text-5xl font-bold mb-2">500+</div>
                    <p class="text-lg opacity-90">Active Vendors</p>
                </div>
                <div class="text-center fade-in">
                    <div class="text-5xl font-bold mb-2">50K+</div>
                    <p class="text-lg opacity-90">Equipment Units Managed</p>
                </div>
                <div class="text-center fade-in">
                    <div class="text-5xl font-bold mb-2">1M+</div>
                    <p class="text-lg opacity-90">Bookings Processed</p>
                </div>
                <div class="text-center fade-in">
                    <div class="text-5xl font-bold mb-2">30%</div>
                    <p class="text-lg opacity-90">Average Cost Savings</p>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Simple, Transparent Pricing</h2>
                <p class="text-xl text-gray-600">Choose the plan that fits your business size</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-xl fade-in card-hover">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold mb-4">Starter</h3>
                        <div class="text-4xl font-bold text-chili-red mb-2">$99<span class="text-lg text-gray-600">/month</span></div>
                        <p class="text-gray-600">Perfect for small operations</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Up to 50 equipment units</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Basic CRM</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Quote & Invoice management</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Mobile apps included</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Email support</li>
                    </ul>
                    <button onclick="alert('Start Free Trial button clicked!')" class="w-full bg-chili-red text-white py-3 rounded-lg font-semibold hover:bg-chili-red-2 transition">
                        Start Free Trial
                    </button>
                </div>

                <div class="bg-chili-red text-white p-8 rounded-xl fade-in card-hover relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-ut-orange text-white px-4 py-1 rounded-full text-sm font-semibold">
                        Most Popular
                    </div>
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold mb-4">Professional</h3>
                        <div class="text-4xl font-bold mb-2">$299<span class="text-lg opacity-90">/month</span></div>
                        <p class="opacity-90">For growing businesses</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check mr-3"></i>Up to 200 equipment units</li>
                        <li class="flex items-center"><i class="fas fa-check mr-3"></i>Advanced CRM & analytics</li>
                        <li class="flex items-center"><i class="fas fa-check mr-3"></i>Route optimization</li>
                        <li class="flex items-center"><i class="fas fa-check mr-3"></i>Visual junk removal quotes</li>
                        <li class="flex items-center"><i class="fas fa-check mr-3"></i>White-label branding</li>
                        <li class="flex items-center"><i class="fas fa-check mr-3"></i>QuickBooks integration</li>
                        <li class="flex items-center"><i class="fas fa-check mr-3"></i>Priority support</li>
                    </ul>
                    <button onclick="alert('Start Free Trial button clicked!')" class="w-full bg-white text-chili-red py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Start Free Trial
                    </button>
                </div>

                <div class="bg-gray-50 p-8 rounded-xl fade-in card-hover">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold mb-4">Enterprise</h3>
                        <div class="text-4xl font-bold text-chili-red mb-2">Custom</div>
                        <p class="text-gray-600">For large operations</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Unlimited equipment units</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Multi-location support</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>API access</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Custom integrations</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Dedicated account manager</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>24/7 phone support</li>
                        <li class="flex items-center"><i class="fas fa-check text-chili-red mr-3"></i>Custom training</li>
                    </ul>
                    <button onclick="alert('Contact Sales button clicked!')" class="w-full bg-chili-red text-white py-3 rounded-lg font-semibold hover:bg-chili-red-2 transition">
                        Contact Sales
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                <p class="text-xl text-gray-600">Everything you need to know about Helly</p>
            </div>
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm fade-in">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="text-lg font-semibold">How quickly can I get started with Helly?</h3>
                        <i class="fas fa-chevron-down text-chili-red"></i>
                    </button>
                    <div class="faq-content hidden mt-4 text-gray-600">
                        <p>Most vendors are up and running within 24-48 hours. Our onboarding team will help you import your existing data, configure your equipment inventory, and customize your branding. We provide comprehensive training for your team.</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm fade-in">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="text-lg font-semibold">Can I integrate with my existing QuickBooks account?</h3>
                        <i class="fas fa-chevron-down text-chili-red"></i>
                    </button>
                    <div class="faq-content hidden mt-4 text-gray-600">
                        <p>Yes! Helly offers seamless QuickBooks Online integration. All your invoices, payments, and customer data can automatically sync with your existing accounting system, eliminating double data entry.</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm fade-in">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="text-lg font-semibold">How does the visual junk removal quoting work?</h3>
                        <i class="fas fa-chevron-down text-chili-red"></i>
                    </button>
                    <div class="faq-content hidden mt-4 text-gray-600">
                        <p>Customers upload photos and videos of their junk through our mobile app or website. Our AI analyzes the content to estimate volume, identify materials, and suggest pricing based on your configured rates. You can review and adjust before sending the quote.</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm fade-in">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="text-lg font-semibold">Is my customer data secure and private?</h3>
                        <i class="fas fa-chevron-down text-chili-red"></i>
                    </button>
                    <div class="faq-content hidden mt-4 text-gray-600">
                        <p>Absolutely. We use enterprise-grade security with encryption at rest and in transit. We're PCI DSS compliant for payment processing and follow all US privacy regulations. Your customer data is yours and is never shared with competitors.</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm fade-in">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="text-lg font-semibold">Do you charge transaction fees on payments?</h3>
                        <i class="fas fa-chevron-down text-chili-red"></i>
                    </button>
                    <div class="faq-content hidden mt-4 text-gray-600">
                        <p>We don't add any fees on top of standard payment processor rates. You get direct access to competitive rates from Stripe, PayPal, and other payment gateways. All transaction fees go directly to the payment processor, not us.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 gradient-bg text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="fade-in">
                <h2 class="text-4xl font-bold mb-6">Ready to Transform Your Equipment Rental Business?</h2>
                <p class="text-xl mb-8 opacity-90">
                    Join hundreds of successful vendors who have streamlined their operations with Helly
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="alert('Start Your Free 30-Day Trial button clicked!')" class="bg-white text-chili-red px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition transform hover:scale-105">
                        Start Your Free 30-Day Trial
                    </button>
                    <button onclick="alert('Schedule a Demo button clicked!')" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-chili-red transition transform hover:scale-105">
                        Schedule a Demo
                    </button>
                </div>
                <p class="mt-6 text-sm opacity-75">No credit card required • Full access to all features • Cancel anytime</p>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold text-chili-red mb-4">Helly</h3>
                    <p class="text-gray-400 mb-4">The complete multi-vendor equipment rental platform for the modern business.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#integrations" class="hover:text-white transition">Integrations</a></li>
                        <li><a href="#apps" class="hover:text-white transition">Mobile Apps</a></li>
                        <li><a href="#" class="hover:text-white transition">API Documentation</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact Support</a></li>
                        <li><a href="#" class="hover:text-white transition">Training Resources</a></li>
                        <li><a href="#" class="hover:text-white transition">System Status</a></li>
                        <li><a href="#" class="hover:text-white transition">Release Notes</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Careers</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition">Security</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Helly. All rights reserved. Built for US equipment rental businesses.</p>
            </div>
        </div>
    </footer>

    <script>
        // Fade in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // FAQ toggle functionality
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('i');

                content.classList.toggle('hidden');
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            });
        });

        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', () => {
            // Add mobile menu functionality here
            console.log('Mobile menu clicked');
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>