<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Driver Details: {{ $driver->name }}</title>
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
            Driver Details: <span id="detailDriverId">{{ $driver->id }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">General Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Name:</strong> <span id="detailDriverName">{{ $driver->name }}</span></p>
                    <p><strong>Email:</strong> <span id="detailDriverEmail">{{ $driver->email }}</span></p>
                    <p><strong>Phone:</strong> <span id="detailDriverPhone">{{ $driver->phone ?? 'N/A' }}</span></p>
                    <p><strong>License Number:</strong> <span id="detailLicenseNumber">{{ $driver->license_number ?? 'N/A' }}</span></p>
                    <p><strong>License Expiry:</strong> <span id="detailLicenseExpiry">{{ $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : 'N/A' }}</span></p>
                    <p><strong>CDL Class:</strong> <span id="detailCdlClass">{{ $driver->cdl_class ?? 'N/A' }}</span></p>
                    <p><strong>Status:</strong> <span id="detailDriverStatus">{{ $driver->status }}</span></p>
                    <p><strong>Assigned Vehicle:</strong> <span id="detailAssignedVehicle">{{ $driver->assigned_vehicle ?? 'N/A' }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Certifications:</strong> <span id="detailCertifications">{{ is_array($driver->certifications) ? implode(', ', $driver->certifications) : ($driver->certifications ?? 'N/A') }}</span></p>
                </div>
            </div>

            @if($driver->driver_notes)
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailDriverNotesGroup">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Internal Notes</h4>
                <p><span id="detailDriverNotes">{{ $driver->driver_notes }}</span></p>
            </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Assigned Bookings (Current/Upcoming)</h4>
                <div id="assignedBookingsList" class="space-y-2">
                    @forelse($assignedBookings as $booking)
                        <div class="border-b border-dashed border-gray-300 pb-2 mb-2 last:border-b-0">
                            <p class="font-medium"><strong>Booking {{ $booking->id }}:</strong> {{ $booking->customer->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600 ml-2">Equipment: {{ $booking->equipment->type ?? 'N/A' }} ({{ $booking->equipment->size ?? 'N/A' }})</p>
                            <p class="text-sm text-gray-600 ml-2">Date: {{ $booking->rental_start_date->format('Y-m-d') }}</p>
                            <p class="text-sm text-gray-600 ml-2">Status: {{ $booking->status }}</p>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="text-blue-600 hover:text-blue-800 text-sm ml-2">View Booking Details</a>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">No assigned bookings for this driver for the upcoming days.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Performance Metrics (Conceptual)</h4>
                <p class="text-gray-600">This section would show driver-specific performance metrics such as:
                    <ul class="list-disc list-inside ml-4 text-sm mt-2">
                        <li>On-time delivery rate.</li>
                        <li>Average service time per job.</li>
                        <li>Fuel efficiency comparisons.</li>
                        <li>Customer feedback/ratings.</li>
                    </ul>
                </p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('drivers.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
            <a href="{{ route('drivers.edit', $driver->id) }}" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200" id="editFromDetailBtn">Edit Driver</a>
        </div>
    </div>
</body>
</html>