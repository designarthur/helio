@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ isset($booking) ? 'Edit Booking' : 'Create New Booking' }}</h2>

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
        <form action="{{ isset($booking) ? route('bookings.update', $booking->id) : route('bookings.store') }}" method="POST">
            @csrf
            @if(isset($booking))
                @method('PUT') {{-- Use PUT for updates --}}
            @endif

            <div class="mb-4">
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <select name="customer_id" id="customer_id"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="">Select a Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ (old('customer_id', $booking->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>
                            {{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-1">Equipment</label>
                <select name="equipment_id" id="equipment_id"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="">Select Equipment</option>
                    @foreach($equipment as $item)
                        <option value="{{ $item->id }}" {{ (old('equipment_id', $booking->equipment_id ?? '') == $item->id) ? 'selected' : '' }}>
                            {{ $item->type }} ({{ $item->size }}) - ID: {{ $item->internal_id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('start_date', $booking->start_date->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                </div>

                <div class="mb-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('end_date', $booking->end_date->format('Y-m-d') ?? \Carbon\Carbon::now()->addDays(1)->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                <input type="text" name="delivery_address" id="delivery_address"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('delivery_address', $booking->delivery_address ?? '') }}" required>
            </div>

            <div class="mb-4">
                <label for="pickup_address" class="block text-sm font-medium text-gray-700 mb-1">Pickup Address (optional, leave blank if same as delivery)</label>
                <input type="text" name="pickup_address" id="pickup_address"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('pickup_address', $booking->pickup_address ?? '') }}">
            </div>

            <div class="mb-4">
                <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Total Price ($)</label>
                <input type="number" name="total_price" id="total_price" step="0.01" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('total_price', $booking->total_price ?? '') }}" required>
            </div>

            <div class="mb-4">
                <label for="driver_id" class="block text-sm font-medium text-gray-700 mb-1">Assign Driver (optional)</label>
                <select name="driver_id" id="driver_id"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm">
                    <option value="">No Driver Assigned</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ (old('driver_id', $booking->driver_id ?? '') == $driver->id) ? 'selected' : '' }}>
                            {{ $driver->first_name }} {{ $driver->last_name }} ({{ $driver->status }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="Pending" {{ (old('status', $booking->status ?? '') == 'Pending') ? 'selected' : '' }}>Pending</option>
                    <option value="Confirmed" {{ (old('status', $booking->status ?? '') == 'Confirmed') ? 'selected' : '' }}>Confirmed</option>
                    <option value="On-Going" {{ (old('status', $booking->status ?? '') == 'On-Going') ? 'selected' : '' }}>On-Going</option>
                    <option value="Completed" {{ (old('status', $booking->status ?? '') == 'Completed') ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ (old('status', $booking->status ?? '') == 'Cancelled') ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                          placeholder="Any specific instructions or additional details">{{ old('notes', $booking->notes ?? '') }}</textarea>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('bookings.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    {{ isset($booking) ? 'Update Booking' : 'Create Booking' }}
                </button>
            </div>
        </form>
    </div>
@endsection