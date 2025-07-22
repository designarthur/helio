<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Equipment Management</title>
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
                    },
                }
            }
        }
    </script>
    <style>
        /* Existing custom styles from your HTML, if any */
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen">

    {{-- Main content wrapper - for now, this will be a full page, later part of a layout --}}
    <div class="p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Equipment Management</h2>

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


        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <a href="{{ route('equipment.create') }}" class="px-6 py-3 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add New Equipment
            </a>
            <div class="flex flex-wrap items-center gap-4">
                <form action="{{ route('equipment.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                    <input type="text" id="equipmentSearch" name="search" placeholder="Search by ID, Type..."
                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                           value="{{ request('search') }}">

                    <select id="statusFilter" name="status_filter"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto">
                        <option value="">All Statuses</option>
                        <option value="Available" @if(request('status_filter') == 'Available') selected @endif>Available</option>
                        <option value="On Rent" @if(request('status_filter') == 'On Rent') selected @endif>On Rent</option>
                        <option value="In Maintenance" @if(request('status_filter') == 'In Maintenance') selected @endif>In Maintenance</option>
                        <option value="Out of Service" @if(request('status_filter') == 'Out of Service') selected @endif>Out of Service</option>
                        <option value="Reserved" @if(request('status_filter') == 'Reserved') selected @endif>Reserved</option>
                    </select>

                    <select id="typeFilter" name="type_filter"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto">
                        <option value="">Select All Types</option>
                        <option value="Dumpster" @if(request('type_filter') == 'Dumpster') selected @endif>Dumpster</option>
                        <option value="Temporary Toilet" @if(request('type_filter') == 'Temporary Toilet') selected @endif>Temporary Toilet</option>
                        <option value="Storage Container" @if(request('type_filter') == 'Storage Container') selected @endif>Storage Container</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors duration-200">Apply Filters</button>
                    <a href="{{ route('equipment.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors duration-200">Reset</a>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-8">
            <table class="min-w-full divide-y divide-gray-200" id="equipmentTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Daily Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($equipment as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->internal_id ?? $item->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->size }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->status }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($item->base_daily_rate, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('equipment.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors duration-200">View</a>
                                <a href="{{ route('equipment.edit', $item->id) }}" class="text-chili-red hover:text-tangelo mr-3 transition-colors duration-200">Edit</a>
                                <form action="{{ route('equipment.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this equipment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No equipment found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="mt-4">
            {{ $equipment->links() }}
        </div>
    </div>
</body>
</html>