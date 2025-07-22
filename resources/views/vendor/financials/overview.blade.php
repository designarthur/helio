@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Financials Overview</h2>

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
        <span class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="overview">Overview</span>
        <a href="{{ route('financials.expenses.index') }}" class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="expenses">Expenses</a>
        <a href="{{ route('financials.reports') }}" class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="reports">Reports</a>
    </div>

    <div id="finance-tab-content-overview" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Financial Metric Cards --}}
            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Current Balance</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">${{ number_format($currentBalance, 2) }}</p>
                    <span class="text-sm text-green-600 flex items-center gap-1">
                        <i class="fas fa-arrow-up text-xs"></i> Stable
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-wallet text-2xl text-blue-600"></i>
                </div>
            </div>

            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Pending Payments</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">${{ number_format($pendingPayments, 2) }}</p>
                    <span class="text-sm text-orange-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle text-xs"></i> Action Required
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
            </div>

            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Total Expenses (Month)</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">${{ number_format($monthlyExpenses, 2) }}</p>
                    <span class="text-sm text-red-600 flex items-center gap-1">
                        <i class="fas fa-arrow-up text-xs"></i> +15% from last month
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-minus-circle text-2xl text-red-600"></i>
                </div>
            </div>

            <div class="metric-card bg-white p-6 rounded-lg shadow-md flex items-center justify-between transform transition-transform duration-200 hover:scale-105 cursor-pointer">
                <div>
                    <h3 class="text-lg text-gray-500 mb-1 font-normal">Revenue vs. Expenses</h3>
                    <p class="text-4xl font-bold text-gray-900 mb-1">
                        @if ($revenueVsExpenses >= 0)
                            <span class="text-green-600">${{ number_format($revenueVsExpenses, 2) }}</span>
                        @else
                            <span class="text-red-600">-${{ number_format(abs($revenueVsExpenses), 2) }}</span>
                        @endif
                    </p>
                    <span class="text-sm {{ $revenueVsExpenses >= 0 ? 'text-green-600' : 'text-red-600' }} flex items-center gap-1">
                        @if ($revenueVsExpenses >= 0)
                            <i class="fas fa-arrow-up text-xs"></i> Profit
                        @else
                            <i class="fas fa-arrow-down text-xs"></i> Loss
                        @endif
                    </span>
                </div>
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Charts --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Revenue & Expense Trend</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">View Details</button>
                </div>
                <canvas id="revenueExpenseChart" class="max-h-[300px]"></canvas>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center pb-3 mb-4 border-b border-gray-200">
                    <h3 class="text-xl text-gray-800 font-semibold m-0">Payment Methods Breakdown</h3>
                    <button class="px-4 py-2 bg-chili-red text-white rounded-md text-sm font-semibold hover:bg-tangelo transition-colors duration-200">View Details</button>
                </div>
                <canvas id="paymentMethodChart" class="max-h-[300px]"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl text-gray-800 font-semibold pb-3 mb-4 border-b border-gray-200">Recent Transactions</h3>
            <div class="space-y-4">
                @forelse($recentTransactions as $transaction)
                    <div class="flex justify-between items-center pb-4 border-b border-dashed border-gray-200 last:border-b-0">
                        <div>
                            <strong class="block text-gray-800 mb-1">{{ $transaction['description'] }}</strong>
                            <span class="text-gray-500 text-sm">{{ $transaction['date']->format('M d, Y') }} - {{ $transaction['type'] }}</span>
                        </div>
                        <span class="text-lg font-bold {{ $transaction['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction['amount'] >= 0 ? '+' : '-' }}${{ number_format(abs($transaction['amount']), 2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No recent transactions found.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data passed from Laravel Controller
        const revenueExpenseChartData = @json($revenueExpenseChartData);
        const paymentMethodChartData = @json($paymentMethodChartData);

        let revenueExpenseChartInstance = null;
        let paymentMethodChartInstance = null;

        function renderFinancialCharts() {
            const revenueCtx = document.getElementById('revenueExpenseChart');
            const paymentCtx = document.getElementById('paymentMethodChart');

            if (revenueExpenseChartInstance) revenueExpenseChartInstance.destroy();
            if (paymentMethodChartInstance) paymentMethodChartInstance.destroy();

            if (revenueCtx) {
                revenueExpenseChartInstance = new Chart(revenueCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: revenueExpenseChartData.labels,
                        datasets: [
                            {
                                label: 'Revenue',
                                data: revenueExpenseChartData.datasets[0].data,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Expenses',
                                data: revenueExpenseChartData.datasets[1].data,
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            title: {
                                display: false,
                                text: 'Revenue & Expense Trend'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Amount ($)'
                                }
                            }
                        }
                    }
                });
            } else {
                console.error("Revenue Expense chart canvas element not found.");
            }

            if (paymentCtx) {
                paymentMethodChartInstance = new Chart(paymentCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: paymentMethodChartData.labels,
                        datasets: [{
                            data: paymentMethodChartData.datasets[0].data,
                            backgroundColor: [
                                '#EA3A26', // Chili Red
                                '#FF8600', // UT Orange
                                '#4CAF50', // Green
                                '#2196F3', // Blue
                                '#FFC107'  // Amber
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            title: {
                                display: false,
                                text: 'Payment Methods Breakdown'
                            }
                        }
                    }
                });
            } else {
                console.error("Payment Method chart canvas element not found.");
            }
        }

        // Set active financial tab
        document.addEventListener('DOMContentLoaded', () => {
            renderFinancialCharts();

            const financialTabs = document.querySelectorAll('.finance-tab');
            financialTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Assuming 'overview' is the default active tab for financials
            document.querySelector('.finance-tab[data-tab="overview"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.finance-tab[data-tab="overview"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection