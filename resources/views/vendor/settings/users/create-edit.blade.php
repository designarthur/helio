@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ isset($user) ? 'Edit User' : 'Add New User' }}</h2>

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
        <form action="{{ isset($user) ? route('settings.users.update', $user->id) : route('settings.users.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT') {{-- Use PUT for updates --}}
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="name"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('name', $user->name ?? '') }}" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           value="{{ old('email', $user->email ?? '') }}" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ isset($user) ? 'New Password (optional)' : 'Password' }}
                    </label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           {{ isset($user) ? '' : 'required' }}>
                    @if(isset($user))
                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password.</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ isset($user) ? 'Confirm New Password' : 'Confirm Password' }}
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                           {{ isset($user) ? '' : 'required' }}>
                </div>
            </div>

            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-chili-red focus:border-chili-red sm:text-sm"
                        required>
                    <option value="">Select a Role</option>
                    <option value="admin" {{ (old('role', $user->role ?? '') == 'admin') ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ (old('role', $user->role ?? '') == 'manager') ? 'selected' : '' }}>Manager</option>
                    <option value="staff" {{ (old('role', $user->role ?? '') == 'staff') ? 'selected' : '' }}>Staff</option>
                    <option value="driver" {{ (old('role', $user->role ?? '') == 'driver') ? 'selected' : '' }}>Driver</option>
                </select>
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" name="is_active" id="is_active"
                       class="h-4 w-4 text-chili-red border-gray-300 rounded focus:ring-chili-red"
                       {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active User</label>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('settings.users.index') }}" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    {{ isset($user) ? 'Update User' : 'Add User' }}
                </button>
            </div>
        </form>
    </div>
@endsection