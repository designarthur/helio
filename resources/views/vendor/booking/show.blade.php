@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Booking Details: #{{ $booking->id }}</h2>

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
                <p class="text-sm font-medium text-gray-500">Booking ID:</p>
                <p class="text-lg text-gray-900 font-semibold">#{{ $booking->id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Customer:</p>
                <p class="text-lg text-gray-900">
                    <a href="{{ route('customers.show', $booking->customer->id ?? '#') }}" class="text-blue-600 hover:underline">
                        {{ $booking->customer->first_name ?? 'N/A' }} {{ $booking->customer->last_name ?? '' }}
                    </a>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Equipment:</p>
                <p class="text-lg text-gray-900">
                    @if($booking->equipment)
                        <a href="{{ route('equipment.show', $booking->equipment->id) }}" class="text-blue-600 hover:underline">
                            {{ $booking->equipment->type }} ({{ $booking->equipment->size }}) - {{ $booking->equipment->internal_id }}
                        </a>
                    @else
                        N/A
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Start Date:</p>
                <p class="text-lg text-gray-900">{{ $booking->start_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">End Date:</p>
                <p class="text-lg text-gray-900">{{ $booking->end_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status:</p>
                <p class="text-lg text-gray-900">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($booking->status == 'Confirmed') bg-green-100 text-green-800
                        @elseif($booking->status == 'Pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status == 'Cancelled') bg-red-100 text-red-800
                        @elseif($booking->status == 'Completed') bg-blue-100 text-blue-800
                        @elseif($booking->status == 'On-Going') bg-purple-100 text-purple-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $booking->status }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Delivery Address:</p>
                <p class="text-lg text-gray-900">{{ $booking->delivery_address }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pickup Address:</p>
                <p class="text-lg text-gray-900">{{ $booking->pickup_address ?? 'Same as Delivery' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Price:</p>
                <p class="text-lg text-gray-900 font-bold">${{ number_format($booking->total_price, 2) }}</p>
            </div>
        </div>

        @if($booking->notes)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Notes:</p>
                <p class="text-lg text-gray-900">{{ $booking->notes }}</p>
            </div>
        @endif

        @if($booking->driver)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Assigned Driver:</p>
                <p class="text-lg text-gray-900">
                    <a href="{{ route('drivers.show', $booking->driver->id) }}" class="text-blue-600 hover:underline">
                        {{ $booking->driver->first_name }} {{ $booking->driver->last_name }}
                    </a>
                </p>
            </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            @if($booking->status === 'Confirmed' && !$booking->invoice_id)
                <a href="{{ route('invoices.create', ['booking_id' => $booking->id]) }}" class="px-6 py-3 bg-purple-600 text-white rounded-md font-semibold hover:bg-purple-700 transition-colors duration-200">
                    Generate Invoice
                </a>
            @endif
            <a href="{{ route('bookings.edit', $booking->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                Edit Booking
            </a>
            <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this booking? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Delete Booking
                </button>
            </form>
            <a href="{{ route('bookings.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                Back to Bookings
            </a>
        </div>
    </div>
@endsection