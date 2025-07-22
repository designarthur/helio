<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($equipment)) Edit Equipment: {{ $equipment->internal_id ?? $equipment->id }} @else Add New Equipment @endif</title>
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
            @if(isset($equipment)) Edit Equipment: {{ $equipment->internal_id ?? $equipment->id }} @else Add New Equipment @endif
        </h3>

        <form id="equipmentForm" method="POST" action="@if(isset($equipment)) {{ route('equipment.update', $equipment->id) }} @else {{ route('equipment.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($equipment)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="equipmentId" name="id" value="{{ $equipment->id ?? '' }}">

                <div class="col-span-1 md:col-span-2">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Equipment Type:</label>
                    <select id="type" name="type" required onchange="toggleTypeSpecificFields()"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Type</option>
                        <option value="Dumpster" {{ (old('type', $equipment->type ?? '') == 'Dumpster') ? 'selected' : '' }}>Dumpster</option>
                        <option value="Temporary Toilet" {{ (old('type', $equipment->type ?? '') == 'Temporary Toilet') ? 'selected' : '' }}>Temporary Toilet</option>
                        <option value="Storage Container" {{ (old('type', $equipment->type ?? '') == 'Storage Container') ? 'selected' : '' }}>Storage Container</option>
                    </select>
                    @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Size:</label>
                    <input type="text" id="size" name="size" placeholder="e.g., 20-yard, 10ft, Standard" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('size', $equipment->size ?? '') }}">
                    @error('size')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                    <select id="status" name="status" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="Available" {{ (old('status', $equipment->status ?? '') == 'Available') ? 'selected' : '' }}>Available</option>
                        <option value="On Rent" {{ (old('status', $equipment->status ?? '') == 'On Rent') ? 'selected' : '' }}>On Rent</option>
                        <option value="In Maintenance" {{ (old('status', $equipment->status ?? '') == 'In Maintenance') ? 'selected' : '' }}>In Maintenance</option>
                        <option value="Out of Service" {{ (old('status', $equipment->status ?? '') == 'Out of Service') ? 'selected' : '' }}>Out of Service</option>
                        <option value="Reserved" {{ (old('status', $equipment->status ?? '') == 'Reserved') ? 'selected' : '' }}>Reserved</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Current Location:</label>
                    <input type="text" id="location" name="location" placeholder="e.g., Yard A, Customer Site X" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('location', $equipment->location ?? '') }}">
                    @error('location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-1">Purchase Date:</label>
                    <input type="date" id="purchase_date" name="purchase_date"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('purchase_date', $equipment->purchase_date ? $equipment->purchase_date->format('Y-m-d') : '') }}">
                    @error('purchase_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="purchase_cost" class="block text-sm font-medium text-gray-700 mb-1">Purchase Cost ($):</label>
                    <input type="number" id="purchase_cost" name="purchase_cost" min="0" step="0.01"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('purchase_cost', $equipment->purchase_cost ?? '') }}">
                    @error('purchase_cost')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="supplier_manufacturer" class="block text-sm font-medium text-gray-700 mb-1">Supplier/Manufacturer:</label>
                    <input type="text" id="supplier_manufacturer" name="supplier_manufacturer" placeholder="e.g., ABC Supply Co."
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('supplier_manufacturer', $equipment->supplier_manufacturer ?? '') }}">
                    @error('supplier_manufacturer')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Internal Notes):</label>
                    <textarea id="description" name="description" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('description', $equipment->description ?? '') }}
                    </textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- We are not implementing image upload functionality for now as it requires backend storage --}}
                {{--
                <div class="col-span-1 md:col-span-2">
                    <label for="equipmentImages" class="block text-sm font-medium text-gray-700 mb-1">Equipment Images (Dummy):</label>
                    <input type="file" id="equipmentImages" multiple accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-chili-red file:text-white hover:file:bg-tangelo"/>
                    <p class="text-xs text-gray-500 mt-1">Note: File upload is for demo purposes; actual files won't be stored.</p>
                </div>
                --}}

                {{-- Type-Specific Fields --}}
                <div id="dumpsterFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Dumpster Specifics</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="dumpster_dimensions" class="block text-sm font-medium text-gray-700 mb-1">Dimensions (LxWxH):</label>
                            <input type="text" id="dumpster_dimensions" name="dumpster_dimensions" placeholder="e.g., 20x8x5 ft"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('dumpster_dimensions', $equipment->dumpster_dimensions ?? '') }}">
                            @error('dumpster_dimensions')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="max_tonnage" class="block text-sm font-medium text-gray-700 mb-1">Max Tonnage:</label>
                            <input type="number" id="max_tonnage" name="max_tonnage" min="0" step="0.1"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('max_tonnage', $equipment->max_tonnage ?? '') }}">
                            @error('max_tonnage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="dumpster_container_type" class="block text-sm font-medium text-gray-700 mb-1">Container Type:</label>
                            <select id="dumpster_container_type" name="dumpster_container_type"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="">Select</option>
                                <option value="Roll-off" {{ (old('dumpster_container_type', $equipment->dumpster_container_type ?? '') == 'Roll-off') ? 'selected' : '' }}>Roll-off</option>
                                <option value="Front-load" {{ (old('dumpster_container_type', $equipment->dumpster_container_type ?? '') == 'Front-load') ? 'selected' : '' }}>Front-load</option>
                                <option value="Rear-load" {{ (old('dumpster_container_type', $equipment->dumpster_container_type ?? '') == 'Rear-load') ? 'selected' : '' }}>Rear-load</option>
                            </select>
                            @error('dumpster_container_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="gate_type" class="block text-sm font-medium text-gray-700 mb-1">Gate Type:</label>
                            <select id="gate_type" name="gate_type"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="">Select</option>
                                <option value="Walk-in Door" {{ (old('gate_type', $equipment->gate_type ?? '') == 'Walk-in Door') ? 'selected' : '' }}>Walk-in Door</option>
                                <option value="No Door" {{ (old('gate_type', $equipment->gate_type ?? '') == 'No Door') ? 'selected' : '' }}>No Door</option>
                            </select>
                            @error('gate_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="prohibited_materials" class="block text-sm font-medium text-gray-700 mb-1">Prohibited Materials (comma-separated):</label>
                            <textarea id="prohibited_materials" name="prohibited_materials" rows="2" placeholder="e.g., Tires, Batteries, Hazardous Waste"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('prohibited_materials', is_array($equipment->prohibited_materials ?? null) ? implode(', ', $equipment->prohibited_materials) : ($equipment->prohibited_materials ?? '')) }}
                            </textarea>
                            @error('prohibited_materials')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div id="toiletFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Temporary Toilet Specifics</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="toilet_capacity" class="block text-sm font-medium text-gray-700 mb-1">Capacity (Gallons/Uses):</label>
                            <input type="text" id="toilet_capacity" name="toilet_capacity" placeholder="e.g., 60 Gallons, 200 Uses"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('toilet_capacity', $equipment->toilet_capacity ?? '') }}">
                            @error('toilet_capacity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="service_frequency" class="block text-sm font-medium text-gray-700 mb-1">Default Service Frequency:</label>
                            <select id="service_frequency" name="service_frequency"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="">Select</option>
                                <option value="Weekly" {{ (old('service_frequency', $equipment->service_frequency ?? '') == 'Weekly') ? 'selected' : '' }}>Weekly</option>
                                <option value="Bi-weekly" {{ (old('service_frequency', $equipment->service_frequency ?? '') == 'Bi-weekly') ? 'selected' : '' }}>Bi-weekly</option>
                                <option value="Event-specific" {{ (old('service_frequency', $equipment->service_frequency ?? '') == 'Event-specific') ? 'selected' : '' }}>Event-specific</option>
                                <option value="Monthly" {{ (old('service_frequency', $equipment->service_frequency ?? '') == 'Monthly') ? 'selected' : '' }}>Monthly</option>
                            </select>
                            @error('service_frequency')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="toilet_features" class="block text-sm font-medium text-gray-700 mb-1">Features (Interior/Exterior, comma-separated):</label>
                            <textarea id="toilet_features" name="toilet_features" rows="2" placeholder="e.g., Sink, Urinal, Mirror, Solar Light"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('toilet_features', is_array($equipment->toilet_features ?? null) ? implode(', ', $equipment->toilet_features) : ($equipment->toilet_features ?? '')) }}
                            </textarea>
                            @error('toilet_features')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div id="containerFields" class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4 hidden">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Storage Container Specifics</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="storage_container_type" class="block text-sm font-medium text-gray-700 mb-1">Container Type:</label>
                            <select id="storage_container_type" name="storage_container_type"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="">Select</option>
                                <option value="Standard" {{ (old('storage_container_type', $equipment->storage_container_type ?? '') == 'Standard') ? 'selected' : '' }}>Standard</option>
                                <option value="High Cube" {{ (old('storage_container_type', $equipment->storage_container_type ?? '') == 'High Cube') ? 'selected' : '' }}>High Cube</option>
                                <option value="Open Top" {{ (old('storage_container_type', $equipment->storage_container_type ?? '') == 'Open Top') ? 'selected' : '' }}>Open Top</option>
                                <option value="Reefer" {{ (old('storage_container_type', $equipment->storage_container_type ?? '') == 'Reefer') ? 'selected' : '' }}>Reefer (Refrigerated)</option>
                            </select>
                            @error('storage_container_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="door_type" class="block text-sm font-medium text-gray-700 mb-1">Door Type:</label>
                            <select id="door_type" name="door_type"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="">Select</option>
                                <option value="Roll-up" {{ (old('door_type', $equipment->door_type ?? '') == 'Roll-up') ? 'selected' : '' }}>Roll-up</option>
                                <option value="Swing-out" {{ (old('door_type', $equipment->door_type ?? '') == 'Swing-out') ? 'selected' : '' }}>Swing-out</option>
                            </select>
                            @error('door_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">Condition:</label>
                            <select id="condition" name="condition"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="">Select</option>
                                <option value="Wind & Watertight" {{ (old('condition', $equipment->condition ?? '') == 'Wind & Watertight') ? 'selected' : '' }}>Wind & Watertight</option>
                                <option value="Cargo Worthy" {{ (old('condition', $equipment->condition ?? '') == 'Cargo Worthy') ? 'selected' : '' }}>Cargo Worthy</option>
                                <option value="As-Is" {{ (old('condition', $equipment->condition ?? '') == 'As-Is') ? 'selected' : '' }}>As-Is</option>
                            </select>
                            @error('condition')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="security_features" class="block text-sm font-medium text-gray-700 mb-1">Security Features (comma-separated):</label>
                            <textarea id="security_features" name="security_features" rows="2" placeholder="e.g., Lockbox, Reinforced Doors, Alarm System"
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                                {{ old('security_features', is_array($equipment->security_features ?? null) ? implode(', ', $equipment->security_features) : ($equipment->security_features ?? '')) }}
                            </textarea>
                            @error('security_features')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Rental Pricing & Rules --}}
                <div class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Rental Pricing & Rules</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="base_daily_rate" class="block text-sm font-medium text-gray-700 mb-1">Base Daily Rate ($):</label>
                            <input type="number" id="base_daily_rate" name="base_daily_rate" min="0" step="0.01" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('base_daily_rate', $equipment->base_daily_rate ?? '') }}">
                            @error('base_daily_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="default_rental_period" class="block text-sm font-medium text-gray-700 mb-1">Default Rental Period (days):</label>
                            <input type="number" id="default_rental_period" name="default_rental_period" min="1" step="1" placeholder="e.g., 7"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('default_rental_period', $equipment->default_rental_period ?? '') }}">
                            @error('default_rental_period')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="min_rental_period" class="block text-sm font-medium text-gray-700 mb-1">Minimum Rental Period (days):</label>
                            <input type="number" id="min_rental_period" name="min_rental_period" min="1" step="1" placeholder="e.g., 1"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('min_rental_period', $equipment->min_rental_period ?? '') }}">
                            @error('min_rental_period')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="extended_daily_rate" class="block text-sm font-medium text-gray-700 mb-1">Extended Daily Rate ($):</label>
                            <input type="number" id="extended_daily_rate" name="extended_daily_rate" min="0" step="0.01" placeholder="e.g., 10.00"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('extended_daily_rate', $equipment->extended_daily_rate ?? '') }}">
                            @error('extended_daily_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">Delivery Fee ($):</label>
                            <input type="number" id="delivery_fee" name="delivery_fee" min="0" step="0.01" placeholder="e.g., 50.00"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('delivery_fee', $equipment->delivery_fee ?? '') }}">
                            @error('delivery_fee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="pickup_fee" class="block text-sm font-medium text-gray-700 mb-1">Pickup Fee ($):</label>
                            <input type="number" id="pickup_fee" name="pickup_fee" min="0" step="0.01" placeholder="e.g., 50.00"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('pickup_fee', $equipment->pickup_fee ?? '') }}">
                            @error('pickup_fee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="overage_per_ton_fee" class="block text-sm font-medium text-gray-700 mb-1">Overage Per Ton Fee ($) (Dumpsters):</label>
                            <input type="number" id="overage_per_ton_fee" name="overage_per_ton_fee" min="0" step="0.01" placeholder="e.g., 75.00"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('overage_per_ton_fee', $equipment->overage_per_ton_fee ?? '') }}">
                            @error('overage_per_ton_fee')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="disposal_rate_per_ton" class="block text-sm font-medium text-gray-700 mb-1">Disposal Rate Per Ton ($) (Dumpsters):</label>
                            <input type="number" id="disposal_rate_per_ton" name="disposal_rate_per_ton" min="0" step="0.01" placeholder="e.g., 50.00"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('disposal_rate_per_ton', $equipment->disposal_rate_per_ton ?? '') }}">
                            @error('disposal_rate_per_ton')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label for="damage_waiver_cost" class="block text-sm font-medium text-gray-700 mb-1">Damage Waiver Cost ($):</label>
                            <input type="number" id="damage_waiver_cost" name="damage_waiver_cost" min="0" step="0.01" placeholder="e.g., 25.00"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   value="{{ old('damage_waiver_cost', $equipment->damage_waiver_cost ?? '') }}">
                            @error('damage_waiver_cost')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('equipment.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        @if(isset($equipment)) Save Changes @else Add Equipment @endif
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipmentTypeSelect = document.getElementById('type');
            const dumpsterFields = document.getElementById('dumpsterFields');
            const toiletFields = document.getElementById('toiletFields');
            const containerFields = document.getElementById('containerFields');

            // Function to hide all type-specific fields and clear their values
            function hideAllTypeSpecificFields() {
                dumpsterFields.classList.add('hidden');
                toiletFields.classList.add('hidden');
                containerFields.classList.add('hidden');

                // Clear values only if it's a new form (not editing) OR if the field type is being changed
                // For editing, we only clear fields if the *type is changing*, otherwise existing values are fine.
                const isEditing = {{ isset($equipment) ? 'true' : 'false' }};
                const currentType = equipmentTypeSelect.value; // The type currently selected in the dropdown

                const clearField = (elementId) => {
                    const el = document.getElementById(elementId);
                    if (el) el.value = '';
                };

                // Clear Dumpster fields
                clearField('dumpster_dimensions');
                clearField('max_tonnage');
                clearField('overage_per_ton_fee');
                clearField('disposal_rate_per_ton');
                clearField('dumpster_container_type');
                clearField('gate_type');
                clearField('prohibited_materials');

                // Clear Toilet fields
                clearField('toilet_capacity');
                clearField('service_frequency');
                clearField('toilet_features');

                // Clear Container fields
                clearField('storage_container_type');
                clearField('door_type');
                clearField('condition');
                clearField('security_features');
            }

            // Function to show/hide relevant fields based on selected type
            window.toggleTypeSpecificFields = function() { // Make global for onchange attribute
                hideAllTypeSpecificFields(); // Always hide all first

                const selectedType = equipmentTypeSelect.value;

                // Show the relevant container
                if (selectedType === 'Dumpster') {
                    dumpsterFields.classList.remove('hidden');
                } else if (selectedType === 'Temporary Toilet') {
                    toiletFields.classList.remove('hidden');
                } else if (selectedType === 'Storage Container') {
                    containerFields.classList.remove('hidden');
                }
            };

            // Call on page load to set initial state (useful for edit mode)
            toggleTypeSpecificFields();
        });
    </script>
</body>
</html>