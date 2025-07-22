@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Record Payment for Invoice #{{ $invoice->id ?? 'N/A' }}</h2>

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
        @if(isset($invoice))
            <div class="mb-6 border-b pb-4">
                <p class="text-lg font-semibold text-gray-800">Invoice Details:</p>
                <p class="text-md text-gray-700">Customer: {{ $invoice->customer->first_name ?? 'N/A' }} {{ $invoice->customer->last_name ?? '' }}</p>
                <p class="text-md text-gray-700">Total Amount: ${{ number_format($invoice->total_amount, 2) }}</p>
                <p class="text-md text-gray-700">Amount Due: <span class="font-bold text-chili-red">${{ number_format($invoice->amount_due, 2) }}</span></p>
                <p class="text-md text-gray-700">Due Date: {{ $invoice->due_date->format('M d, Y') }}</p>
            </div>
        @else
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Warning!</strong>
                <span class="block sm:inline">No invoice selected for payment. Please select an invoice from the invoices list.</span>
            </div>
        @endif

        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id ?? '' }}">
            <input type="hidden" name="customer_id" value="{{ $invoice->customer_id ?? '' }}">

            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Payment Amount ($)</label>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                       max="{{ $invoice->amount_due ?? '' }}" {{-- Restrict max to amount due --}}
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('amount', $invoice->amount_due ?? '') }}" required>
                @if(isset($invoice))
                    <p class="mt-2 text-xs text-gray-500">Max amount: ${{ number_format($invoice->amount_due, 2) }}</p>
                @endif
            </div>

            <div class="mb-4">
                <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                <input type="date" name="payment_date" id="payment_date"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('payment_date', date('Y-m-d')) }}" required>
            </div>

            <div class="mb-4">
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select name="payment_method" id="payment_method"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="">Select Method</option>
                    <option value="Bank Transfer" {{ (old('payment_method') == 'Bank Transfer') ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="Credit Card" {{ (old('payment_method') == 'Credit Card') ? 'selected' : '' }}>Credit Card</option>
                    <option value="Cash" {{ (old('payment_method') == 'Cash') ? 'selected' : '' }}>Cash</option>
                    <option value="Check" {{ (old('payment_method') == 'Check') ? 'selected' : '' }}>Check</option>
                    <option value="Online Payment" {{ (old('payment_method') == 'Online Payment') ? 'selected' : '' }}>Online Payment</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-1">Transaction ID (optional)</label>
                <input type="text" name="transaction_id" id="transaction_id"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('transaction_id') }}" placeholder="e.g., Stripe charge ID, bank reference">
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('invoices.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
@endsection