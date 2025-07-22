<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Junk Removal Job: {{ $junkRemovalJob->job_number }}</title>
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
            Junk Removal Job: <span id="detailJobId">{{ $junkRemovalJob->job_number }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Job Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <p><strong>Customer:</strong> <span id="detailJrCustomerName">{{ $junkRemovalJob->customer->name ?? 'N/A' }}</span></p>
                    <p><strong>Requested:</strong> <span id="detailRequestedDateTime">{{ $junkRemovalJob->requested_date->format('Y-m-d') }} {{ $junkRemovalJob->requested_time ? $junkRemovalJob->requested_time->format('H:i') : '' }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Location:</strong> <span id="detailJobLocation">{{ $junkRemovalJob->job_location }}</span></p>
                    <p class="col-span-1 md:col-span-2"><strong>Description:</strong> <span id="detailDescriptionOfJunk">{{ $junkRemovalJob->description_of_junk }}</span></p>
                    <p><strong>Volume Est.:</strong> <span id="detailVolumeEstimate">{{ $junkRemovalJob->volume_estimate ?? 'N/A' }}</span></p>
                    <p><strong>Weight Est.:</strong> <span id="detailWeightEstimate">{{ $junkRemovalJob->weight_estimate ?? 'N/A' }}</span></p>
                    <p><strong>Crew Needed:</strong> <span id="detailCrewRequirements">{{ $junkRemovalJob->crew_requirements }}</span></p>
                    <p><strong>Assigned Driver:</strong> <span id="detailAssignedDriver">{{ $junkRemovalJob->driver->name ?? 'Unassigned' }}</span></p>
                    <p><strong>Est. Price:</strong> $<span id="detailEstimatedPrice">{{ number_format($junkRemovalJob->estimated_price, 2) }}</span></p>
                    <p><strong>Status:</strong>
                        <span id="detailJobStatus" class="px-3 py-1 rounded-full text-xs font-bold uppercase text-white
                            @if($junkRemovalJob->status == 'Completed') bg-green-600
                            @elseif(in_array($junkRemovalJob->status, ['Scheduled', 'In Progress', 'Quoted'])) bg-blue-600
                            @elseif($junkRemovalJob->status == 'Pending Quote') bg-ut-orange
                            @elseif($junkRemovalJob->status == 'Cancelled') bg-red-600
                            @endif
                        ">{{ $junkRemovalJob->status }}</span>
                    </p>
                </div>
            </div>

            @if($junkRemovalJob->job_notes)
            <div class="bg-gray-50 p-4 rounded-md shadow-sm" id="detailJobNotesGroup">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Internal Notes</h4>
                <p><span id="detailJobNotes">{{ $junkRemovalJob->job_notes }}</span></p>
            </div>
            @endif
            
            {{-- Optional: Display uploaded images/videos here if you implement file storage --}}
            {{-- @if($junkRemovalJob->customer_uploaded_images && count($junkRemovalJob->customer_uploaded_images) > 0)
            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-2 border-b border-dashed border-gray-300 pb-2">Customer Uploaded Visuals</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($junkRemovalJob->customer_uploaded_images as $imagePath)
                        <img src="{{ asset('storage/' . $imagePath) }}" alt="Junk Image" class="w-full h-32 object-cover rounded-md shadow">
                    @endforeach
                </div>
            </div>
            @endif --}}
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('junk_removal_jobs.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
            <a href="{{ route('junk_removal_jobs.edit', $junkRemovalJob->id) }}" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200" id="editFromDetailBtn">Edit Job</a>
        </div>
    </div>
</body>
</html>