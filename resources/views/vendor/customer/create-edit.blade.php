@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ isset($customer) ? 'Edit Customer' : 'Add New Customer' }}</h2>

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

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form action="{{ isset($customer) ? route('customers.update', $customer->id) : route('customers.store') }}" method="POST">
            @csrf
            @if(isset($customer))
                @method('PUT') {{-- Use PUT for updates --}}
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" id="first_name"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('first_name', $customer->first_name ?? '') }}" required>
                </div>

                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" id="last_name"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('last_name', $customer->last_name ?? '') }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('email', $customer->email ?? '') }}" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone_number" id="phone_number"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('phone_number', $customer->phone_number ?? '') }}">
                </div>

                <div class="mb-4">
                    <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-1">Customer Type</label>
                    <select name="customer_type" id="customer_type"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                            required>
                        <option value="">Select Type</option>
                        <option value="Residential" {{ (old('customer_type', $customer->customer_type ?? '') == 'Residential') ? 'selected' : '' }}>Residential</option>
                        <option value="Commercial" {{ (old('customer_type', $customer->customer_type ?? '') == 'Commercial') ? 'selected' : '' }}>Commercial</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="address" id="address"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                       value="{{ old('address', $customer->address ?? '') }}">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" id="city"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('city', $customer->city ?? '') }}">
                </div>

                <div class="mb-4">
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                    <input type="text" name="state" id="state"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('state', $customer->state ?? '') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">Zip/Postal Code</label>
                    <input type="text" name="zip_code" id="zip_code"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('zip_code', $customer->zip_code ?? '') }}">
                </div>

                <div class="mb-4">
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" name="country" id="country"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('country', $customer->country ?? '') }}">
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                          placeholder="Any additional notes about the customer">{{ old('notes', $customer->notes ?? '') }}</textarea>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('customers.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    {{ isset($customer) ? 'Update Customer' : 'Add Customer' }}
                </button>
            </div>
        </form>
    </div>
@endsection