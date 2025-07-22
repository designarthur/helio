@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ isset($quote) ? 'Edit Quote' : 'Create New Quote' }}</h2>

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
        <form action="{{ isset($quote) ? route('quotes.update', $quote->id) : route('quotes.store') }}" method="POST">
            @csrf
            @if(isset($quote))
                @method('PUT') {{-- Use PUT for updates --}}
            @endif

            <div class="mb-4">
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <select name="customer_id" id="customer_id"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="">Select a Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ (old('customer_id', $quote->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>
                            {{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-1">Issue Date</label>
                    <input type="date" name="issue_date" id="issue_date"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('issue_date', $quote->issue_date->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                </div>

                <div class="mb-4">
                    <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
                    <input type="date" name="expiration_date" id="expiration_date"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('expiration_date', $quote->expiration_date->format('Y-m-d') ?? \Carbon\Carbon::now()->addDays(7)->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description/Service Details</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                          placeholder="Detailed description of services or items quoted" required>{{ old('description', $quote->description ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-1">Total Amount ($)</label>
                <input type="number" name="total_amount" id="total_amount" step="0.01" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('total_amount', $quote->total_amount ?? '') }}" required>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="Pending" {{ (old('status', $quote->status ?? '') == 'Pending') ? 'selected' : '' }}>Pending</option>
                    <option value="Sent" {{ (old('status', $quote->status ?? '') == 'Sent') ? 'selected' : '' }}>Sent</option>
                    <option value="Accepted" {{ (old('status', $quote->status ?? '') == 'Accepted') ? 'selected' : '' }}>Accepted</option>
                    <option value="Rejected" {{ (old('status', $quote->status ?? '') == 'Rejected') ? 'selected' : '' }}>Rejected</option>
                    <option value="Expired" {{ (old('status', $quote->status ?? '') == 'Expired') ? 'selected' : '' }}>Expired</option>
                    <option value="Invoiced" {{ (old('status', $quote->status ?? '') == 'Invoiced') ? 'selected' : '' }}>Invoiced</option>
                </select>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('quotes.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    {{ isset($quote) ? 'Update Quote' : 'Create Quote' }}
                </button>
            </div>
        </form>
    </div>
@endsection