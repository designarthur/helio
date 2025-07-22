<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Booking Details: {{ $booking->id }}</title>
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
            Booking Details: <span id="detailBookingId">{{ $booking->id }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Booking Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Customer:</strong> <span id="detailCustomerName">{{ $booking->customer->name ?? 'N/A' }}</span></p>
                    <p><strong>Equipment:</strong> <span id="detailEquipmentDisplay">{{ $booking->equipment->type ?? 'N/A' }} ({{ $booking->equipment->size ?? 'N/A' }})</span></p>
                    <p><strong>Start Date:</strong> <span id="detailRentalStartDate">{{ $booking->rental_start_date->format('Y-m-d') }}</span></p>
                    <p><strong>End Date:</strong> <span id="detailRentalEndDate">{{ $booking->rental_end_date->format('Y-m-d') }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Delivery Address:</strong> <span id="detailDeliveryAddress">{{ $booking->delivery_address }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Pickup Address:</strong> <span id="detailPickupAddress">{{ $booking->pickup_address ?? 'N/A' }}</span></p>
                    <p><strong>Status:</strong>
                        <span id="detailBookingStatus" class="px-3 py-1 rounded-full text-xs font-bold uppercase text-white
                            @if($booking->status == 'Pending') bg-ut-orange
                            @elseif($booking->status == 'Confirmed') bg-green-600
                            @elseif($booking->status == 'Delivered') bg-blue-600
                            @elseif($booking->status == 'Completed') bg-gray-500
                            @elseif($booking->status == 'Cancelled') bg-red-600
                            @endif
                        ">{{ $booking->status }}</span>
                    </p>
                    <p><strong>Total Price:</strong> $<span id="detailTotalPriceDisplay">{{ number_format($booking->total_price, 2) }}</span></p>
                    <p><strong>Assigned Driver:</strong> <span id="detailDriverName">{{ $booking->driver->name ?? 'Unassigned' }}</span></p>
                </div>
            </div>

            {{-- Dumpster Specific Booking Fields --}}
            @if($booking->equipment && $booking->equipment->type === 'Dumpster')
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailDumpsterBookingFields">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Dumpster Booking Specifics</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Estimated Tonnage:</strong> <span id="detailEstimatedTonnage">{{ $booking->estimated_tonnage ? number_format($booking->estimated_tonnage, 2) . ' tons' : 'N/A' }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Prohibited Materials Ack:</strong> <span id="detailProhibitedMaterialsAck">{{ $booking->prohibited_materials_ack ?? 'N/A' }}</span></p>
                </div>
            </div>
            @endif

            {{-- Temporary Toilet Specific Booking Fields --}}
            @if($booking->equipment && $booking->equipment->type === 'Temporary Toilet')
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailToiletBookingFields">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Temporary Toilet Booking Specifics</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Requested Service Freq:</strong> <span id="detailRequestedServiceFreq">{{ $booking->requested_service_freq ?? 'N/A' }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Special Requests:</strong> <span id="detailToiletSpecialRequests">{{ $booking->toilet_special_requests ?? 'N/A' }}</span></p>
                </div>
            </div>
            @endif

            {{-- Storage Container Specific Booking Fields --}}
            @if($booking->equipment && $booking->equipment->type === 'Storage Container')
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailContainerBookingFields">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Storage Container Booking Specifics</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p class="col-span-1 md:col-span-2"><strong>Placement Notes:</strong> <span id="detailContainerPlacementNotes">{{ $booking->container_placement_notes ?? 'N/A' }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Security/Access:</strong> <span id="detailContainerSecurityAccess">{{ $booking->container_security_access ?? 'N/A' }}</span></p>
                </div>
            </div>
            @endif

            @if($booking->booking_notes)
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailBookingNotesGroup">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Booking Notes (General)</h4>
                <p><span id="detailBookingNotes">{{ $booking->booking_notes }}</span></p>
            </div>
            @endif
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('customer.bookings.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
            {{-- Edit button for customers is often restricted for confirmed bookings, but can link to a modification request --}}
            {{-- <a href="#" onclick="alert('Booking modification is a conceptual feature for customers. Please contact support to modify.')" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">Modify Booking</a> --}}
        </div>
    </div>
</body>
</html>