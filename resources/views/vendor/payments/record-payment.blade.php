<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($payment)) Edit Payment: {{ $payment->id }} @else Record New Payment @endif</title>
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

    {{-- Main content wrapper (simulating a modal or a dedicated page for the form) --}}
    <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-md relative max-h-[90vh] overflow-y-auto">
        <button onclick="window.history.back()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl font-bold">&times;</button>
        <h3 class="text-2xl font-bold text-chili-red mb-6 border-b pb-3 border-gray-200">
            @if(isset($payment)) Edit Payment: {{ $payment->id }} @else Record New Payment @endif
        </h3>

        <form id="paymentForm" method="POST" action="@if(isset($payment)) {{ route('payments.update', $payment->id) }} @else {{ route('payments.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($payment)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="space-y-4">
                <input type="hidden" id="paymentId" name="id" value="{{ $payment->id ?? '' }}">

                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer:</label>
                    <select id="customer_id" name="customer_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (old('customer_id', $payment->customer_id ?? ($selectedInvoice->customer_id ?? '')) == $customer->id) ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->company ?? 'Residential' }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="invoice_id" class="block text-sm font-medium text-gray-700 mb-1">Invoice ID (Optional):</label>
                    <select id="invoice_id" name="invoice_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">No Specific Invoice</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ (old('invoice_id', $payment->invoice_id ?? ($selectedInvoice->id ?? '')) == $invoice->id) ? 'selected' : '' }}
                                data-balance-due="{{ $invoice->balance_due }}">
                                {{ $invoice->invoice_number }} (Balance: ${{ number_format($invoice->balance_due, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('invoice_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount ($):</label>
                    <input type="number" id="amount" name="amount" min="0.01" step="0.01" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('amount', $payment->amount ?? ($selectedInvoice->balance_due ?? '')) }}">
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method:</label>
                    <select id="method" name="method" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Method</option>
                        <option value="Credit Card" {{ (old('method', $payment->method ?? '') == 'Credit Card') ? 'selected' : '' }}>Credit Card</option>
                        <option value="ACH" {{ (old('method', $payment->method ?? '') == 'ACH') ? 'selected' : '' }}>ACH/Bank Transfer</option>
                        <option value="Check" {{ (old('method', $payment->method ?? '') == 'Check') ? 'selected' : '' }}>Check</option>
                        <option value="Cash" {{ (old('method', $payment->method ?? '') == 'Cash') ? 'selected' : '' }}>Cash</option>
                        <option value="Manual Mark as Paid" {{ (old('method', $payment->method ?? '') == 'Manual Mark as Paid') ? 'selected' : '' }}>Manual Mark as Paid</option>
                    </select>
                    @error('method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date:</label>
                    <input type="date" id="payment_date" name="payment_date" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d') : now()->format('Y-m-d')) }}">
                    @error('payment_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-1">Transaction ID (Optional):</label>
                    <input type="text" id="transaction_id" name="transaction_id" placeholder="e.g., Stripe_ch_123xyz"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('transaction_id', $payment->transaction_id ?? '') }}">
                    @error('transaction_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional):</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Any specific notes about this payment."
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('notes', $payment->notes ?? '') }}
                    </textarea>
                    @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('payments.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        @if(isset($payment)) Save Changes @else Record Payment @endif
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const invoiceSelect = document.getElementById('invoice_id');
            const amountInput = document.getElementById('amount');
            const customerSelect = document.getElementById('customer_id');

            // Store invoice data from PHP for JS lookup
            const invoicesData = @json($invoices->keyBy('id'));

            // Function to update amount and optionally customer when invoice is selected
            invoiceSelect.addEventListener('change', function() {
                const selectedInvoiceId = this.value;
                const selectedInvoice = invoicesData[selectedInvoiceId];

                if (selectedInvoice) {
                    // Set amount to balance_due if an invoice is selected and it has balance
                    if (selectedInvoice.balance_due > 0) {
                        amountInput.value = parseFloat(selectedInvoice.balance_due).toFixed(2);
                    } else {
                        amountInput.value = parseFloat(selectedInvoice.total_amount).toFixed(2); // If balance is 0, suggest total
                    }
                    // Automatically select customer if invoice is selected
                    customerSelect.value = selectedInvoice.customer_id;
                } else {
                    // If "No Specific Invoice" is selected, clear amount
                    if (!amountInput.value) { // Don't clear if user already typed something
                        amountInput.value = '';
                    }
                }
            });

            // Set current date for new payments if the field is empty
            @if(!isset($payment))
                if (!document.getElementById('payment_date').value) {
                    document.getElementById('payment_date').valueAsDate = new Date();
                }
            @endif

            // Trigger change event if an invoice was pre-selected on page load (e.g., from query param)
            if (invoiceSelect.value && !amountInput.value && !customerSelect.value) { // Only trigger if nothing is filled yet
                invoiceSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>