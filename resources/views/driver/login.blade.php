<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Driver Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'chili-red': '#EA3A26',
                        'ut-orange': '#FF8600',
                        'tangelo': '#F54F1D',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-in-out',
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
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl w-full max-w-md animate-fade-in">
        <div class="text-center mb-8">
            <h1 class="text-chili-red text-4xl font-bold drop-shadow-lg mb-2">
                <i class="fas fa-truck-pickup mr-2"></i>Helly
            </h1>
            <p class="text-gray-600 text-lg">Driver Portal</p>
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

        <form method="POST" action="{{ route('driver.login.attempt') }}">
            @csrf {{-- CSRF token for security --}}

            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red transition-all duration-200"
                    placeholder="Enter your email"
                    value="{{ old('email') }}"
                    required
                >
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-chili-red focus:border-chili-red transition-all duration-200"
                    placeholder="Enter your password"
                    required
                >
            </div>
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="h-4 w-4 text-chili-red focus:ring-chili-red border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember Me
                    </label>
                </div>
                <a href="#" class="text-chili-red hover:text-ut-orange text-sm font-semibold transition-colors duration-200" onclick="alert('Forgot password functionality coming soon!')">
                    Forgot Password?
                </a>
            </div>
            <button
                type="submit"
                class="w-full bg-gradient-to-r from-chili-red to-tangelo text-white py-3 rounded-lg font-semibold text-lg shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login
            </button>
        </form>
    </div>
</body>
</html>