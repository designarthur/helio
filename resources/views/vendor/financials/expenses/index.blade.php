@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Expenses Management</h2>

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

    <div class="flex border-b border-gray-200 mb-8 space-x-6">
        {{-- Financials Tabs --}}
        <a href="{{ route('financials.overview') }}" class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="overview">Overview</a>
        <span class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="expenses">Expenses</span>
        <a href="{{ route('financials.reports') }}" class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="reports">Reports</a>
    </div>

    <div id="finance-tab-content-expenses" class="tab-content">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <a href="{{ route('expenses.create') }}" class="px-6 py-3 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add New Expense
            </a>
            <div class="flex flex-wrap items-center gap-4">
                <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                    <input type="text" id="expenseSearch" name="search" placeholder="Search by description, amount..."
                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                           value="{{ request('search') }}">

                    <select id="categoryFilter" name="category_filter"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto">
                        <option value="">All Categories</option>
                        <option value="Fuel" @if(request('category_filter') == 'Fuel') selected @endif>Fuel</option>
                        <option value="Maintenance" @if(request('category_filter') == 'Maintenance') selected @endif>Maintenance</option>
                        <option value="Salaries" @if(request('category_filter') == 'Salaries') selected @endif>Salaries</option>
                        <option value="Rent" @if(request('category_filter') == 'Rent') selected @endif>Rent</option>
                        <option value="Utilities" @if(request('category_filter') == 'Utilities') selected @endif>Utilities</option>
                        <option value="Supplies" @if(request('category_filter') == 'Supplies') selected @endif>Supplies</option>
                        <option value="Other" @if(request('category_filter') == 'Other') selected @endif>Other</option>
                    </select>

                    <input type="date" id="dateFilter" name="date_filter"
                           class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                           value="{{ request('date_filter') }}">

                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors duration-200">Apply Filters</button>
                    <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors duration-200">Reset</a>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-8">
            <table class="min-w-full divide-y divide-gray-200" id="expensesTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $expense->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->category }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($expense->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('expenses.show', $expense->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors duration-200">View</a>
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="text-chili-red hover:text-tangelo mr-3 transition-colors duration-200">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    </div>

    <script>
        // Set active financial tab
        document.addEventListener('DOMContentLoaded', () => {
            const financialTabs = document.querySelectorAll('.finance-tab');
            financialTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Set 'expenses' as active
            document.querySelector('.finance-tab[data-tab="expenses"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.finance-tab[data-tab="expenses"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection