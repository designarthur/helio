<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Invoice Details: {{ $invoice->invoice_number }}</title>
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
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen flex items-center justify-center py-8">

    {{-- Main content wrapper (simulating a modal or a dedicated page for details) --}}
    <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-3xl relative max-h-[90vh] overflow-y-auto">
        <button onclick="window.history.back()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl font-bold">&times;</button>
        <h3 class="text-2xl font-bold text-chili-red mb-6 border-b pb-3 border-gray-200">
            Invoice Details: <span id="detailInvoiceId">{{ $invoice->invoice_number }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Invoice Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Customer:</strong> <span id="detailInvoiceCustomerName">{{ $invoice->customer->name ?? 'N/A' }}</span></p>
                    <p><strong>Issue Date:</strong> <span id="detailInvoiceIssueDate">{{ $invoice->issue_date->format('Y-m-d') }}</span></p>
                    <p><strong>Due Date:</strong> <span id="detailInvoiceDueDate">{{ $invoice->due_date->format('Y-m-d') }}</span></p>
                    <p><strong>Total Amount:</strong> $<span id="detailInvoiceTotal">{{ number_format($invoice->total_amount, 2) }}</span></p>
                    <p><strong>Balance Due:</strong> $<span id="detailInvoiceBalanceDue">{{ number_format($invoice->balance_due, 2) }}</span></p>
                    <p><strong>Status:</strong>
                        <span id="detailInvoiceStatus" class="px-2 py-1 rounded-full text-xs font-bold uppercase text-white
                            @if($invoice->status == 'Paid') bg-green-600
                            @elseif($invoice->status == 'Partially Paid') bg-ut-orange
                            @elseif($invoice->status == 'Overdue') bg-red-600
                            @else bg-blue-600
                            @endif
                        ">{{ $invoice->status }}</span>
                    </p>
                    <p><strong>Linked Booking:</strong>
                        @if($invoice->linkedBooking)
                            <a href="{{ route('bookings.show', $invoice->linkedBooking->id) }}" class="text-blue-600 hover:underline">#{{ $invoice->linkedBooking->id }}</a>
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Linked Quote:</strong>
                        @if($invoice->linkedQuote)
                            <a href="{{ route('quotes.show', $invoice->linkedQuote->id) }}" class="text-blue-600 hover:underline">#{{ $invoice->linkedQuote->id }}</a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Invoice Items</h4>
                <ul id="detailInvoiceItemsList" class="list-disc list-inside ml-4 space-y-1">
                    @forelse($invoice->items as $item)
                        <li>{{ $item['description'] }} - ${{ number_format($item['amount'], 2) }}</li>
                    @empty
                        <li>No items on this invoice.</li>
                    @endforelse
                </ul>
            </div>

            @if($invoice->notes)
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Notes</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Payments Received</h4>
                <ul class="list-disc list-inside ml-4 space-y-1">
                    @forelse($invoice->payments as $payment)
                        <li>${{ number_format($payment->amount, 2) }} on {{ $payment->payment_date->format('Y-m-d') }} via {{ $payment->method }}</li>
                    @empty
                        <li>No payments recorded for this invoice yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('invoices.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
            <a href="{{ route('invoices.edit', $invoice->id) }}" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">Edit Invoice</a>

            @if($invoice->balance_due > 0)
                <form action="{{ route('invoices.markAsPaid', $invoice->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to mark this invoice as fully paid? This will record a payment for the remaining balance.');">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">Mark As Paid</button>
                </form>
            @endif
        </div>
    </div>
</body>
</html>