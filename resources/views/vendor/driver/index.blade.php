@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Driver Management</h2>

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
        <a href="{{ route('drivers.create') }}" class="px-6 py-3 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Add New Driver
        </a>
        <div class="flex flex-wrap items-center gap-4">
            <form action="{{ route('drivers.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <input type="text" id="driverSearch" name="search" placeholder="Search by name, license..."
                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                       value="{{ request('search') }}">

                <select id="statusFilter" name="status_filter"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto">
                    <option value="">All Statuses</option>
                    <option value="Available" @if(request('status_filter') == 'Available') selected @endif>Available</option>
                    <option value="On Duty" @if(request('status_filter') == 'On Duty') selected @endif>On Duty</option>
                    <option value="Off Duty" @if(request('status_filter') == 'Off Duty') selected @endif>Off Duty</option>
                    <option value="Incapacitated" @if(request('status_filter') == 'Incapacitated') selected @endif>Incapacitated</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors duration-200">Apply Filters</button>
                <a href="{{ route('drivers.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors duration-200">Reset</a>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-8">
        <table class="min-w-full divide-y divide-gray-200" id="driverTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($drivers as $driver)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $driver->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->first_name }} {{ $driver->last_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->license_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $driver->phone_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($driver->status == 'Available') bg-green-100 text-green-800
                                @elseif($driver->status == 'On Duty') bg-blue-100 text-blue-800
                                @elseif($driver->status == 'Off Duty') bg-gray-100 text-gray-800
                                @elseif($driver->status == 'Incapacitated') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $driver->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $driver->vehicle->make ?? 'N/A' }} {{ $driver->vehicle->model ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('drivers.show', $driver->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors duration-200">View</a>
                            <a href="{{ route('drivers.edit', $driver->id) }}" class="text-chili-red hover:text-tangelo mr-3 transition-colors duration-200">Edit</a>
                            <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No drivers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $drivers->links() }}
    </div>
@endsection