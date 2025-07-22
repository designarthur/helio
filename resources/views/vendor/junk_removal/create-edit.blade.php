<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($junkRemovalJob)) Edit Job: {{ $junkRemovalJob->job_number }} @else Create New Junk Removal Job @endif</title>
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
            @if(isset($junkRemovalJob)) Edit Junk Removal Job: {{ $junkRemovalJob->job_number }} @else Create New Junk Removal Job @endif
        </h3>

        <form id="junkRemovalForm" method="POST" action="@if(isset($junkRemovalJob)) {{ route('junk_removal_jobs.update', $junkRemovalJob->id) }} @else {{ route('junk_removal_jobs.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($junkRemovalJob)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="jobId" name="id" value="{{ $junkRemovalJob->id ?? '' }}">

                <div class="col-span-1 md:col-span-2">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer:</label>
                    <select id="customer_id" name="customer_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (old('customer_id', $junkRemovalJob->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->company ?? 'Residential' }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="requested_date" class="block text-sm font-medium text-gray-700 mb-1">Requested Date:</label>
                    <input type="date" id="requested_date" name="requested_date" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('requested_date', $junkRemovalJob->requested_date ? $junkRemovalJob->requested_date->format('Y-m-d') : '') }}">
                    @error('requested_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="requested_time" class="block text-sm font-medium text-gray-700 mb-1">Requested Time (Optional):</label>
                    <input type="time" id="requested_time" name="requested_time"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('requested_time', $junkRemovalJob->requested_time ? $junkRemovalJob->requested_time->format('H:i') : '') }}">
                    @error('requested_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="job_location" class="block text-sm font-medium text-gray-700 mb-1">Job Location:</label>
                    <input type="text" id="job_location" name="job_location" placeholder="123 Main St, Anytown" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('job_location', $junkRemovalJob->job_location ?? '') }}">
                    @error('job_location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="description_of_junk" class="block text-sm font-medium text-gray-700 mb-1">Description of Junk:</label>
                    <textarea id="description_of_junk" name="description_of_junk" rows="3" placeholder="e.g., Old furniture, yard waste, construction debris" required
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('description_of_junk', $junkRemovalJob->description_of_junk ?? '') }}
                    </textarea>
                    @error('description_of_junk')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="volume_estimate" class="block text-sm font-medium text-gray-700 mb-1">Volume Estimate (Cu Yards/Truckloads):</label>
                    <input type="text" id="volume_estimate" name="volume_estimate" placeholder="e.g., 2 cu yards, 1/2 truckload"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('volume_estimate', $junkRemovalJob->volume_estimate ?? '') }}">
                    @error('volume_estimate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="weight_estimate" class="block text-sm font-medium text-gray-700 mb-1">Weight Estimate (Lbs/Tons):</label>
                    <input type="text" id="weight_estimate" name="weight_estimate" placeholder="e.g., 500 lbs, 0.25 tons"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('weight_estimate', $junkRemovalJob->weight_estimate ?? '') }}">
                    @error('weight_estimate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="crew_requirements" class="block text-sm font-medium text-gray-700 mb-1">Crew Requirements:</label>
                    <input type="number" id="crew_requirements" name="crew_requirements" min="1" value="{{ old('crew_requirements', $junkRemovalJob->crew_requirements ?? '1') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                    @error('crew_requirements')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="assigned_driver" class="block text-sm font-medium text-gray-700 mb-1">Assigned Driver (Optional):</label>
                    <select id="assigned_driver" name="assigned_driver"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Unassigned</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ (old('assigned_driver', $junkRemovalJob->assigned_driver ?? '') == $driver->id) ? 'selected' : '' }}>
                                {{ $driver->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_driver')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label for="estimated_price" class="block text-sm font-medium text-gray-700 mb-1">Estimated Price ($):</label>
                    <input type="number" id="estimated_price" name="estimated_price" min="0" step="0.01" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('estimated_price', $junkRemovalJob->estimated_price ?? '') }}">
                    @error('estimated_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                    <select id="status" name="status" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="Pending Quote" {{ (old('status', $junkRemovalJob->status ?? '') == 'Pending Quote') ? 'selected' : '' }}>Pending Quote</option>
                        <option value="Quoted" {{ (old('status', $junkRemovalJob->status ?? '') == 'Quoted') ? 'selected' : '' }}>Quoted</option>
                        <option value="Scheduled" {{ (old('status', $junkRemovalJob->status ?? '') == 'Scheduled') ? 'selected' : '' }}>Scheduled</option>
                        <option value="In Progress" {{ (old('status', $junkRemovalJob->status ?? '') == 'In Progress') ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ (old('status', $junkRemovalJob->status ?? '') == 'Completed') ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ (old('status', $junkRemovalJob->status ?? '') == 'Cancelled') ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="job_notes" class="block text-sm font-medium text-gray-700 mb-1">Internal Notes:</label>
                    <textarea id="job_notes" name="job_notes" rows="3" placeholder="Any specific instructions or customer notes."
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('job_notes', $junkRemovalJob->job_notes ?? '') }}
                    </textarea>
                    @error('job_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('junk_removal_jobs.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        @if(isset($junkRemovalJob)) Save Changes @else Create Job @endif
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>