@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Junk Removal Job Details: #{{ $junkRemovalJob->id }}</h2>

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

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Job ID:</p>
                <p class="text-lg text-gray-900 font-semibold">#{{ $junkRemovalJob->id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Customer:</p>
                <p class="text-lg text-gray-900">
                    <a href="{{ route('customers.show', $junkRemovalJob->customer->id ?? '#') }}" class="text-blue-600 hover:underline">
                        {{ $junkRemovalJob->customer->first_name ?? 'N/A' }} {{ $junkRemovalJob->customer->last_name ?? '' }}
                    </a>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Scheduled Date:</p>
                <p class="text-lg text-gray-900">{{ $junkRemovalJob->scheduled_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Scheduled Time:</p>
                <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($junkRemovalJob->scheduled_time)->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Pickup Address:</p>
                <p class="text-lg text-gray-900">{{ $junkRemovalJob->pickup_address }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Estimated Price:</p>
                <p class="text-lg text-gray-900">${{ number_format($junkRemovalJob->estimated_price, 2) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status:</p>
                <p class="text-lg text-gray-900">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($junkRemovalJob->status == 'Completed') bg-green-100 text-green-800
                        @elseif($junkRemovalJob->status == 'Scheduled') bg-blue-100 text-blue-800
                        @elseif($junkRemovalJob->status == 'In Progress') bg-purple-100 text-purple-800
                        @elseif($junkRemovalJob->status == 'Pending') bg-yellow-100 text-yellow-800
                        @elseif($junkRemovalJob->status == 'Cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $junkRemovalJob->status }}
                    </span>
                </p>
            </div>
        </div>

        <div class="mt-4">
            <p class="text-sm font-medium text-gray-500">Description of Items:</p>
            <p class="text-lg text-gray-900">{{ $junkRemovalJob->description }}</p>
        </div>

        @if($junkRemovalJob->driver)
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-500">Assigned Driver:</p>
                <p class="text-lg text-gray-900">
                    <a href="{{ route('drivers.show', $junkRemovalJob->driver->id) }}" class="text-blue-600 hover:underline">
                        {{ $junkRemovalJob->driver->first_name }} {{ $junkRemovalJob->driver->last_name }}
                    </a>
                </p>
            </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('junk_removal_jobs.edit', $junkRemovalJob->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                Edit Job
            </a>
            <form action="{{ route('junk_removal_jobs.destroy', $junkRemovalJob->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this junk removal job? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 transition-colors duration-200">
                    Delete Job
                </button>
            </form>
            <a href="{{ route('junk_removal_jobs.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                Back to Jobs
            </a>
        </div>
    </div>
@endsection