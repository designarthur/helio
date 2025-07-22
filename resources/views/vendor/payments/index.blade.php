@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Payments</h2>

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

    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
        {{-- You might have a "Record New Payment" button here, or link it from invoices --}}
        {{-- <a href="{{ route('payments.create') }}" class="px-6 py-3 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Record New Payment
        </a> --}}
        <div class="flex flex-wrap items-center gap-4">
            <form action="{{ route('payments.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <input type="text" id="paymentSearch" name="search" placeholder="Search by customer, invoice ID..."
                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                       value="{{ request('search') }}">

                <select id="methodFilter" name="method_filter"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto">
                    <option value="">All Methods</option>
                    <option value="Bank Transfer" @if(request('method_filter') == 'Bank Transfer') selected @endif>Bank Transfer</option>
                    <option value="Credit Card" @if(request('method_filter') == 'Credit Card') selected @endif>Credit Card</option>
                    <option value="Cash" @if(request('method_filter') == 'Cash') selected @endif>Cash</option>
                    <option value="Check" @if(request('method_filter') == 'Check') selected @endif>Check</option>
                </select>

                <input type="date" id="dateFilter" name="date_filter"
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                       value="{{ request('date_filter') }}">

                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors duration-200">Apply Filters</button>
                <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors duration-200">Reset</a>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-8">
        <table class="min-w-full divide-y divide-gray-200" id="paymentsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($payment->invoice)
                                <a href="{{ route('invoices.show', $payment->invoice->id) }}" class="text-indigo-600 hover:text-indigo-900">#{{ $payment->invoice->id }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->customer->first_name ?? 'N/A' }} {{ $payment->customer->last_name ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_method }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('payments.show', $payment->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors duration-200">View</a>
                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this payment record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
@endsection