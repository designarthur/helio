@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ isset($equipment) ? 'Edit Equipment' : 'Add New Equipment' }}</h2>

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

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ isset($equipment) ? route('equipment.update', $equipment->id) : route('equipment.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($equipment))
                @method('PUT') {{-- Use PUT for updates --}}
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="internal_id" class="block text-sm font-medium text-gray-700 mb-1">Internal ID (SKU)</label>
                    <input type="text" name="internal_id" id="internal_id"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('internal_id', $equipment->internal_id ?? '') }}" placeholder="e.g., DUMPSTER-001" required>
                </div>

                <div class="mb-4">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Equipment Type</label>
                    <select name="type" id="type"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                            required>
                        <option value="">Select Type</option>
                        <option value="Dumpster" {{ (old('type', $equipment->type ?? '') == 'Dumpster') ? 'selected' : '' }}>Dumpster</option>
                        <option value="Temporary Toilet" {{ (old('type', $equipment->type ?? '') == 'Temporary Toilet') ? 'selected' : '' }}>Temporary Toilet</option>
                        <option value="Storage Container" {{ (old('type', $equipment->type ?? '') == 'Storage Container') ? 'selected' : '' }}>Storage Container</option>
                        <option value="Other" {{ (old('type', $equipment->type ?? '') == 'Other') ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Size/Capacity</label>
                    <input type="text" name="size" id="size"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('size', $equipment->size ?? '') }}" placeholder="e.g., 20 Yard, 500 Gallon" required>
                </div>

                <div class="mb-4">
                    <label for="base_daily_rate" class="block text-sm font-medium text-gray-700 mb-1">Base Daily Rate ($)</label>
                    <input type="number" name="base_daily_rate" id="base_daily_rate" step="0.01" min="0"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('base_daily_rate', $equipment->base_daily_rate ?? '') }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Current Location</label>
                <input type="text" name="location" id="location"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('location', $equipment->location ?? '') }}" placeholder="e.g., Warehouse A, Customer Site, On Route" required>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="Available" {{ (old('status', $equipment->status ?? '') == 'Available') ? 'selected' : '' }}>Available</option>
                    <option value="On Rent" {{ (old('status', $equipment->status ?? '') == 'On Rent') ? 'selected' : '' }}>On Rent</option>
                    <option value="In Maintenance" {{ (old('status', $equipment->status ?? '') == 'In Maintenance') ? 'selected' : '' }}>In Maintenance</option>
                    <option value="Out of Service" {{ (old('status', $equipment->status ?? '') == 'Out of Service') ? 'selected' : '' }}>Out of Service</option>
                    <option value="Reserved" {{ (old('status', $equipment->status ?? '') == 'Reserved') ? 'selected' : '' }}>Reserved</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="last_maintenance_date" class="block text-sm font-medium text-gray-700 mb-1">Last Maintenance Date (optional)</label>
                <input type="date" name="last_maintenance_date" id="last_maintenance_date"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('last_maintenance_date', $equipment->last_maintenance_date->format('Y-m-d') ?? '') }}">
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                          placeholder="Any specific details, conditions, or instructions about the equipment">{{ old('notes', $equipment->notes ?? '') }}</textarea>
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Equipment Image (optional)</label>
                <input type="file" name="image" id="image"
                       class="mt-1 block w-full text-sm text-gray-500
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-md file:border-0
                       file:text-sm file:font-semibold
                       file:bg-chili-red file:text-white
                       hover:file:bg-tangelo">
                @if (isset($equipment->image_url) && $equipment->image_url)
                    <p class="mt-2 text-sm text-gray-500">Current Image:</p>
                    <img src="{{ $equipment->image_url }}" alt="Equipment Image" class="mt-2 h-32 w-auto object-cover rounded-md">
                @endif
                <p class="mt-2 text-xs text-gray-500">Upload an image for the equipment (Max 2MB).</p>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('equipment.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    {{ isset($equipment) ? 'Update Equipment' : 'Add Equipment' }}
                </button>
            </div>
        </form>
    </div>
@endsection