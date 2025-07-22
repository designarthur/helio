@extends('layouts.vendor-app')

@section('content')
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Users & Roles Management</h2>

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

    <div class="flex border-b border-gray-200 mb-8 space-x-6">
        {{-- Settings Tabs --}}
        <a href="{{ route('settings.show') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="general">General</a>
        <a href="{{ route('settings.profile') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="profile">Profile</a>
        <a href="{{ route('settings.notifications') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="notifications">Notifications</a>
        <a href="{{ route('settings.integrations') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="integrations">Integrations</a>
        <span class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent text-[#EA3A26] border-[#EA3A26]" data-tab="users">Users & Roles</span>
        <a href="{{ route('settings.subscription') }}" class="settings-tab py-2 px-0 cursor-pointer font-bold text-gray-500 transition-colors duration-300 hover:text-gray-800 border-b-2 border-transparent" data-tab="subscription">Subscription</a>
    </div>

    <div id="settings-tab-content-users" class="tab-content">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <a href="{{ route('settings.users.create') }}" class="px-6 py-3 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Add New User
            </a>
            <div class="flex flex-wrap items-center gap-4">
                <form action="{{ route('settings.users.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                    <input type="text" id="userSearch" name="search" placeholder="Search by name, email..."
                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto"
                           value="{{ request('search') }}">

                    <select id="roleFilter" name="role_filter"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-chili-red w-full md:w-auto">
                        <option value="">All Roles</option>
                        <option value="Admin" @if(request('role_filter') == 'Admin') selected @endif>Admin</option>
                        <option value="Manager" @if(request('role_filter') == 'Manager') selected @endif>Manager</option>
                        <option value="Staff" @if(request('role_filter') == 'Staff') selected @endif>Staff</option>
                        <option value="Driver" @if(request('role_filter') == 'Driver') selected @endif>Driver</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors duration-200">Apply Filters</button>
                    <a href="{{ route('settings.users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition-colors duration-200">Reset</a>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-x-auto mb-8">
            <table class="min-w-full divide-y divide-gray-200" id="usersTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($user->role) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($user->is_active) bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('settings.users.show', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 transition-colors duration-200">View</a>
                                <a href="{{ route('settings.users.edit', $user->id) }}" class="text-chili-red hover:text-tangelo mr-3 transition-colors duration-200">Edit</a>
                                <form action="{{ route('settings.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <script>
        // Set active settings tab
        document.addEventListener('DOMContentLoaded', () => {
            const settingsTabs = document.querySelectorAll('.settings-tab');
            settingsTabs.forEach(tab => {
                tab.classList.remove('text-[#EA3A26]', 'border-[#EA3A26]');
                tab.classList.add('text-gray-500', 'border-transparent');
            });
            // Set 'users' as active
            document.querySelector('.settings-tab[data-tab="users"]').classList.add('text-[#EA3A26]', 'border-[#EA3A26]');
            document.querySelector('.settings-tab[data-tab="users"]').classList.remove('text-gray-500', 'border-transparent');
        });
    </script>
@endsection