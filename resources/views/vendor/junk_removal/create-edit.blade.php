@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ isset($junkRemovalJob) ? 'Edit Junk Removal Job' : 'Create New Junk Removal Job' }}</h2>

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
        <form action="{{ isset($junkRemovalJob) ? route('junk_removal_jobs.update', $junkRemovalJob->id) : route('junk_removal_jobs.store') }}" method="POST">
            @csrf
            @if(isset($junkRemovalJob))
                @method('PUT') {{-- Use PUT for updates --}}
            @endif

            <div class="mb-4">
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <select name="customer_id" id="customer_id"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="">Select a Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ (old('customer_id', $junkRemovalJob->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>
                            {{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="pickup_address" class="block text-sm font-medium text-gray-700 mb-1">Pickup Address</label>
                <input type="text" name="pickup_address" id="pickup_address"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('pickup_address', $junkRemovalJob->pickup_address ?? '') }}" required>
            </div>

            <div class="mb-4">
                <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                <input type="date" name="scheduled_date" id="scheduled_date"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('scheduled_date', $junkRemovalJob->scheduled_date->format('Y-m-d') ?? date('Y-m-d')) }}" required>
            </div>

            <div class="mb-4">
                <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Time</label>
                <input type="time" name="scheduled_time" id="scheduled_time"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('scheduled_time', $junkRemovalJob->scheduled_time ?? '09:00') }}" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description of Items</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                          placeholder="List items to be removed, e.g., Old furniture, appliances, debris" required>{{ old('description', $junkRemovalJob->description ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="estimated_price" class="block text-sm font-medium text-gray-700 mb-1">Estimated Price ($)</label>
                <input type="number" name="estimated_price" id="estimated_price" step="0.01" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('estimated_price', $junkRemovalJob->estimated_price ?? '') }}" required>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="Pending" {{ (old('status', $junkRemovalJob->status ?? '') == 'Pending') ? 'selected' : '' }}>Pending</option>
                    <option value="Scheduled" {{ (old('status', $junkRemovalJob->status ?? '') == 'Scheduled') ? 'selected' : '' }}>Scheduled</option>
                    <option value="In Progress" {{ (old('status', $junkRemovalJob->status ?? '') == 'In Progress') ? 'selected' : '' }}>In Progress</option>
                    <option value="Completed" {{ (old('status', $junkRemovalJob->status ?? '') == 'Completed') ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ (old('status', $junkRemovalJob->status ?? '') == 'Cancelled') ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('junk_removal_jobs.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    {{ isset($junkRemovalJob) ? 'Update Job' : 'Create Job' }}
                </button>
            </div>
        </form>
    </div>
@endsection