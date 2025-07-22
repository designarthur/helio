<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($customer)) Edit Customer: {{ $customer->name }} @else Add New Customer @endif</title>
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
    <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-3xl relative max-h-[90vh] overflow-y-auto">
        <button onclick="window.history.back()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl font-bold">&times;</button>
        <h3 class="text-2xl font-bold text-chili-red mb-6 border-b pb-3 border-gray-200">
            @if(isset($customer)) Edit Customer: {{ $customer->name }} @else Add New Customer @endif
        </h3>

        <form id="customerForm" method="POST" action="@if(isset($customer)) {{ route('customers.update', $customer->id) }} @else {{ route('customers.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($customer)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="customerId" name="id" value="{{ $customer->id ?? '' }}">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name:</label>
                    <input type="text" id="name" name="name" placeholder="John Doe" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('name', $customer->name ?? '') }}">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company Name (Optional):</label>
                    <input type="text" id="company" name="company" placeholder="ABC Construction"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('company', $customer->company ?? '') }}">
                    @error('company')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                    <input type="email" id="email" name="email" placeholder="john.doe@example.com" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('email', $customer->email ?? '') }}">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone:</label>
                    <input type="tel" id="phone" name="phone" placeholder="(123) 456-7890" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('phone', $customer->phone ?? '') }}">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-1">Billing Address:</label>
                    <input type="text" id="billing_address" name="billing_address" placeholder="123 Main St, Anytown, USA 12345" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('billing_address', $customer->billing_address ?? '') }}">
                    @error('billing_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="service_addresses" class="block text-sm font-medium text-gray-700 mb-1">Service Addresses (comma-separated):</label>
                    <textarea id="service_addresses" name="service_addresses" rows="2" placeholder="456 Oak Ave, Anytown; 789 Pine Ln, Otherville"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('service_addresses', is_array($customer->service_addresses ?? null) ? implode('; ', $customer->service_addresses) : ($customer->service_addresses ?? '')) }}
                    </textarea>
                    @error('service_addresses')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-1">Customer Type:</label>
                    <select id="customer_type" name="customer_type" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Type</option>
                        <option value="Residential" {{ (old('customer_type', $customer->customer_type ?? '') == 'Residential') ? 'selected' : '' }}>Residential</option>
                        <option value="Commercial" {{ (old('customer_type', $customer->customer_type ?? '') == 'Commercial') ? 'selected' : '' }}>Commercial</option>
                    </select>
                    @error('customer_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Account Status:</label>
                    <select id="status" name="status" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="Active" {{ (old('status', $customer->status ?? '') == 'Active') ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ (old('status', $customer->status ?? '') == 'Inactive') ? 'selected' : '' }}>Inactive</option>
                        <option value="On Hold" {{ (old('status', $customer->status ?? '') == 'On Hold') ? 'selected' : '' }}>On Hold</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="internal_notes" class="block text-sm font-medium text-gray-700 mb-1">Internal Notes:</label>
                    <textarea id="internal_notes" name="internal_notes" rows="3" placeholder="Customer prefers calls after 3 PM."
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('internal_notes', $customer->internal_notes ?? '') }}
                    </textarea>
                    @error('internal_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('customers.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        @if(isset($customer)) Save Changes @else Add Customer @endif
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>