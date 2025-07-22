<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($quote)) Edit Quote: {{ $quote->id }} @else Create New Quote @endif</title>
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
            @if(isset($quote)) Edit Quote: {{ $quote->id }} @else Create New Quote @endif
        </h3>

        <form id="quoteForm" method="POST" action="@if(isset($quote)) {{ route('quotes.update', $quote->id) }} @else {{ route('quotes.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($quote)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="quoteId" name="id" value="{{ $quote->id ?? '' }}">

                <div class="col-span-1 md:col-span-2">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer:</label>
                    <select id="customer_id" name="customer_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (old('customer_id', $quote->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->company ?? 'Residential' }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (Optional):</label>
                    <input type="date" id="expiry_date" name="expiry_date"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('expiry_date', $quote->expiry_date ? $quote->expiry_date->format('Y-m-d') : '') }}">
                    @error('expiry_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quote Items:</label>
                    <div id="quoteItemsContainer" class="space-y-3 border p-3 rounded-md bg-gray-50">
                        {{-- Items will be rendered here by JavaScript --}}
                        @if(isset($quote) && count($quote->items) > 0)
                            @foreach($quote->items as $item)
                                <div class="flex flex-wrap items-end gap-2 p-2 border-b border-gray-200 last:border-b-0">
                                    <select name="items[][equipment_id]" class="flex-grow min-w-[150px] px-3 py-2 border border-gray-300 rounded-md focus:outline-none equipment-select" onchange="calculateQuoteTotal()">
                                        <option value="">Select Equipment</option>
                                        @foreach($equipment as $eq)
                                            <option value="{{ $eq->id }}" data-base-daily-rate="{{ $eq->base_daily_rate }}"
                                                {{ (old('items.'.$loop->parent->index.'.equipment_id', $item['equipment_id']) == $eq->id) ? 'selected' : '' }}>
                                                {{ $eq->type }} - {{ $eq->size }} (ID: {{ $eq->internal_id ?? $eq->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="items[][rental_days]" placeholder="Days" min="1" class="w-20 px-3 py-2 border border-gray-300 rounded-md days-input" onchange="calculateQuoteTotal()" value="{{ old('items.'.$loop->parent->index.'.rental_days', $item['rental_days']) }}">
                                    <button type="button" class="text-red-600 hover:text-red-800" onclick="removeQuoteItem(this)"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            @endforeach
                        @else
                            {{-- Default empty item for new quotes --}}
                            <div class="flex flex-wrap items-end gap-2 p-2 border-b border-gray-200 last:border-b-0">
                                <select name="items[][equipment_id]" class="flex-grow min-w-[150px] px-3 py-2 border border-gray-300 rounded-md focus:outline-none equipment-select" onchange="calculateQuoteTotal()">
                                    <option value="">Select Equipment</option>
                                    @foreach($equipment as $eq)
                                        <option value="{{ $eq->id }}" data-base-daily-rate="{{ $eq->base_daily_rate }}">
                                            {{ $eq->type }} - {{ $eq->size }} (ID: {{ $eq->internal_id ?? $eq->id }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="number" name="items[][rental_days]" placeholder="Days" min="1" class="w-20 px-3 py-2 border border-gray-300 rounded-md days-input" onchange="calculateQuoteTotal()">
                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeQuoteItem(this)"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="addQuoteItemBtn" class="mt-3 px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 transition-colors duration-200">Add Item</button>
                    @error('items')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('items.*.equipment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('items.*.rental_days')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">Delivery Fee ($):</label>
                    <input type="number" id="delivery_fee" name="delivery_fee" min="0" step="0.01" onchange="calculateQuoteTotal()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('delivery_fee', $quote->delivery_fee ?? '0.00') }}">
                    @error('delivery_fee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="pickup_fee" class="block text-sm font-medium text-gray-700 mb-1">Pickup Fee ($):</label>
                    <input type="number" id="pickup_fee" name="pickup_fee" min="0" step="0.01" onchange="calculateQuoteTotal()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('pickup_fee', $quote->pickup_fee ?? '0.00') }}">
                    @error('pickup_fee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="damage_waiver" class="block text-sm font-medium text-gray-700 mb-1">Damage Waiver ($):</label>
                    <input type="number" id="damage_waiver" name="damage_waiver" min="0" step="0.01" onchange="calculateQuoteTotal()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('damage_waiver', $quote->damage_waiver ?? '0.00') }}">
                    @error('damage_waiver')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes/Terms:</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('notes', $quote->notes ?? '') }}
                    </textarea>
                    @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                    <select id="status" name="status" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="Draft" {{ (old('status', $quote->status ?? '') == 'Draft') ? 'selected' : '' }}>Draft</option>
                        <option value="Sent" {{ (old('status', $quote->status ?? '') == 'Sent') ? 'selected' : '' }}>Sent</option>
                        <option value="Accepted" {{ (old('status', $quote->status ?? '') == 'Accepted') ? 'selected' : '' }}>Accepted</option>
                        <option value="Rejected" {{ (old('status', $quote->status ?? '') == 'Rejected') ? 'selected' : '' }}>Rejected</option>
                        <option value="Expired" {{ (old('status', $quote->status ?? '') == 'Expired') ? 'selected' : '' }}>Expired</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2 text-right">
                    <label class="block text-xl font-bold text-gray-800 mb-1">Total Quote Amount:</label>
                    <span class="text-3xl font-extrabold text-chili-red" id="quoteTotal">$0.00</span>
                    <input type="hidden" name="total_amount" id="totalAmountHidden" value="{{ old('total_amount', $quote->total_amount ?? '0.00') }}">
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('quotes.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        @if(isset($quote)) Save Changes @else Create Quote @endif
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quoteItemsContainer = document.getElementById('quoteItemsContainer');
            const addQuoteItemBtn = document.getElementById('addQuoteItemBtn');
            const equipmentData = @json($equipment->keyBy('id')->map(function($eq) {
                return ['base_daily_rate' => $eq->base_daily_rate];
            })); // Pass only necessary data
            const quoteTotalDisplay = document.getElementById('quoteTotal');
            const totalAmountHidden = document.getElementById('totalAmountHidden');

            // --- Item Management Functions ---
            function createQuoteItemRow(equipmentId = '', rentalDays = '') {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('flex', 'flex-wrap', 'items-end', 'gap-2', 'p-2', 'border-b', 'border-gray-200', 'last:border-b-0');
                itemDiv.innerHTML = `
                    <select name="items[][equipment_id]" class="flex-grow min-w-[150px] px-3 py-2 border border-gray-300 rounded-md focus:outline-none equipment-select" onchange="calculateQuoteTotal()">
                        <option value="">Select Equipment</option>
                        @foreach($equipment as $eq)
                            <option value="{{ $eq->id }}" data-base-daily-rate="{{ $eq->base_daily_rate }}">
                                {{ $eq->type }} - {{ $eq->size }} (ID: {{ $eq->internal_id ?? $eq->id }})
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="items[][rental_days]" placeholder="Days" min="1" class="w-20 px-3 py-2 border border-gray-300 rounded-md days-input" onchange="calculateQuoteTotal()">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="removeQuoteItem(this)"><i class="fas fa-trash-alt"></i></button>
                `;
                
                quoteItemsContainer.appendChild(itemDiv);

                // Set values if provided (for edit mode)
                if (equipmentId) {
                    itemDiv.querySelector('.equipment-select').value = equipmentId;
                }
                if (rentalDays) {
                    itemDiv.querySelector('.days-input').value = rentalDays;
                }
            }

            function removeQuoteItem(button) {
                if (quoteItemsContainer.children.length > 1) { // Don't remove if it's the last item
                    button.closest('div').remove();
                    calculateQuoteTotal();
                } else {
                    alert('A quote must have at least one item.');
                }
            }

            // --- Price Calculation Logic ---
            window.calculateQuoteTotal = function() { // Made global for onchange attribute
                let total = 0;
                quoteItemsContainer.querySelectorAll('.flex.flex-wrap').forEach(itemDiv => {
                    const equipmentId = itemDiv.querySelector('.equipment-select').value;
                    const rentalDays = parseInt(itemDiv.querySelector('.days-input').value) || 0;
                    
                    const equipmentRate = equipmentData[equipmentId] ? parseFloat(equipmentData[equipmentId].base_daily_rate) : 0;
                    
                    if (equipmentRate > 0 && rentalDays > 0) {
                        total += equipmentRate * rentalDays;
                    }
                });

                const deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
                const pickupFee = parseFloat(document.getElementById('pickup_fee').value) || 0;
                const damageWaiver = parseFloat(document.getElementById('damage_waiver').value) || 0;

                total += deliveryFee + pickupFee + damageWaiver;

                quoteTotalDisplay.textContent = `$${total.toFixed(2)}`;
                totalAmountHidden.value = total.toFixed(2); // Update hidden input for submission
            };

            // --- Event Listeners ---
            addQuoteItemBtn.addEventListener('click', () => createQuoteItemRow());

            // Initial render for edit mode or empty form
            @if(isset($quote) && count($quote->items) > 0)
                // Clear the default empty row added by Blade if items exist
                quoteItemsContainer.innerHTML = '';
                @foreach($quote->items as $item)
                    createQuoteItemRow('{{ $item['equipment_id'] }}', '{{ $item['rental_days'] }}');
                @endforeach
            @else
                // Ensure there's at least one empty row for new quotes
                if (quoteItemsContainer.children.length === 0) {
                    createQuoteItemRow();
                }
            @endif

            // Initial calculation to display total if values are pre-filled (edit mode or old input)
            calculateQuoteTotal();
        });
    </script>
</body>
</html>