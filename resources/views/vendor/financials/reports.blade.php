@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Financial Reports</h2>

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
        <a href="{{ route('financials.expenses.index') }}" class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="expenses">Expenses</a>
        <span class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="reports">Reports</span>
    </div>

    <div id="finance-tab-content-reports" class="tab-content bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Generate Financial Reports</h3>
        <p class="text-gray-600 mb-6">
            Generate detailed financial reports such as Profit & Loss, Balance Sheet, and Cash Flow.
        </p>

        <form action="{{ route('financials.generateReport') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                    <select name="report_type" id="report_type"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                            required>
                        <option value="">Select a Report Type</option>
                        <option value="profit_loss">Profit & Loss (Income Statement)</option>
                        <option value="balance_sheet">Balance Sheet</option>
                        <option value="cash_flow">Cash Flow Statement</option>
                        <option value="aged_receivables">Aged Receivables</option>
                        <option value="aged_payables">Aged Payables</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select name="date_range" id="date_range"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                            required>
                        <option value="">Select a Date Range</option>
                        <option value="this_month">This Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="last_month">Last Month</option>
                        <option value="last_quarter">Last Quarter</option>
                        <option value="last_year">Last Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
            </div>

            <div id="customDateRangeFields" class="grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm">
                </div>
            </div>

            <div class="mb-6">
                <label for="output_format" class="block text-sm font-medium text-gray-700 mb-1">Output Format</label>
                <select name="output_format" id="output_format"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="pdf">PDF</option>
                    <option value="csv">CSV</option>
                    <option value="xlsx">Excel (XLSX)</option>
                </select>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    Generate Report
                </button>
            </div>
        </form>

        <h3 class="text-xl font-semibold text-gray-800 mb-4 mt-10">Previously Generated Reports</h3>
        <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-8">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated On</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($generatedReports as $report)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $report['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report['type'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report['date_range'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report['generated_at']->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ strtoupper($report['format']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ $report['download_link'] }}" class="text-indigo-600 hover:text-indigo-900 mr-3" download>Download</a>
                                <form action="{{ route('financials.deleteReport', $report['id']) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this report?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No reports generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dateRangeSelect = document.getElementById('date_range');
                const customDateRangeFields = document.getElementById('customDateRangeFields');

                dateRangeSelect.addEventListener('change', function () {
                    if (this.value === 'custom') {
                        customDateRangeFields.classList.remove('hidden');
                    } else {
                        customDateRangeFields.classList.add('hidden');
                    }
                });

                // Trigger change on load if 'custom' was already selected (e.g., after validation error)
                if (dateRangeSelect.value === 'custom') {
                    customDateRangeFields.classList.remove('hidden');
                }

                // Set active financial tab
                const financialTabs = document.querySelectorAll('.finance-tab');
                financialTabs.forEach(tab => {
                    tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                    tab.classList.add('text-gray-500', 'border-transparent');
                });
                // Set 'reports' as active
                document.querySelector('.finance-tab[data-tab="reports"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
                document.querySelector('.finance-tab[data-tab="reports"]').classList.remove('text-gray-500', 'border-transparent');
            });
        </script>
@endsection