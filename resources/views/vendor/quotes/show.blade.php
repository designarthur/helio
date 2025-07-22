@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Quote Details: #{{ $quote->id }}</h2>

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
                <p class="text-sm font-medium text-gray-500">Quote ID:</p>
                <p class="text-lg text-gray-900 font-semibold">#{{ $quote->id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Customer:</p>
                <p class="text-lg text-gray-900">
                    <a href="{{ route('customers.show', $quote->customer->id ?? '#') }}" class="text-blue-600 hover:underline">
                        {{ $quote->customer->first_name ?? 'N/A' }} {{ $quote->customer->last_name ?? '' }}
                    </a>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Issue Date:</p>
                <p class="text-lg text-gray-900">{{ $quote->issue_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Expiration Date:</p>
                <p class="text-lg text-gray-900">{{ $quote->expiration_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status:</p>
                <p class="text-lg text-gray-900">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($quote->status == 'Accepted') bg-green-100 text-green-800
                        @elseif($quote->status == 'Pending' || $quote->status == 'Sent') bg-yellow-100 text-yellow-800
                        @elseif($quote->status == 'Rejected' || $quote->status == 'Expired') bg-red-100 text-red-800
                        @elseif($quote->status == 'Invoiced') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $quote->status }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Amount:</p>
                <p class="text-lg text-gray-900">${{ number_format($quote->total_amount, 2) }}</p>
            </div>
        </div>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500">Description/Service Details:</p>
            <p class="text-lg text-gray-900">{{ $quote->description }}</p>
        </div>

        @if($quote->invoice)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Associated Invoice:</p>
                <p class="text-lg text-gray-900">
                    <a href="{{ route('invoices.show', $quote->invoice->id) }}" class="text-blue-600 hover:underline">
                        #{{ $quote->invoice->id }}
                    </a>
                </p>
            </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            @if($quote->status === 'Accepted' && !$quote->invoice_id)
                <a href="{{ route('invoices.create', ['quote_id' => $quote->id]) }}" class="px-6 py-3 bg-purple-600 text-white rounded-md font-semibold hover:bg-purple-700 transition-colors duration-200">
                    Generate Invoice
                </a>
            @endif
            <a href="{{ route('quotes.edit', $quote->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                Edit Quote
            </a>
            <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this quote? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Delete Quote
                </button>
            </form>
            <a href="{{ route('quotes.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                Back to Quotes
            </a>
        </div>
    </div>
@endsection