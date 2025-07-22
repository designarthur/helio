@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Customer Details: {{ $customer->first_name }} {{ $customer->last_name }}</h2>

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
                <p class="text-lg text-gray-900 font-semibold">{{ $customer->first_name }} {{ $customer->last_name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Email:</p>
                <p class="text-lg text-gray-900">{{ $customer->email }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Phone Number:</p>
                <p class="text-lg text-gray-900">{{ $customer->phone_number }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Customer Type:</p>
                <p class="text-lg text-gray-900">{{ $customer->customer_type }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Address:</p>
                <p class="text-lg text-gray-900">
                    {{ $customer->address }}<br>
                    {{ $customer->city }}, {{ $customer->state }} {{ $customer->zip_code }}<br>
                    {{ $customer->country }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Bookings:</p>
                <p class="text-lg text-gray-900">{{ $customer->bookings_count ?? 0 }}</p>
            </div>
        </div>

        @if($customer->notes)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Notes:</p>
                <p class="text-lg text-gray-900">{{ $customer->notes }}</p>
            </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('customers.edit', $customer->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                Edit Customer
            </a>
            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this customer and all associated data?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Delete Customer
                </button>
            </form>
            <a href="{{ route('customers.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                Back to Customers
            </a>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Customer's Bookings</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customer->bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $booking->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $booking->equipment->type ?? 'N/A' }} ({{ $booking->equipment->size ?? '' }})
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->start_date->format('Y-m-d') }} - {{ $booking->end_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($booking->status == 'Confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status == 'Pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($booking->total_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No bookings for this customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Customer's Invoices</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Due</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customer->invoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->issue_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->due_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($invoice->amount_due, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($invoice->status == 'Paid') bg-green-100 text-green-800
                                    @elseif($invoice->status == 'Unpaid') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No invoices for this customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection