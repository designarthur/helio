<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Request New Quote</title>
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
            Request New Quote
        </h3>

        <form id="newQuoteRequestForm" method="POST" action="{{ route('customer.quotes.store') }}">
            @csrf {{-- CSRF token for security --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <p class="col-span-1 md:col-span-2 text-gray-700">Hello {{ $customerProfile->name }}! Please provide details for your custom quote request.</p>
                
                {{-- Customer ID (hidden field, pre-filled for logged-in customer) --}}
                <input type="hidden" name="customer_id" value="{{ $customerProfile->id }}">

                <div class="col-span-1 md:col-span-2">
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">Preferred Expiry Date (Optional):</label>
                    <input type="date" id="expiry_date" name="expiry_date"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('expiry_date') }}">
                    <p class="text-xs text-gray-500 mt-1">Suggest a date for this quote to remain valid. The vendor will confirm.</p>
                    @error('expiry_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quote Items:</label>
                    <div id="quoteItemsContainer" class="space-y-3 border p-3 rounded-md bg-gray-50">
                        {{-- Items will be rendered here by JavaScript or default empty --}}
                        <div class="flex flex-wrap items-end gap-2 p-2 border-b border-gray-200 last:border-b-0">
                            <select name="items[][equipment_id]" class="flex-grow min-w-[150px] px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-chili-red equipment-select" onchange="calculatePreliminaryQuoteTotal()" required>
                                <option value="">Select Equipment</option>
                                @forelse($availableEquipment as $item)
                                    <option value="{{ $item->id }}" data-base-daily-rate="{{ $item->base_daily_rate }}">
                                        {{ $item->type }} - {{ $item->size }} (ID: {{ $item->internal_id ?? $item->id }})
                                    </option>
                                @empty
                                    <option value="" disabled>No equipment available for quotes.</option>
                                @endforelse
                            </select>
                            <input type="number" name="items[][rental_days]" placeholder="Days" min="1" class="w-20 px-3 py-2 border border-gray-300 rounded-md days-input" onchange="calculatePreliminaryQuoteTotal()" required>
                            <button type="button" class="text-red-600 hover:text-red-800" onclick="removeQuoteItem(this)"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    <button type="button" id="addQuoteItemBtn" class="mt-3 px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 transition-colors duration-200">Add Item</button>
                    @error('items')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('items.*.equipment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('items.*.rental_days')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">Estimated Delivery Fee ($):</label>
                    <input type="number" id="delivery_fee" name="delivery_fee" min="0" step="0.01" onchange="calculatePreliminaryQuoteTotal()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('delivery_fee', '0.00') }}">
                    @error('delivery_fee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="pickup_fee" class="block text-sm font-medium text-gray-700 mb-1">Estimated Pickup Fee ($):</label>
                    <input type="number" id="pickup_fee" name="pickup_fee" min="0" step="0.01" onchange="calculatePreliminaryQuoteTotal()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('pickup_fee', '0.00') }}">
                    @error('pickup_fee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="damage_waiver" class="block text-sm font-medium text-gray-700 mb-1">Estimated Damage Waiver ($):</label>
                    <input type="number" id="damage_waiver" name="damage_waiver" min="0" step="0.01" onchange="calculatePreliminaryQuoteTotal()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('damage_waiver', '0.00') }}">
                    @error('damage_waiver')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes/Special Requests:</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="e.g., specific delivery requirements, desired features"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('notes') }}
                    </textarea>
                    @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2 text-right">
                    <label class="block text-lg font-bold text-gray-800 mb-1">Estimated Total Quote:</label>
                    <span class="text-3xl font-extrabold text-blue-600" id="preliminaryQuoteTotal">$0.00</span>
                    <p class="text-xs text-gray-500 mt-1">This is an estimate. The vendor will send a final quote for your approval.</p>
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('customer.quotes.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        Submit Quote Request
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quoteItemsContainer = document.getElementById('quoteItemsContainer');
            const addQuoteItemBtn = document.getElementById('addQuoteItemBtn');
            const preliminaryQuoteTotalDisplay = document.getElementById('preliminaryQuoteTotal');

            // Get available equipment data from Blade for JS lookup
            const availableEquipmentData = @json($availableEquipment->keyBy('id')->map(function($eq) {
                return [
                    'base_daily_rate' => $eq->base_daily_rate,
                    'delivery_fee' => $eq->delivery_fee, // Assuming these are unit fees for simplicity
                    'pickup_fee' => $eq->pickup_fee,
                    'damage_waiver_cost' => $eq->damage_waiver_cost,
                    // Add other relevant pricing fields if needed for client-side estimate
                ];
            }));


            // --- Item Management Functions ---
            function createQuoteItemRow(equipmentId = '', rentalDays = '') {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('flex', 'flex-wrap', 'items-end', 'gap-2', 'p-2', 'border-b', 'border-gray-200', 'last:border-b-0');
                itemDiv.innerHTML = `
                    <select name="items[][equipment_id]" class="flex-grow min-w-[150px] px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-chili-red equipment-select" onchange="calculatePreliminaryQuoteTotal()" required>
                        <option value="">Select Equipment</option>
                        @forelse($availableEquipment as $item)
                            <option value="{{ $item->id }}" data-base-daily-rate="{{ $item->base_daily_rate }}">
                                {{ $item->type }} - {{ $item->size }} (ID: {{ $item->internal_id ?? $item->id }})
                            </option>
                        @empty
                            <option value="" disabled>No equipment available for quotes.</option>
                        @endforelse
                    </select>
                    <input type="number" name="items[][rental_days]" placeholder="Days" min="1" class="w-20 px-3 py-2 border border-gray-300 rounded-md days-input" onchange="calculatePreliminaryQuoteTotal()" required>
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="removeQuoteItem(this)"><i class="fas fa-trash-alt"></i></button>
                `;
                
                quoteItemsContainer.appendChild(itemDiv);

                // Set values if provided (for edit mode, though this is a create-only form now)
                if (equipmentId) {
                    itemDiv.querySelector('.equipment-select').value = equipmentId;
                }
                if (rentalDays) {
                    itemDiv.querySelector('.days-input').value = rentalDays;
                }
            }

            window.removeQuoteItem = function(button) { // Made global for onclick attribute
                if (quoteItemsContainer.children.length > 1) { // Don't remove if it's the last item
                    button.closest('div').remove();
                    calculatePreliminaryQuoteTotal();
                } else {
                    alert('A quote request must have at least one item.');
                }
            };

            // --- Preliminary Price Calculation Logic ---
            window.calculatePreliminaryQuoteTotal = function() { // Made global for onchange attribute
                let total = 0;
                quoteItemsContainer.querySelectorAll('.flex.flex-wrap').forEach(itemDiv => {
                    const equipmentId = itemDiv.querySelector('.equipment-select').value;
                    const rentalDays = parseInt(itemDiv.querySelector('.days-input').value) || 0;
                    
                    const equipmentRate = availableEquipmentData[equipmentId] ? parseFloat(availableEquipmentData[equipmentId].base_daily_rate) : 0;
                    
                    if (equipmentRate > 0 && rentalDays > 0) {
                        total += equipmentRate * rentalDays;
                    }
                });

                const deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
                const pickupFee = parseFloat(document.getElementById('pickup_fee').value) || 0;
                const damageWaiver = parseFloat(document.getElementById('damage_waiver').value) || 0;

                total += deliveryFee + pickupFee + damageWaiver;

                preliminaryQuoteTotalDisplay.textContent = `$${total.toFixed(2)}`;
            };

            // --- Event Listeners ---
            addQuoteItemBtn.addEventListener('click', () => createQuoteItemRow());

            // Initial setup for new quote
            if (quoteItemsContainer.children.length === 0) {
                createQuoteItemRow(); // Ensure at least one empty row for new quotes
            }
            calculatePreliminaryQuoteTotal(); // Initial calculation to display total
        });
    </script>
</body>
</html>