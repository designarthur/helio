<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Registration Success</title>
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
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl w-full max-w-md animate-fade-in text-center">
        <div class="text-center mb-8">
            <h1 class="text-chili-red text-4xl font-bold drop-shadow-lg mb-2">
                <i class="fas fa-truck mr-2"></i>Helly
            </h1>
            <p class="text-gray-600 text-lg">Vendor Registration</p>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <h2 class="text-2xl font-bold text-gray-800 mb-4">Thank You for Registering!</h2>
        <p class="text-gray-600 mb-6">
            Your company account has been created successfully and is now **pending approval**.
            We will review your registration shortly and notify you via email once your account is active.
        </p>

        <a href="{{ route('login') }}" class="bg-gradient-to-r from-chili-red to-tangelo text-white py-3 px-6 rounded-lg font-semibold shadow-lg hover:shadow-xl hover:transform hover:-translate-y-1 transition-all duration-300 inline-flex items-center">
            <i class="fas fa-sign-in-alt mr-2"></i> Go to Login
        </a>
    </div>
</body>
</html>