@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Expense Details: #{{ $expense->id }}</h2>

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
                <p class="text-sm font-medium text-gray-500">Expense ID:</p>
                <p class="text-lg text-gray-900 font-semibold">#{{ $expense->id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Date:</p>
                <p class="text-lg text-gray-900">{{ $expense->expense_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Category:</p>
                <p class="text-lg text-gray-900">{{ $expense->category }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Amount:</p>
                <p class="text-lg text-gray-900">${{ number_format($expense->amount, 2) }}</p>
            </div>
        </div>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500">Description:</p>
            <p class="text-lg text-gray-900">{{ $expense->description }}</p>
        </div>

        @if($expense->notes)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Notes:</p>
                <p class="text-lg text-gray-900">{{ $expense->notes }}</p>
            </div>
        @endif

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500">Recorded By:</p>
            <p class="text-lg text-gray-900">{{ $expense->user->name ?? 'N/A' }}</p>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('expenses.edit', $expense->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                Edit Expense
            </a>
            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this expense? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Delete Expense
                </button>
            </form>
            <a href="{{ route('expenses.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                Back to Expenses
            </a>
        </div>
    </div>
@endsection