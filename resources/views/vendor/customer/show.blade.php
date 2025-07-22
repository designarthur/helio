<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Customer Details: {{ $customer->name }}</title>
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
            Customer Details: <span id="detailCustomerId">{{ $customer->id }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">General Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Name:</strong> <span id="detailCustomerName">{{ $customer->name }}</span></p>
                    <p><strong>Company:</strong> <span id="detailCompanyName">{{ $customer->company ?? 'N/A' }}</span></p>
                    <p><strong>Email:</strong> <span id="detailCustomerEmail">{{ $customer->email }}</span></p>
                    <p><strong>Phone:</strong> <span id="detailCustomerPhone">{{ $customer->phone }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Billing Address:</strong> <span id="detailBillingAddress">{{ $customer->billing_address }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Service Addresses:</strong> <span id="detailServiceAddresses">{{ is_array($customer->service_addresses) ? implode('; ', $customer->service_addresses) : ($customer->service_addresses ?? 'N/A') }}</span></p>
                    <p><strong>Customer Type:</strong> <span id="detailCustomerType">{{ $customer->customer_type }}</span></p>
                    <p><strong>Account Status:</strong> <span id="detailCustomerStatus">{{ $customer->status }}</span></p>
                </div>
            </div>

            @if($customer->internal_notes)
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailInternalNotesGroup">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Internal Notes</h4>
                <p><span id="detailInternalNotes">{{ $customer->internal_notes }}</span></p>
            </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Communication Log (Dummy)</h4>
                <p class="text-gray-600">No recent communication history. (In a real app, this would show calls, emails, etc.)</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Rental & Service History (Dummy)</h4>
                <p class="text-gray-600">No past rentals or services. (In a real app, this would list equipment rentals, junk removals, etc.)</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Financial Overview (Dummy)</h4>
                <p class="text-gray-600">Total Spent: $0.00 | Outstanding Balance: $0.00 (In a real app, this would show financial summaries.)</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('customers.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
            <a href="{{ route('customers.edit', $customer->id) }}" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200" id="editFromDetailBtn">Edit Customer</a>
        </div>
    </div>
</body>
</html>