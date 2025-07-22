@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Invoice Details: #{{ $invoice->id }}</h2>

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
                <p class="text-sm font-medium text-gray-500">Invoice ID:</p>
                <p class="text-lg text-gray-900 font-semibold">#{{ $invoice->id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Customer:</p>
                <p class="text-lg text-gray-900">
                    <a href="{{ route('customers.show', $invoice->customer->id ?? '#') }}" class="text-blue-600 hover:underline">
                        {{ $invoice->customer->first_name ?? 'N/A' }} {{ $invoice->customer->last_name ?? '' }}
                    </a>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Issue Date:</p>
                <p class="text-lg text-gray-900">{{ $invoice->issue_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Due Date:</p>
                <p class="text-lg text-gray-900">{{ $invoice->due_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status:</p>
                <p class="text-lg text-gray-900">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($invoice->status == 'Paid') bg-green-100 text-green-800
                        @elseif($invoice->status == 'Unpaid') bg-yellow-100 text-yellow-800
                        @elseif($invoice->status == 'Partially Paid') bg-orange-100 text-orange-800
                        @elseif($invoice->status == 'Overdue') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $invoice->status }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Amount:</p>
                <p class="text-lg text-gray-900">${{ number_format($invoice->total_amount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Amount Paid:</p>
                <p class="text-lg text-gray-900">${{ number_format($invoice->amount_paid, 2) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Amount Due:</p>
                <p class="text-lg text-gray-900 font-bold text-chili-red">${{ number_format($invoice->amount_due, 2) }}</p>
            </div>
            @if($invoice->quote)
                <div>
                    <p class="text-sm font-medium text-gray-500">From Quote:</p>
                    <p class="text-lg text-gray-900">
                        <a href="{{ route('quotes.show', $invoice->quote->id) }}" class="text-blue-600 hover:underline">
                            #{{ $invoice->quote->id }}
                        </a>
                    </p>
                </div>
            @endif
        </div>

        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-t pt-4 mt-6">Items on Invoice</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoice->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No items on this invoice.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-t pt-4 mt-6">Payment History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoice->payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_method }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->transaction_id ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No payments recorded for this invoice.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            @if($invoice->status !== 'Paid' && $invoice->amount_due > 0)
                <a href="{{ route('payments.record', ['invoice_id' => $invoice->id]) }}" class="px-6 py-3 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">
                    Record Payment
                </a>
            @endif
            <a href="{{ route('invoices.edit', $invoice->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                Edit Invoice
            </a>
            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this invoice? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Delete Invoice
                </button>
            </form>
            <a href="{{ route('invoices.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                Back to Invoices
            </a>
        </div>
    </div>
@endsection