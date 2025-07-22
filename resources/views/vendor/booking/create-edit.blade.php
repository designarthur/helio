<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($booking)) Edit Booking: {{ $booking->id }} @else Create New Booking @endif</title>
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
            @if(isset($booking)) Edit Booking: {{ $booking->id }} @else Create New Booking @endif
        </h3>

        <form id="bookingForm" method="POST" action="@if(isset($booking)) {{ route('bookings.update', $booking->id) }} @else {{ route('bookings.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($booking)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="bookingId" name="id" value="{{ $booking->id ?? '' }}">

                <div class="col-span-1 md:col-span-2">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer:</label>
                    <select id="customer_id" name="customer_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (old('customer_id', $booking->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->company ?? 'Residential' }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-1">Equipment:</label>
                    <select id="equipment_id" name="equipment_id" required onchange="handleEquipmentSelection()"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Equipment</option>
                        @foreach($equipment as $item)
                            <option value="{{ $item->id }}" data-type="{{ $item->type }}"
                                {{ (old('equipment_id', $booking->equipment_id ?? '') == $item->id) ? 'selected' : '' }}>
                                {{ $item->type }} - {{ $item->size }} (ID: {{ $item->internal_id ?? $item->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('equipment_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="rental_start_date" class="block text-sm font-medium text-gray-700 mb-1">Rental Start Date:</label>
                    <input type="date" id="rental_start_date" name="rental_start_date" required onchange="calculatePrice()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('rental_start_date', $booking->rental_start_date ? $booking->rental_start_date->format('Y-m-d') : '') }}">
                    @error('rental_start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="rental_end_date" class="block text-sm font-medium text-gray-700 mb-1">Rental End Date:</label>
                    <input type="date" id="rental_end_date" name="rental_end_date" required onchange="calculatePrice()"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('rental_end_date', $booking->rental_end_date ? $booking->rental_end_date->format('Y-m-d') : '') }}">
                    @error('rental_end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-1">Delivery Address:</label>
                    <input type="text" id="delivery_address" name="delivery_address" placeholder="123 Main St, Anytown" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('delivery_address', $booking->delivery_address ?? '') }}">
                    @error('delivery_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="pickup_address" class="block text-sm font-medium text-gray-700 mb-1">Pickup Address (if different):</label>
                    <input type="text" id="pickup_address" name="pickup_address" placeholder="Same as delivery if left blank"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('pickup_address', $booking->pickup_address ?? '') }}">
                    @error('pickup_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Dumpster Specific Booking Fields --}}
                <div id="dumpsterBookingFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Dumpster Specific Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="estimated_tonnage" class="block text-sm font-medium text-gray-700 mb-1">Estimated Tonnage:</label>
                            <input type="number" id="estimated_tonnage" name="estimated_tonnage" min="0" step="0.1" onchange="calculatePrice()"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('estimated_tonnage', $booking->estimated_tonnage ?? '') }}">
                            @error('estimated_tonnage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="prohibited_materials_ack" class="block text-sm font-medium text-gray-700 mb-1">Prohibited Materials Acknowledgment:</label>
                            <textarea id="prohibited_materials_ack" name="prohibited_materials_ack" rows="2" placeholder="Acknowledged prohibited items: Tires, Batteries..."
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('prohibited_materials_ack', $booking->prohibited_materials_ack ?? '') }}
                            </textarea>
                            @error('prohibited_materials_ack')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Temporary Toilet Specific Booking Fields --}}
                <div id="toiletBookingFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Temporary Toilet Specific Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="requested_service_freq" class="block text-sm font-medium text-gray-700 mb-1">Requested Service Frequency:</label>
                            <select id="requested_service_freq" name="requested_service_freq" onchange="calculatePrice()"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="Weekly" {{ (old('requested_service_freq', $booking->requested_service_freq ?? '') == 'Weekly') ? 'selected' : '' }}>Weekly</option>
                                <option value="Bi-weekly" {{ (old('requested_service_freq', $booking->requested_service_freq ?? '') == 'Bi-weekly') ? 'selected' : '' }}>Bi-weekly</option>
                                <option value="Event-specific" {{ (old('requested_service_freq', $booking->requested_service_freq ?? '') == 'Event-specific') ? 'selected' : '' }}>Event-specific</option>
                            </select>
                            @error('requested_service_freq')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="toilet_special_requests" class="block text-sm font-medium text-gray-700 mb-1">Special Requests:</label>
                            <textarea id="toilet_special_requests" name="toilet_special_requests" rows="2" placeholder="e.g., specific placement, extra supplies"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('toilet_special_requests', $booking->toilet_special_requests ?? '') }}
                            </textarea>
                            @error('toilet_special_requests')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Storage Container Specific Booking Fields --}}
                <div id="containerBookingFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Storage Container Specific Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-1 md:col-span-2">
                            <label for="container_placement_notes" class="block text-sm font-medium text-gray-700 mb-1">Placement Notes:</label>
                            <textarea id="container_placement_notes" name="container_placement_notes" rows="2" placeholder="e.g., place on concrete pad, clear path needed"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('container_placement_notes', $booking->container_placement_notes ?? '') }}
                            </textarea>
                            @error('container_placement_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="container_security_access" class="block text-sm font-medium text-gray-700 mb-1">Security/Access Instructions:</label>
                            <textarea id="container_security_access" name="container_security_access" rows="2" placeholder="e.g., gate code 1234, call prior to arrival"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('container_security_access', $booking->container_security_access ?? '') }}
                            </textarea>
                            @error('container_security_access')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="driver_id" class="block text-sm font-medium text-gray-700 mb-1">Assigned Driver (Optional):</label>
                    <select id="driver_id" name="driver_id"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Unassigned</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ (old('driver_id', $booking->driver_id ?? '') == $driver->id) ? 'selected' : '' }}>
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('driver_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                    <select id="status" name="status" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="Pending" {{ (old('status', $booking->status ?? '') == 'Pending') ? 'selected' : '' }}>Pending</option>
                        <option value="Confirmed" {{ (old('status', $booking->status ?? '') == 'Confirmed') ? 'selected' : '' }}>Confirmed</option>
                        <option value="Delivered" {{ (old('status', $booking->status ?? '') == 'Delivered') ? 'selected' : '' }}>Delivered</option>
                        <option value="Completed" {{ (old('status', $booking->status ?? '') == 'Completed') ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ (old('status', $booking->status ?? '') == 'Cancelled') ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Total Price ($):</label>
                    <input type="number" id="total_price" name="total_price" min="0" step="0.01" placeholder="Calculated automatically" readonly
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700"
                           value="{{ old('total_price', $booking->total_price ?? '') }}">
                    @error('total_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="booking_notes" class="block text-sm font-medium text-gray-700 mb-1">Booking Notes (General):</label>
                    <textarea id="booking_notes" name="booking_notes" rows="3" placeholder="Any general instructions for this booking."
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('booking_notes', $booking->booking_notes ?? '') }}
                    </textarea>
                    @error('booking_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('bookings.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        @if(isset($booking)) Save Changes @else Add Booking @endif
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
            const totalPriceInput = document.getElementById('total_price');

            // Type-specific booking fields
            const dumpsterBookingFields = document.getElementById('dumpsterBookingFields');
            const estimatedTonnageInput = document.getElementById('estimated_tonnage');
            const prohibitedMaterialsAckInput = document.getElementById('prohibited_materials_ack');

            const toiletBookingFields = document.getElementById('toiletBookingFields');
            const requestedServiceFreqSelect = document.getElementById('requested_service_freq');
            const toiletSpecialRequestsInput = document.getElementById('toilet_special_requests');

            const containerBookingFields = document.getElementById('containerBookingFields');
            const containerPlacementNotesInput = document.getElementById('container_placement_notes');
            const containerSecurityAccessInput = document.getElementById('container_security_access');

            const equipmentData = @json($equipment->keyBy('id')); // Convert equipment collection to JS object for easy lookup

            // Function to hide all type-specific fields and clear their values
            function hideAllTypeSpecificBookingFields() {
                dumpsterBookingFields.classList.add('hidden');
                toiletBookingFields.classList.add('hidden');
                containerBookingFields.classList.add('hidden');

                // Clear values
                estimatedTonnageInput.value = '';
                prohibitedMaterialsAckInput.value = '';
                requestedServiceFreqSelect.value = 'Weekly'; // Default value
                toiletSpecialRequestsInput.value = '';
                containerPlacementNotesInput.value = '';
                containerSecurityAccessInput.value = '';
            }

            // Function to show/hide relevant fields based on selected type
            window.handleEquipmentSelection = function() { // Made global for onchange attribute
                hideAllTypeSpecificBookingFields(); // Always hide all first

                const selectedEquipmentId = equipmentIdSelect.value;
                const selectedEquipment = equipmentData[selectedEquipmentId]; // Look up using the JS object

                if (selectedEquipment) {
                    if (selectedEquipment.type === 'Dumpster') {
                        dumpsterBookingFields.classList.remove('hidden');
                    } else if (selectedEquipment.type === 'Temporary Toilet') {
                        toiletBookingFields.classList.remove('hidden');
                    } else if (selectedEquipment.type === 'Storage Container') {
                        containerBookingFields.classList.remove('hidden');
                    }
                }
                calculatePrice(); // Recalculate price whenever equipment selection changes
            };

            // Function to calculate price via AJAX
            window.calculatePrice = function() { // Made global for onchange attribute
                const equipmentId = equipmentIdSelect.value;
                const startDate = rentalStartDateInput.value;
                const endDate = rentalEndDateInput.value;

                // Only proceed if all required inputs are present
                if (!equipmentId || !startDate || !endDate) {
                    totalPriceInput.value = '0.00';
                    return;
                }

                // Gather type-specific data for calculation
                const selectedEquipment = equipmentData[equipmentId];
                let params = {
                    equipment_id: equipmentId,
                    start_date: startDate,
                    end_date: endDate,
                };

                if (selectedEquipment) {
                    if (selectedEquipment.type === 'Dumpster') {
                        params.estimated_tonnage = estimatedTonnageInput.value;
                    } else if (selectedEquipment.type === 'Temporary Toilet') {
                        params.service_frequency = requestedServiceFreqSelect.value;
                    }
                }
                
                // Make AJAX request to Laravel backend
                fetch('{{ route('api.bookings.calculatePrice') }}?' + new URLSearchParams(params))
                    .then(response => response.json())
                    .then(data => {
                        if (data.totalPrice !== undefined) {
                            totalPriceInput.value = parseFloat(data.totalPrice).toFixed(2);
                        } else {
                            totalPriceInput.value = '0.00';
                            console.error('Price calculation error:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching price:', error);
                        totalPriceInput.value = '0.00';
                    });
            };

            // Initial call on page load to set initial state (useful for edit mode)
            handleEquipmentSelection(); // This also triggers an initial price calculation
        });
    </script>
</body>
</html>