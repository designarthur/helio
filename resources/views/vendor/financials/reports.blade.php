<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Financial Reports</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'chili-red': '#EA3A26',
                        'ut-orange': '#FF8600',
                        'tangelo': '#F54F1D',
                    },
                }
            }
        }
    </script>
    <style>
        /* Existing custom styles from your HTML, if any */
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen">

    {{-- Main content wrapper - for now, this will be a full page, later part of a layout --}}
    <div class="p-8">
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
        @if (session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Info:</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
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
            <a href="{{ route('financials.overview') }}" class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Overview</a>
            <a href="{{ route('financials.reports') }}" class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Reports</a>
            <a href="{{ route('financials.expenses.index') }}" class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Expenses</a>
            <span class="finance-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Settings (Conceptual)</span>
        </div>

        <div id="finance-tab-reports" class="finance-content-view">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Financial Reports</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Revenue Reports</h4>
                    <div class="space-y-4">
                        <form action="{{ route('financials.reports') }}" method="GET">
                            <input type="hidden" name="report_type" value="revenueByCustomer">
                            <button type="submit" class="w-full text-left px-4 py-3 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">Revenue by Customer</button>
                        </form>
                        <form action="{{ route('financials.reports') }}" method="GET">
                            <input type="hidden" name="report_type" value="revenueByEquipment">
                            <button type="submit" class="w-full text-left px-4 py-3 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">Revenue by Equipment Type</button>
                        </form>
                        <form action="{{ route('financials.reports') }}" method="GET">
                            <input type="hidden" name="report_type" value="salesTax">
                            <button type="submit" class="w-full text-left px-4 py-3 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">Sales Tax Collected</button>
                        </form>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Accounts Receivable (A/R)</h4>
                    <div class="space-y-4">
                        <form action="{{ route('financials.reports') }}" method="GET">
                            <input type="hidden" name="report_type" value="arAging">
                            <button type="submit" class="w-full text-left px-4 py-3 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">A/R Aging Report</button>
                        </form>
                        <form action="{{ route('financials.reports') }}" method="GET">
                            <input type="hidden" name="report_type" value="customerStatements">
                            <button type="submit" class="w-full text-left px-4 py-3 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">Customer Statements (Summary)</button>
                        </form>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md col-span-1 md:col-span-2">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Other Reports (Conceptual)</h4>
                    <div class="space-y-4">
                        <p class="text-gray-600">This section would include advanced reports like:</p>
                        <ul class="list-disc list-inside ml-4 text-sm space-y-1">
                            <li>Profit & Loss (P&L) Statement</li>
                            <li>Cash Flow Statement</li>
                            <li>Equipment Utilization Report</li>
                            <li>Maintenance Cost Analysis per Unit</li>
                            <li>Cancellation & Refund Reports</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            {{-- Report Output Section --}}
            @if(isset($reportData)) {{-- Only show this section if reportData is passed (i.e., a report was generated) --}}
            <div id="reportOutput" class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h4 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300" id="reportTitle">{{ $reportTitle }}</h4>
                <div id="reportContent" class="overflow-x-auto">
                    @if(is_string($reportData)) {{-- For conceptual messages directly as string --}}
                        <p class="text-gray-600">{{ $reportData }}</p>
                    @elseif(is_object($reportData) && property_exists($reportData, 'message')) {{-- For conceptual objects with a message property --}}
                        <p class="text-gray-600">{{ $reportData->message }}</p>
                    @elseif(request('report_type') == 'revenueByCustomer')
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($reportData as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $row->customer->name ?? 'Unknown Customer' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($row->total_revenue, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No data for this report.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @elseif(request('report_type') == 'revenueByEquipment')
                         <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipment Type / Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($reportData as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $row['type'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($row['total_revenue'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No data for this report.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @elseif(request('report_type') == 'arAging')
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aging Bucket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Due</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $hasData = false; @endphp
                                @foreach($reportData as $bucketName => $invoicesInBucket)
                                    @forelse($invoicesInBucket as $invoice)
                                    @php $hasData = true; @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $bucketName }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->invoice_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->customer->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($invoice->balance_due, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->due_date->format('Y-m-d') }}</td>
                                    </tr>
                                    @empty
                                    @endforelse
                                @endforeach
                                @if(!$hasData)
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No data for this report.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-600">Select a report from the left to generate its output here.</p>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </body>
    </html>