<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Dispatching & Route Optimization</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <style>
        /* Custom scrollbar for driver columns if needed */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background-color: #cbd5e0; /* gray-300 */
            border-radius: 4px;
        }
        .overflow-x-auto::-webkit-scrollbar-track {
            background-color: #f7fafc; /* gray-50 */
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans min-h-screen">

    {{-- Main content wrapper - for now, this will be a full page, later part of a layout --}}
    <div class="p-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Dispatching & Route Optimization</h2>

        {{-- Success/Error Messages from Controller --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if (session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Info:</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Validation Error!</strong>
                <span class="block sm:inline">Please check your input.</span>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="flex border-b border-gray-200 mb-8 space-x-6">
            <a href="{{ route('dispatching.show', ['tab' => 'board']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-chili-red border-chili-red">Driver Board</a>
            <a href="{{ route('dispatching.show', ['tab' => 'list']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Job List</a>
            <a href="{{ route('dispatching.show', ['tab' => 'map']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Map View (Conceptual)</a>
            <a href="{{ route('dispatching.show', ['tab' => 'schedule']) }}" class="dispatch-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent">Driver Schedule (Conceptual)</a>
        </div>

        <div id="dispatch-view-board" class="dispatch-content-view">
            <div class="flex flex-col md:flex-row items-start justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800 mb-2 md:mb-0">Job Assignment Board</h3>
                <form action="{{ route('dispatching.simulateOptimization') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-route"></i> Simulate Route Optimization
                    </button>
                </form>
            </div>
            <div class="flex overflow-x-auto pb-4 -mx-2">
                {{-- Unassigned Jobs Column --}}
                <div class="flex-none w-80 bg-gray-100 p-4 rounded-lg shadow-md mr-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">Unassigned Jobs</h3>
                    <div id="unassignedJobsContainer" class="space-y-3 min-h-[200px]">
                        @forelse($unassignedJobs as $booking)
                            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200 cursor-pointer hover:shadow-md transition-shadow"
                                onclick="openJobActionModal('{{ $booking->id }}', '{{ $booking->customer->name ?? 'N/A' }}', '{{ $booking->equipment->type ?? 'N/A' }} ({{ $booking->equipment->size ?? 'N/A' }})', '{{ $booking->driver->name ?? 'Unassigned' }}')">
                                <p class="font-semibold text-gray-800">{{ $booking->id }} - {{ $booking->customer->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->equipment->type ?? 'N/A' }} ({{ $booking->equipment->size ?? 'N/A' }})</p>
                                <p class="text-xs text-gray-500">Delivery: {{ $booking->rental_start_date->format('Y-m-d') }} to {{ $booking->delivery_address }}</p>
                                <p class="text-xs {{ $booking->status == 'Pending' ? 'text-ut-orange' : ($booking->status == 'Confirmed' ? 'text-green-600' : 'text-gray-500') }}">Status: {{ $booking->status }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-center py-4">All jobs assigned!</p>
                        @endforelse
                    </div>
                </div>

                {{-- Driver Columns --}}
                <div id="driverColumnsContainer" class="flex flex-grow overflow-x-auto">
                    @forelse($activeDrivers as $driver)
                        <div class="flex-none w-80 bg-gray-100 p-4 rounded-lg shadow-md mr-4">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 border-gray-300">
                                {{ $driver->name }} <span class="text-sm text-gray-500">({{ $driver->assigned_vehicle ?? 'No Vehicle' }})</span>
                            </h3>
                            <div id="driver-jobs-{{ $driver->id }}" class="space-y-3 min-h-[150px]">
                                @php
                                    $driverJobs = $assignedJobsByDriver->get($driver->id, collect());
                                @endphp
                                @forelse($driverJobs as $booking)
                                    <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200 cursor-pointer hover:shadow-md transition-shadow"
                                        onclick="openJobActionModal('{{ $booking->id }}', '{{ $booking->customer->name ?? 'N/A' }}', '{{ $booking->equipment->type ?? 'N/A' }} ({{ $booking->equipment->size ?? 'N/A' }})', '{{ $booking->driver->name ?? 'Unassigned' }}')">
                                        <p class="font-semibold text-gray-800">{{ $booking->id }} - {{ $booking->customer->name ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->equipment->type ?? 'N/A' }} ({{ $booking->equipment->size ?? 'N/A' }})</p>
                                        <p class="text-xs text-gray-500">Delivery: {{ $booking->rental_start_date->format('Y-m-d') }} to {{ $booking->delivery_address }}</p>
                                        <p class="text-xs {{ $booking->status == 'Pending' ? 'text-ut-orange' : ($booking->status == 'Confirmed' ? 'text-green-600' : 'text-gray-500') }}">Status: {{ $booking->status }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-500 italic text-center py-4 text-sm">No assigned jobs.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic text-center py-4 text-sm w-full">No active drivers available for assignment.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Job Action Modal --}}
        <div id="jobActionModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden">
            <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-md relative">
                <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl font-bold" onclick="closeModal('jobActionModal')">&times;</button>
                <h3 id="jobActionModalTitle" class="text-2xl font-bold text-chili-red mb-6 border-b pb-3 border-gray-200">Assign Job</h3>
                <form id="jobActionForm" method="POST" action="{{ route('dispatching.assign') }}">
                    @csrf
                    <input type="hidden" name="booking_id" id="modalBookingId">
                    <input type="hidden" name="action" id="modalActionType"> {{-- 'assign' or 'unassign' --}}

                    <div class="space-y-4">
                        <p><strong>Booking:</strong> <span id="modalBookingInfo"></span></p>
                        <p><strong>Current Driver:</strong> <span id="modalCurrentDriver">Unassigned</span></p>

                        <div id="assignDriverField">
                            <label for="assignDriverSelect" class="block text-sm font-medium text-gray-700 mb-1">Assign to Driver:</label>
                            <select id="assignDriverSelect" name="driver_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                                <option value="">Select Driver</option>
                                @foreach($activeDrivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200" onclick="closeModal('jobActionModal')">Cancel</button>
                            <button type="submit" id="confirmAssignBtn" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">Confirm Assignment</button>
                            <button type="submit" id="unassignBtn" class="px-6 py-2 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200 hidden">Unassign Job</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global Modal Functions (copied from your original HTML for consistency)
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            // Reset form fields on close
            if (modalId === 'jobActionModal') {
                document.getElementById('jobActionForm').reset();
                document.getElementById('jobActionModalTitle').textContent = 'Assign Job';
                document.getElementById('confirmAssignBtn').textContent = 'Confirm Assignment';
                document.getElementById('unassignBtn').classList.add('hidden');
            }
        }

        let currentJobToAssign = null; // Stores the booking ID being acted upon in the modal

        function openJobActionModal(bookingId, customerName, equipmentDisplay, currentDriverName) {
            currentJobToAssign = bookingId;
            const jobActionModalTitle = document.getElementById('jobActionModalTitle');
            const modalBookingInfo = document.getElementById('modalBookingInfo');
            const modalCurrentDriver = document.getElementById('modalCurrentDriver');
            const assignDriverSelect = document.getElementById('assignDriverSelect');
            const confirmAssignBtn = document.getElementById('confirmAssignBtn');
            const unassignBtn = document.getElementById('unassignBtn');
            const modalActionType = document.getElementById('modalActionType');
            const jobActionForm = document.getElementById('jobActionForm');

            // Populate modal fields
            document.getElementById('modalBookingId').value = bookingId;
            modalBookingInfo.textContent = `${bookingId} - ${customerName} - ${equipmentDisplay}`;
            modalCurrentDriver.textContent = currentDriverName;

            // Pre-select current driver in dropdown
            const drivers = @json($activeDrivers->keyBy('id')); // Pass activeDrivers to JS for lookup
            if (currentDriverName !== 'Unassigned' && drivers) {
                const driverId = Object.keys(drivers).find(id => drivers[id].name === currentDriverName);
                if (driverId) {
                    assignDriverSelect.value = driverId;
                }
            } else {
                assignDriverSelect.value = ''; // Reset dropdown
            }

            // Configure modal buttons based on current assignment status
            if (currentDriverName !== 'Unassigned') {
                jobActionModalTitle.textContent = 'Reassign/Unassign Job';
                confirmAssignBtn.textContent = 'Change Assignment';
                unassignBtn.classList.remove('hidden'); // Show unassign button if driver is assigned
                
                // Set action type for assign button
                confirmAssignBtn.onclick = function() {
                    modalActionType.value = 'assign';
                    jobActionForm.submit();
                };
                // Set action type for unassign button
                unassignBtn.onclick = function() {
                    if (confirm('Are you sure you want to unassign this job?')) {
                        modalActionType.value = 'unassign';
                        jobActionForm.submit();
                    }
                };

            } else {
                jobActionModalTitle.textContent = 'Assign Job';
                confirmAssignBtn.textContent = 'Assign Job';
                unassignBtn.classList.add('hidden'); // Hide unassign button if no driver is assigned

                // Set action type for assign button
                confirmAssignBtn.onclick = function() {
                    modalActionType.value = 'assign';
                    jobActionForm.submit();
                };
            }

            openModal('jobActionModal');
        }

        // The simulateRouteOptimization is a form submission now, not a JS alert
        // It's handled by the Blade form directly.
    </script>
</body>
</html>