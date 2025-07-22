<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($driver)) Edit Driver: {{ $driver->name }} @else Add New Driver @endif</title>
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
            @if(isset($driver)) Edit Driver: {{ $driver->name }} @else Add New Driver @endif
        </h3>

        <form id="driverForm" method="POST" action="@if(isset($driver)) {{ route('drivers.update', $driver->id) }} @else {{ route('drivers.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($driver)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="driverId" name="id" value="{{ $driver->id ?? '' }}">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name:</label>
                    <input type="text" id="name" name="name" placeholder="Michael Scott" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('name', $driver->name ?? '') }}">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                    <input type="email" id="email" name="email" placeholder="michael.scott@example.com" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('email', $driver->email ?? '') }}">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone:</label>
                    <input type="tel" id="phone" name="phone" placeholder="(111) 222-3333"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('phone', $driver->phone ?? '') }}">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">License Number:</label>
                    <input type="text" id="license_number" name="license_number" placeholder="MS123456"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('license_number', $driver->license_number ?? '') }}">
                    @error('license_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="license_expiry" class="block text-sm font-medium text-gray-700 mb-1">License Expiry Date:</label>
                    <input type="date" id="license_expiry" name="license_expiry"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('license_expiry', $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : '') }}">
                    @error('license_expiry')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="cdl_class" class="block text-sm font-medium text-gray-700 mb-1">CDL Class (Optional):</label>
                    <input type="text" id="cdl_class" name="cdl_class" placeholder="Class A, B, C"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('cdl_class', $driver->cdl_class ?? '') }}">
                    @error('cdl_class')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                    <select id="status" name="status" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="Active" {{ (old('status', $driver->status ?? '') == 'Active') ? 'selected' : '' }}>Active</option>
                        <option value="On Leave" {{ (old('status', $driver->status ?? '') == 'On Leave') ? 'selected' : '' }}>On Leave</option>
                        <option value="Inactive" {{ (old('status', $driver->status ?? '') == 'Inactive') ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="assigned_vehicle" class="block text-sm font-medium text-gray-700 mb-1">Assigned Vehicle:</label>
                    <input type="text" id="assigned_vehicle" name="assigned_vehicle" placeholder="Truck 101"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('assigned_vehicle', $driver->assigned_vehicle ?? '') }}">
                    @error('assigned_vehicle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="certifications" class="block text-sm font-medium text-gray-700 mb-1">Certifications (comma-separated):</label>
                    <textarea id="certifications" name="certifications" rows="2" placeholder="e.g., Forklift, HazMat, OSHA-10"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('certifications', is_array($driver->certifications ?? null) ? implode(', ', $driver->certifications) : ($driver->certifications ?? '')) }}
                    </textarea>
                    @error('certifications')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2 border-t border-gray-200 pt-4 mt-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Account Security</h4>
                    <p class="text-sm text-gray-600 mb-3">@if(isset($driver)) Leave password fields blank to keep current password. @else Set initial password for this driver. @endif</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password:</label>
                            <input type="password" id="password" name="password" placeholder="********"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   @if(!isset($driver)) required @endif>
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password:</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="********"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                                   @if(!isset($driver)) required @endif>
                            @error('password_confirmation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="driver_notes" class="block text-sm font-medium text-gray-700 mb-1">Driver Notes (Internal):</label>
                    <textarea id="driver_notes" name="driver_notes" rows="3" placeholder="Notes about driver preferences, skills, or warnings."
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('driver_notes', $driver->driver_notes ?? '') }}
                    </textarea>
                    @error('driver_notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-6">
                    <a href="{{ route('drivers.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                        @if(isset($driver)) Save Changes @else Add Driver @endif
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>