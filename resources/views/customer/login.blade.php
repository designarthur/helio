<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Customer Login</title>
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
        /* Styles for input-group from the provided HTML */
        .input-group {
            position: relative;
        }
        .input-group input:focus + label,
        .input-group input:valid + label,
        .input-group input:not(:placeholder-shown) + label { /* Added :not(:placeholder-shown) for persistent label */
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
        /* Adjust input padding to account for floating label */
        .input-group input {
            padding-top: 20px; 
            padding-bottom: 8px;
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

        <div class="w-full max-w-md relative z-10 slide-in">
            <div class="glass-morphism rounded-2xl p-8 shadow-2xl">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white mb-2">Customer Portal</h2>
                    <p class="text-white/80">Sign in to your customer account</p>
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

                <form method="POST" action="{{ route('customer.login.attempt') }}" class="space-y-6">
                    @csrf {{-- CSRF token for security --}}

                    <div class="input-group">
                        <input type="email" id="loginEmail" name="email" required 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-white focus:bg-white/30 transition"
                               value="{{ old('email') }}" placeholder=" ">
                        <label for="loginEmail">Email Address</label>
                    </div>

                    <div class="input-group">
                        <input type="password" id="loginPassword" name="password" required 
                               class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-transparent focus:outline-none focus:border-white focus:bg-white/30 transition"
                               placeholder=" ">
                        <label for="loginPassword">Password</label>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-white/80 text-sm">
                            <input type="checkbox" name="remember" class="mr-2 rounded">
                            Remember me
                        </label>
                        <a href="#" class="text-white/80 text-sm hover:text-white transition" onclick="alert('Forgot password functionality coming soon!')">Forgot password?</a>
                    </div>

                    <button type="submit" class="w-full bg-white text-chili-red py-3 rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105">
                        Sign In
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-white/80 text-sm">
                        Don't have an account? 
                        <a href="#" class="text-white font-semibold hover:underline" onclick="alert('Customer registration functionality coming soon!')">Sign up here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>