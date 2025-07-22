<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Equipment Details: {{ $equipment->internal_id ?? $equipment->id }}</title>
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
            Equipment Details: <span id="detailId">{{ $equipment->internal_id ?? $equipment->id }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">General Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Type:</strong> <span id="detailType">{{ $equipment->type }}</span></p>
                    <p><strong>Size:</strong> <span id="detailSize">{{ $equipment->size }}</span></p>
                    <p><strong>Status:</strong> <span id="detailStatus">{{ $equipment->status }}</span></p>
                    <p><strong>Current Location:</strong> <span id="detailLocation">{{ $equipment->location }}</span></p>
                    <p><strong>Purchase Date:</strong> <span id="detailPurchaseDate">{{ $equipment->purchase_date ? $equipment->purchase_date->format('Y-m-d') : 'N/A' }}</span></p>
                    <p><strong>Purchase Cost:</strong> $<span id="detailPurchaseCost">{{ number_format($equipment->purchase_cost ?? 0, 2) }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Supplier/Manufacturer:</strong> <span id="detailSupplierManufacturer">{{ $equipment->supplier_manufacturer ?? 'N/A' }}</span></p>
                </div>
            </div>

            @if($equipment->description)
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailDescriptionGroup">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Internal Description</h4>
                <p><span id="detailDescription">{{ $equipment->description }}</span></p>
            </div>
            @endif

            {{-- Dumpster Specifics --}}
            @if($equipment->type === 'Dumpster')
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailDumpsterFields">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Dumpster Specifics</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Dimensions:</strong> <span id="detailDumpsterDimensions">{{ $equipment->dumpster_dimensions ?? 'N/A' }}</span></p>
                    <p><strong>Max Tonnage:</strong> <span id="detailMaxTonnage">{{ $equipment->max_tonnage ? number_format($equipment->max_tonnage, 2) . ' tons' : 'N/A' }}</span></p>
                    <p><strong>Container Type:</strong> <span id="detailDumpsterContainerType">{{ $equipment->dumpster_container_type ?? 'N/A' }}</span></p>
                    <p><strong>Gate Type:</strong> <span id="detailGateType">{{ $equipment->gate_type ?? 'N/A' }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Prohibited Materials:</strong> <span id="detailProhibitedMaterials">{{ is_array($equipment->prohibited_materials) ? implode(', ', $equipment->prohibited_materials) : ($equipment->prohibited_materials ?? 'N/A') }}</span></p>
                </div>
            </div>
            @endif

            {{-- Temporary Toilet Specifics --}}
            @if($equipment->type === 'Temporary Toilet')
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailToiletFields">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Temporary Toilet Specifics</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Capacity:</strong> <span id="detailToiletCapacity">{{ $equipment->toilet_capacity ?? 'N/A' }}</span></p>
                    <p><strong>Service Frequency:</strong> <span id="detailServiceFrequency">{{ $equipment->service_frequency ?? 'N/A' }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Features:</strong> <span id="detailToiletFeatures">{{ is_array($equipment->toilet_features) ? implode(', ', $equipment->toilet_features) : ($equipment->toilet_features ?? 'N/A') }}</span></p>
                </div>
            </div>
            @endif

            {{-- Storage Container Specifics --}}
            @if($equipment->type === 'Storage Container')
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailContainerFields">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Storage Container Specifics</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Container Type:</strong> <span id="detailStorageContainerType">{{ $equipment->storage_container_type ?? 'N/A' }}</span></p>
                    <p><strong>Door Type:</strong> <span id="detailDoorType">{{ $equipment->door_type ?? 'N/A' }}</span></p>
                    <p><strong>Condition:</strong> <span id="detailCondition">{{ $equipment->condition ?? 'N/A' }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Security Features:</strong> <span id="detailSecurityFeatures">{{ is_array($equipment->security_features) ? implode(', ', $equipment->security_features) : ($equipment->security_features ?? 'N/A') }}</span></p>
                </div>
            </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Rental Pricing & Rules</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Base Daily Rate:</strong> $<span id="detailBaseDailyRate">{{ number_format($equipment->base_daily_rate, 2) }}</span></p>
                    <p><strong>Default Rental Period:</strong> <span id="detailDefaultRentalPeriod">{{ $equipment->default_rental_period ?? 'N/A' }}</span> days</p>
                    <p><strong>Minimum Rental Period:</strong> <span id="detailMinRentalPeriod">{{ $equipment->min_rental_period ?? 'N/A' }}</span> days</p>
                    <p><strong>Extended Daily Rate:</strong> $<span id="detailExtendedDailyRate">{{ number_format($equipment->extended_daily_rate ?? 0, 2) }}</span></p>
                    <p><strong>Delivery Fee:</strong> $<span id="detailDeliveryFee">{{ number_format($equipment->delivery_fee ?? 0, 2) }}</span></p>
                    <p><strong>Pickup Fee:</strong> $<span id="detailPickupFee">{{ number_format($equipment->pickup_fee ?? 0, 2) }}</span></p>
                    <p><strong>Overage Per Ton Fee:</strong> $<span id="detailOveragePerTonFee">{{ number_format($equipment->overage_per_ton_fee ?? 0, 2) }}</span></p>
                    <p><strong>Disposal Rate Per Ton:</strong> $<span id="detailDisposalRatePerTon">{{ number_format($equipment->disposal_rate_per_ton ?? 0, 2) }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Damage Waiver Cost:</strong> $<span id="detailDamageWaiverCost">{{ number_format($equipment->damage_waiver_cost ?? 0, 2) }}</span></p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Maintenance History (Dummy)</h4>
                <p class="text-gray-600">No maintenance records found for this equipment. (In a real application, this would display a detailed log or link to a maintenance module.)</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Attached Documents (Dummy)</h4>
                <p class="text-gray-600">No documents attached. (In a real application, this would list downloadable files like manuals, repair receipts, etc.)</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('equipment.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
            <a href="{{ route('equipment.edit', $equipment->id) }}" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200" id="editFromDetailBtn">Edit Equipment</a>
        </div>
    </div>
</body>
</html>