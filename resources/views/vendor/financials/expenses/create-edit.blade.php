@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ isset($expense) ? 'Edit Expense' : 'Add New Expense' }}</h2>

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
        <form action="{{ isset($expense) ? route('expenses.update', $expense->id) : route('expenses.store') }}" method="POST">
            @csrf
            @if(isset($expense))
                @method('PUT') {{-- Use PUT for updates --}}
            @endif

            <div class="mb-4">
                <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1">Date of Expense</label>
                <input type="date" name="expense_date" id="expense_date"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('expense_date', $expense->expense_date->format('Y-m-d') ?? date('Y-m-d')) }}" required>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="">Select a Category</option>
                    <option value="Fuel" {{ (old('category', $expense->category ?? '') == 'Fuel') ? 'selected' : '' }}>Fuel</option>
                    <option value="Maintenance" {{ (old('category', $expense->category ?? '') == 'Maintenance') ? 'selected' : '' }}>Maintenance</option>
                    <option value="Salaries" {{ (old('category', $expense->category ?? '') == 'Salaries') ? 'selected' : '' }}>Salaries</option>
                    <option value="Rent" {{ (old('category', $expense->category ?? '') == 'Rent') ? 'selected' : '' }}>Rent</option>
                    <option value="Utilities" {{ (old('category', $expense->category ?? '') == 'Utilities') ? 'selected' : '' }}>Utilities</option>
                    <option value="Supplies" {{ (old('category', $expense->category ?? '') == 'Supplies') ? 'selected' : '' }}>Supplies</option>
                    <option value="Other" {{ (old('category', $expense->category ?? '') == 'Other') ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                          placeholder="Brief description of the expense" required>{{ old('description', $expense->description ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                <input type="number" name="amount" id="amount" step="0.01" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('amount', $expense->amount ?? '') }}" required>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                          placeholder="Any additional notes about the expense">{{ old('notes', $expense->notes ?? '') }}</textarea>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('expenses.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    {{ isset($expense) ? 'Update Expense' : 'Add Expense' }}
                </button>
            </div>
        </form>
    </div>
@endsection