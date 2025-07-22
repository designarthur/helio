<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - User Details: {{ $user->name }}</title>
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
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen flex items-center justify-center py-8">

    {{-- Main content wrapper (simulating a modal or a dedicated page for details) --}}
    <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-md relative max-h-[90vh] overflow-y-auto">
        <button onclick="window.history.back()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl font-bold">&times;</button>
        <h3 class="text-2xl font-bold text-chili-red mb-6 border-b pb-3 border-gray-200">
            User Details: <span id="detailInternalUserId">{{ $user->id }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <p><strong>Name:</strong> <span id="detailInternalUserName">{{ $user->name }}</span></p>
            <p><strong>Email:</strong> <span id="detailInternalUserEmail">{{ $user->email }}</span></p>
            <p><strong>Role:</strong> <span id="detailInternalUserRole">{{ $user->role }}</span></p>
            <p><strong>Status:</strong> <span id="detailInternalUserStatus">{{ $user->status }}</span></p>
        </div>
        <div class="mt-6 flex justify-end">
            <a href="{{ route('settings.show', ['tab' => 'users']) }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
        </div>
    </div>
</body>
</html>