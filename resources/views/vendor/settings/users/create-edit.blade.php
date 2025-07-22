<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($user)) Edit User: {{ $user->name }} @else Add New User @endif</title>
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
    <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-md relative max-h-[90vh] overflow-y-auto">
        <button onclick="window.history.back()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl font-bold">&times;</button>
        <h3 class="text-2xl font-bold text-chili-red mb-6 border-b pb-3 border-gray-200">
            @if(isset($user)) Edit User: {{ $user->name }} @else Add New User @endif
        </h3>

        <form id="internalUserForm" method="POST" action="@if(isset($user)) {{ route('settings.users.update', $user->id) }} @else {{ route('settings.users.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($user)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="space-y-4">
                <input type="hidden" id="internalUserId" name="id" value="{{ $user->id ?? '' }}">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name:</label>
                    <input type="text" id="name" name="name" placeholder="Jane Doe" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('name', $user->name ?? '') }}">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                    <input type="email" id="email" name="email" placeholder="jane.doe@yourcompany.com" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('email', $user->email ?? '') }}">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role:</label>
                    <select id="role" name="role" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Role</option>
                        @foreach($roles as $roleOption)
                            <option value="{{ $roleOption }}" {{ (old('role', $user->role ?? '') == $roleOption) ? 'selected' : '' }}>{{ $roleOption }}</option>
                        @endforeach
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                    <select id="status" name="status" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        @foreach(['Active', 'Inactive'] as $statusOption)
                            <option value="{{ $statusOption }}" {{ (old('status', $user->status ?? '') == $statusOption) ? 'selected' : '' }}>{{ $statusOption }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Account Security</h4>
                    <p class="text-sm text-gray-600 mb-3">@if(isset($user)) Leave password fields blank to keep current password. @else Set initial password for this user. @endif</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password:</label>
                            <input type="password" id="password" name="password" placeholder="********"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   @if(!isset($user)) required @endif>
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password:</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   @if(!isset($user)) required @endif>
                            @error('password_confirmation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('settings.show', ['tab' => 'users']) }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        @if(isset($user)) Save Changes @else Add User @endif
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>