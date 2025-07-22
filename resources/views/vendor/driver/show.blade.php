@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Driver Details: {{ $driver->first_name }} {{ $driver->last_name }}</h2>

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

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Name:</p>
                <p class="text-lg text-gray-900 font-semibold">{{ $driver->first_name }} {{ $driver->last_name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Email:</p>
                <p class="text-lg text-gray-900">{{ $driver->email }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Phone Number:</p>
                <p class="text-lg text-gray-900">{{ $driver->phone_number }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">License Number:</p>
                <p class="text-lg text-gray-900">{{ $driver->license_number }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status:</p>
                <p class="text-lg text-gray-900">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($driver->status == 'Available') bg-green-100 text-green-800
                        @elseif($driver->status == 'On Duty') bg-blue-100 text-blue-800
                        @elseif($driver->status == 'Off Duty') bg-gray-100 text-gray-800
                        @elseif($driver->status == 'Incapacitated') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $driver->status }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Assigned Vehicle:</p>
                <p class="text-lg text-gray-900">
                    @if($driver->vehicle)
                        <a href="{{ route('equipment.show', $driver->vehicle->id) }}" class="text-blue-600 hover:underline">
                            {{ $driver->vehicle->make }} {{ $driver->vehicle->model }} ({{ $driver->vehicle->license_plate }})
                        </a>
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>

        @if($driver->notes)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Notes:</p>
                <p class="text-lg text-gray-900">{{ $driver->notes }}</p>
            </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('drivers.edit', $driver->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                Edit Driver
            </a>
            <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this driver? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Delete Driver
                </button>
            </form>
            <a href="{{ route('drivers.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                Back to Drivers
            </a>
        </div>
    </div>
@endsection