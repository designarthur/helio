<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Request New Booking</title>
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
            Request New Booking
        </h3>

        <form id="newBookingRequestForm" method="POST" action="{{ route('customer.bookings.store') }}">
            @csrf {{-- CSRF token for security --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <p class="col-span-1 md:col-span-2 text-gray-700">Hello {{ $customerProfile->name }}! Please fill out the details for your rental request.</p>

                <div class="col-span-1 md:col-span-2">
                    <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-1">Select Equipment:</label>
                    <select id="equipment_id" name="equipment_id" required onchange="handleEquipmentSelection()"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Equipment Type</option>
                        @forelse($availableEquipment as $item)
                            <option value="{{ $item->id }}" data-type="{{ $item->type }}"
                                data-base-daily-rate="{{ $item->base_daily_rate }}"
                                data-delivery-fee="{{ $item->delivery_fee }}"
                                data-pickup-fee="{{ $item->pickup_fee }}"
                                data-damage-waiver-cost="{{ $item->damage_waiver_cost }}"
                                data-max-tonnage="{{ $item->max_tonnage }}"
                                data-overage-per-ton-fee="{{ $item->overage_per_ton_fee }}"
                                data-disposal-rate-per-ton="{{ $item->disposal_rate_per_ton }}"
                                >
                                {{ $item->type }} - {{ $item->size }} (ID: {{ $item->internal_id ?? $item->id }})
                            </option>
                        @empty
                            <option value="" disabled>No equipment available at this time.</option>
                        @endforelse
                    </select>
                    @error('equipment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="rental_start_date" class="block text-sm font-medium text-gray-700 mb-1">Rental Start Date:</label>
                    <input type="date" id="rental_start_date" name="rental_start_date" required onchange="calculatePreliminaryPrice()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('rental_start_date') }}">
                    @error('rental_start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="rental_end_date" class="block text-sm font-medium text-gray-700 mb-1">Rental End Date:</label>
                    <input type="date" id="rental_end_date" name="rental_end_date" required onchange="calculatePreliminaryPrice()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('rental_end_date') }}">
                    @error('rental_end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Delivery Address:</label>
                    <input type="text" id="delivery_address" name="delivery_address" placeholder="e.g., {{ $customerProfile->billing_address }}" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('delivery_address', $customerProfile->billing_address) }}">
                    <p class="text-xs text-gray-500 mt-1">This will be the primary location for delivery. You can update your saved addresses in your profile.</p>
                    @error('delivery_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="pickup_address" class="block text-sm font-medium text-gray-700 mb-1">Pickup Address (Optional, if different):</label>
                    <input type="text" id="pickup_address" name="pickup_address" placeholder="Leave blank if same as delivery"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('pickup_address') }}">
                    @error('pickup_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Dumpster Specific Request Fields --}}
                <div id="dumpsterRequestFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Dumpster Specific Request Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="estimated_tonnage" class="block text-sm font-medium text-gray-700 mb-1">Estimated Tonnage:</label>
                            <input type="number" id="estimated_tonnage" name="estimated_tonnage" min="0" step="0.1" onchange="calculatePreliminaryPrice()"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('estimated_tonnage') }}">
                            <p class="text-xs text-gray-500 mt-1">Provide your best estimate. Final weight will be confirmed at pickup.</p>
                            @error('estimated_tonnage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="prohibited_materials_ack" class="block text-sm font-medium text-gray-700 mb-1">Prohibited Materials Acknowledgment:</label>
                            <textarea id="prohibited_materials_ack" name="prohibited_materials_ack" rows="2" placeholder="e.g., I understand that tires, batteries, and hazardous waste are not allowed."
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('prohibited_materials_ack') }}
                            </textarea>
                            @error('prohibited_materials_ack')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Temporary Toilet Specific Request Fields --}}
                <div id="toiletRequestFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Temporary Toilet Specific Request Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="requested_service_freq" class="block text-sm font-medium text-gray-700 mb-1">Requested Service Frequency:</label>
                            <select id="requested_service_freq" name="requested_service_freq" onchange="calculatePreliminaryPrice()"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="Weekly" {{ (old('requested_service_freq') == 'Weekly') ? 'selected' : '' }}>Weekly</option>
                                <option value="Bi-weekly" {{ (old('requested_service_freq') == 'Bi-weekly') ? 'selected' : '' }}>Bi-weekly</option>
                                <option value="Event-specific" {{ (old('requested_service_freq') == 'Event-specific') ? 'selected' : '' }}>Event-specific</option>
                            </select>
                            @error('requested_service_freq')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="toilet_special_requests" class="block text-sm font-medium text-gray-700 mb-1">Special Requests:</label>
                            <textarea id="toilet_special_requests" name="toilet_special_requests" rows="2" placeholder="e.g., specific placement, extra supplies"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('toilet_special_requests') }}
                            </textarea>
                            @error('toilet_special_requests')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Storage Container Specific Request Fields --}}
                <div id="containerRequestFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Storage Container Specific Request Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-1 md:col-span-2">
                            <label for="container_placement_notes" class="block text-sm font-medium text-gray-700 mb-1">Placement Notes:</label>
                            <textarea id="container_placement_notes" name="container_placement_notes" rows="2" placeholder="e.g., place on concrete pad, clear path needed"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('container_placement_notes') }}
                            </textarea>
                            @error('container_placement_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="container_security_access" class="block text-sm font-medium text-gray-700 mb-1">Security/Access Instructions:</label>
                            <textarea id="container_security_access" name="container_security_access" rows="2" placeholder="e.g., gate code 1234, call prior to arrival"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('container_security_access') }}
                            </textarea>
                            @error('container_security_access')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="booking_notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional):</label>
                    <textarea id="booking_notes" name="booking_notes" rows="3" placeholder="Any special requests or instructions for your rental."
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('booking_notes') }}
                    </textarea>
                    @error('booking_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2 text-right">
                    <label class="block text-lg font-bold text-gray-800 mb-1">Estimated Preliminary Price:</label>
                    <span class="text-2xl font-extrabold text-blue-600" id="preliminaryPriceDisplay">$0.00</span>
                    <p class="text-xs text-gray-500 mt-1">This is an estimate. Final pricing will be confirmed by the vendor.</p>
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('customer.bookings.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipmentIdSelect = document.getElementById('equipment_id');
            const rentalStartDateInput = document.getElementById('rental_start_date');
            const rentalEndDateInput = document.getElementById('rental_end_date');
            const preliminaryPriceDisplay = document.getElementById('preliminaryPriceDisplay');

            // Type-specific request fields
            const dumpsterRequestFields = document.getElementById('dumpsterRequestFields');
            const estimatedTonnageInput = document.getElementById('estimated_tonnage');
            const prohibitedMaterialsAckInput = document.getElementById('prohibited_materials_ack');

            const toiletRequestFields = document.getElementById('toiletRequestFields');
            const requestedServiceFreqSelect = document.getElementById('requested_service_freq');
            const toiletSpecialRequestsInput = document.getElementById('toilet_special_requests');

            const containerRequestFields = document.getElementById('containerRequestFields');
            const containerPlacementNotesInput = document.getElementById('container_placement_notes');
            const containerSecurityAccessInput = document.getElementById('container_security_access');

            // Get equipment data from the Blade loop (populated in the select options' data-attributes)
            // We convert this to a JS object for easier lookup by ID
            const availableEquipmentData = {};
            Array.from(equipmentIdSelect.options).forEach(option => {
                if (option.value) {
                    availableEquipmentData[option.value] = {
                        type: option.dataset.type,
                        base_daily_rate: parseFloat(option.dataset.baseDailyRate),
                        delivery_fee: parseFloat(option.dataset.deliveryFee),
                        pickup_fee: parseFloat(option.dataset.pickupFee),
                        damage_waiver_cost: parseFloat(option.dataset.damageWaiverCost),
                        max_tonnage: parseFloat(option.dataset.maxTonnage),
                        overage_per_ton_fee: parseFloat(option.dataset.overagePerTonFee),
                        disposal_rate_per_ton: parseFloat(option.dataset.disposalRatePerTon),
                        // Include other relevant pricing fields if needed for client-side estimate
                    };
                }
            });


            // Function to hide all type-specific fields and clear their values
            function hideAllTypeSpecificRequestFields() {
                dumpsterRequestFields.classList.add('hidden');
                toiletRequestFields.classList.add('hidden');
                containerRequestFields.classList.add('hidden');

                // Clear values
                estimatedTonnageInput.value = '';
                prohibitedMaterialsAckInput.value = '';
                requestedServiceFreqSelect.value = 'Weekly'; // Default value
                toiletSpecialRequestsInput.value = '';
                containerPlacementNotesInput.value = '';
                containerSecurityAccessInput.value = '';
            }

            // Function to show/hide relevant fields based on selected type and recalculate price
            window.handleEquipmentSelection = function() { // Make global for onchange attribute
                hideAllTypeSpecificRequestFields(); // Always hide all first

                const selectedEquipmentId = equipmentIdSelect.value;
                const selectedEquipment = availableEquipmentData[selectedEquipmentId];

                if (selectedEquipment) {
                    if (selectedEquipment.type === 'Dumpster') {
                        dumpsterRequestFields.classList.remove('hidden');
                    } else if (selectedEquipment.type === 'Temporary Toilet') {
                        toiletRequestFields.classList.remove('hidden');
                    } else if (selectedEquipment.type === 'Storage Container') {
                        containerRequestFields.classList.remove('hidden');
                    }
                }
                calculatePreliminaryPrice(); // Recalculate price whenever equipment selection changes
            };

            // Function to calculate preliminary price
            window.calculatePreliminaryPrice = function() { // Make global for onchange attribute
                const equipmentId = equipmentIdSelect.value;
                const startDate = rentalStartDateInput.value;
                const endDate = rentalEndDateInput.value;
                
                let calculatedPrice = 0;

                const selectedEquipment = availableEquipmentData[equipmentId];

                if (selectedEquipment && startDate && endDate) {
                    const start = new Date(startDate);
                    const end = new Date(endDate);
                    // Ensure end date is not before start date
                    if (start > end) {
                        preliminaryPriceDisplay.textContent = '$0.00';
                        return;
                    }
                    const timeDiff = end.getTime() - start.getTime();
                    const dayDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // +1 to include both start and end day

                    if (dayDiff > 0) {
                        calculatedPrice = selectedEquipment.base_daily_rate * dayDiff;

                        // Add flat fees (delivery, pickup, damage waiver)
                        calculatedPrice += (selectedEquipment.delivery_fee || 0);
                        calculatedPrice += (selectedEquipment.pickup_fee || 0);
                        calculatedPrice += (selectedEquipment.damage_waiver_cost || 0);

                        // Add type-specific pricing
                        if (selectedEquipment.type === 'Dumpster') {
                            const estimatedTonnage = parseFloat(estimatedTonnageInput.value) || 0;
                            // If estimated tonnage exceeds maxTonnage, add overage
                            if (selectedEquipment.max_tonnage && estimatedTonnage > selectedEquipment.max_tonnage) {
                                calculatedPrice += (estimatedTonnage - selectedEquipment.max_tonnage) * (selectedEquipment.overage_per_ton_fee || 0);
                            }
                            // Add disposal rate for all estimated tonnage (assuming this is part of customer's preliminary cost)
                            calculatedPrice += (estimatedTonnage * (selectedEquipment.disposal_rate_per_ton || 0));
                        } else if (selectedEquipment.type === 'Temporary Toilet') {
                            const serviceFreq = requestedServiceFreqSelect.value;
                            let numServices = 0;
                            if (serviceFreq === 'Weekly') {
                                numServices = Math.floor(dayDiff / 7);
                            } else if (serviceFreq === 'Bi-weekly') {
                                numServices = Math.floor(dayDiff / 14);
                            } else if (serviceFreq === 'Event-specific' && dayDiff < 7) { // Assume 1 service for short events
                                numServices = 1;
                            }
                            calculatedPrice += numServices * 20; // Dummy cost per service for toilet servicing
                        }
                    }
                }
                preliminaryPriceDisplay.textContent = `$${calculatedPrice.toFixed(2)}`;
            };

            // Call on page load to set initial state and calculate initial price (if old values exist)
            handleEquipmentSelection(); // This also triggers initial price calculation
        });
    </script>
</body>
</html>