@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Invoices</h2>

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
        {{-- You might have a "Create New Invoice" button, but often invoices are generated from quotes/bookings --}}
        {{-- <a href="{{ route('invoices.create') }}" class="px-6 py-3 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Create New Invoice
        </a> --}}
        <div class="flex flex-wrap items-center gap-4">
            <form action="{{ route('invoices.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <input type="text" id="invoiceSearch" name="search" placeholder="Search by customer, ID..."
                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                       value="{{ request('search') }}">

                <select id="statusFilter" name="status_filter"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto">
                    <option value="">All Statuses</option>
                    <option value="Unpaid" @if(request('status_filter') == 'Unpaid') selected @endif>Unpaid</option>
                    <option value="Paid" @if(request('status_filter') == 'Paid') selected @endif>Paid</option>
                    <option value="Partially Paid" @if(request('status_filter') == 'Partially Paid') selected @endif>Partially Paid</option>
                    <option value="Overdue" @if(request('status_filter') == 'Overdue') selected @endif>Overdue</option>
                </select>

                <input type="date" id="dateFilter" name="date_filter"
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                       value="{{ request('date_filter') }}">

                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors duration-200">Apply Filters</button>
                <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors duration-200">Reset</a>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-8">
        <table class="min-w-full divide-y divide-gray-200" id="invoicesTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Due</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $invoice->customer->first_name ?? 'N/A' }} {{ $invoice->customer->last_name ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->issue_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->due_date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($invoice->status == 'Paid') bg-green-100 text-green-800
                                @elseif($invoice->status == 'Unpaid') bg-yellow-100 text-yellow-800
                                @elseif($invoice->status == 'Partially Paid') bg-orange-100 text-orange-800
                                @elseif($invoice->status == 'Overdue') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($invoice->amount_due, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors duration-200">View</a>
                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="text-chili-red hover:text-tangelo mr-3 transition-colors duration-200">Edit</a>
                            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">Delete</button>
                            </form>
                            @if($invoice->status !== 'Paid' && $invoice->amount_due > 0)
                                <a href="{{ route('payments.record', $invoice->id) }}" class="text-purple-600 hover:text-purple-900 ml-3 transition-colors duration-200">Record Payment</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
@endsection