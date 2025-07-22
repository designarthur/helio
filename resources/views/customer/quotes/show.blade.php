<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Quote Details: {{ $quote->id }}</title>
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
            Quote Details: <span id="detailQuoteId">{{ $quote->id }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Quote Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Customer:</strong> <span id="detailQuoteCustomerName">{{ $quote->customer->name ?? 'N/A' }}</span></p>
                    <p><strong>Quote Date:</strong> <span id="detailQuoteDate">{{ $quote->quote_date->format('Y-m-d') }}</span></p>
                    <p><strong>Expiry Date:</strong> <span id="detailExpiryDate">{{ $quote->expiry_date ? $quote->expiry_date->format('Y-m-d') : 'N/A' }}</span></p>
                    <p><strong>Status:</strong>
                        <span id="detailQuoteStatus" class="px-2 py-1 rounded-full text-xs font-bold uppercase text-white
                            @if($quote->status == 'Draft') bg-gray-500
                            @elseif($quote->status == 'Sent') bg-blue-600
                            @elseif($quote->status == 'Accepted') bg-green-600
                            @elseif($quote->status == 'Rejected' || $quote->status == 'Expired') bg-red-600
                            @endif
                        ">{{ $quote->status }}</span>
                    </p>
                    <p class="md:col-span-2"><strong>Total Amount:</strong> $<span id="detailQuoteTotal">{{ number_format($quote->total_amount, 2) }}</span></p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Items Quoted</h4>
                <ul id="detailQuoteItemsList" class="list-disc list-inside ml-4 space-y-1">
                    @forelse($quote->items as $item)
                        <li>
                            <strong>{{ $item['description'] ?? 'Item' }}</strong> - {{ $item['rental_days'] ?? 'N/A' }} days @ ${{ number_format($item['unit_price'] ?? 0, 2) }}/day (Subtotal: ${{ number_format($item['item_total_price'] ?? 0, 2) }})
                        </li>
                    @empty
                        <li>No items quoted.</li>
                    @endforelse
                </ul>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Additional Fees</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Delivery Fee:</strong> $<span id="detailQuoteDeliveryFee">{{ number_format($quote->delivery_fee, 2) }}</span></p>
                    <p><strong>Pickup Fee:</strong> $<span id="detailQuotePickupFee">{{ number_format($quote->pickup_fee, 2) }}</span></p>
                    <p><strong>Damage Waiver:</strong> $<span id="detailQuoteDamageWaiver">{{ number_format($quote->damage_waiver, 2) }}</span></p>
                </div>
            </div>

            @if($quote->notes)
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailQuoteNotesGroup">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Notes/Terms</h4>
                <p><span id="detailQuoteNotes">{{ $quote->notes }}</span></p>
            </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Linked Records</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Linked Booking:</strong>
                        @if($quote->linkedBooking)
                            <a href="{{ route('customer.bookings.show', $quote->linkedBooking->id) }}" class="text-blue-600 hover:underline">#{{ $quote->linkedBooking->id }}</a>
                        @else
                            N/A
                        @endif
                    </p>
                    <p><strong>Linked Invoice:</strong>
                        @if($quote->linkedInvoice)
                            <a href="{{ route('customer.invoices.show', $quote->linkedInvoice->id) }}" class="text-blue-600 hover:underline">#{{ $quote->linkedInvoice->invoice_number }}</a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('customer.quotes.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
            <button onclick="showConceptualAction('Download Quote', 'Downloading quote PDF...')" class="px-6 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">Download PDF</button>
            
            {{-- Conditional Action Buttons for Customer based on Quote Status --}}
            @if(in_array($quote->status, ['Draft', 'Sent']))
                <form action="{{ route('customer.quotes.accept', $quote->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to ACCEPT Quote #{{ $quote->id }}? This will notify the vendor and initiate the booking process.');">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-semibold hover:bg-green-700 transition-colors duration-200">
                        Accept Quote
                    </button>
                </form>
                <form action="{{ route('customer.quotes.reject', $quote->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to REJECT Quote #{{ $quote->id }}?');">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                        Reject
                    </button>
                </form>
                <button onclick="showConceptualAction('Request Revision', 'Requesting revision for Quote #{{ $quote->id }}. The vendor will contact you.')" class="px-6 py-2 border border-blue-600 text-blue-600 rounded-md font-semibold hover:bg-blue-50 transition-colors duration-200">
                    Request Revision
                </button>
            @elseif(in_array($quote->status, ['Expired', 'Rejected']))
                <a href="{{ route('customer.quotes.create') }}" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                    Request New Quote
                </a>
            @endif
        </div>
    </div>

    <script>
        function showConceptualAction(title, message) {
            alert(`${title}: ${message}`);
        }
    </script>
</body>
</html>