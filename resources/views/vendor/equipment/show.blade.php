@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Equipment Details: {{ $equipment->internal_id }}</h2>

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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Internal ID:</p>
                <p class="text-lg text-gray-900 font-semibold">{{ $equipment->internal_id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Type:</p>
                <p class="text-lg text-gray-900">{{ $equipment->type }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Size/Capacity:</p>
                <p class="text-lg text-gray-900">{{ $equipment->size }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Base Daily Rate:</p>
                <p class="text-lg text-gray-900">${{ number_format($equipment->base_daily_rate, 2) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Current Location:</p>
                <p class="text-lg text-gray-900">{{ $equipment->location }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status:</p>
                <p class="text-lg text-gray-900">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($equipment->status == 'Available') bg-green-100 text-green-800
                        @elseif($equipment->status == 'On Rent') bg-blue-100 text-blue-800
                        @elseif($equipment->status == 'In Maintenance') bg-yellow-100 text-yellow-800
                        @elseif($equipment->status == 'Out of Service') bg-red-100 text-red-800
                        @elseif($equipment->status == 'Reserved') bg-purple-100 text-purple-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $equipment->status }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Last Maintenance:</p>
                <p class="text-lg text-gray-900">{{ $equipment->last_maintenance_date ? $equipment->last_maintenance_date->format('M d, Y') : 'N/A' }}</p>
            </div>
        </div>

        @if($equipment->notes)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Notes:</p>
                <p class="text-lg text-gray-900">{{ $equipment->notes }}</p>
            </div>
        @endif

        @if($equipment->image_url)
            <div class="mt-6">
                <p class="text-sm font-medium text-gray-500 mb-2">Equipment Image:</p>
                <img src="{{ $equipment->image_url }}" alt="Equipment Image" class="w-full max-w-sm h-auto rounded-md shadow-md object-cover">
            </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('equipment.edit', $equipment->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                Edit Equipment
            </a>
            <form action="{{ route('equipment.destroy', $equipment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this equipment? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Delete Equipment
                </button>
            </form>
            <a href="{{ route('equipment.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                Back to Equipment List
            </a>
        </div>
    </div>
@endsection