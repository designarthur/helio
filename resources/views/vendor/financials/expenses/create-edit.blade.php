<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - @if(isset($expense)) Edit Expense: {{ $expense->description }} @else Add New Expense @endif</title>
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
            @if(isset($expense)) Edit Expense: {{ $expense->description }} @else Add New Expense @endif
        </h3>

        <form id="expenseForm" method="POST" action="@if(isset($expense)) {{ route('expenses.update', $expense->id) }} @else {{ route('expenses.store') }} @endif">
            @csrf {{-- CSRF token for security --}}
            @if(isset($expense)) @method('PUT') @endif {{-- Method spoofing for UPDATE request --}}

            <div class="space-y-4">
                <input type="hidden" id="expenseId" name="id" value="{{ $expense->id ?? '' }}">

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date:</label>
                    <input type="date" id="date" name="date" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('date', $expense->date ? $expense->date->format('Y-m-d') : '') }}">
                    @error('date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                    <input type="text" id="description" name="description" placeholder="e.g., Fuel for Truck 101" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('description', $expense->description ?? '') }}">
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category:</label>
                    <select id="category" name="category" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red">
                        <option value="">Select Category</option>
                        <option value="Fuel" {{ (old('category', $expense->category ?? '') == 'Fuel') ? 'selected' : '' }}>Fuel</option>
                        <option value="Maintenance" {{ (old('category', $expense->category ?? '') == 'Maintenance') ? 'selected' : '' }}>Maintenance</option>
                        <option value="Salaries" {{ (old('category', $expense->category ?? '') == 'Salaries') ? 'selected' : '' }}>Salaries</option>
                        <option value="Office Supplies" {{ (old('category', $expense->category ?? '') == 'Office Supplies') ? 'selected' : '' }}>Office Supplies</option>
                        <option value="Marketing" {{ (old('category', $expense->category ?? '') == 'Marketing') ? 'selected' : '' }}>Marketing</option>
                        <option value="Utilities" {{ (old('category', $expense->category ?? '') == 'Utilities') ? 'selected' : '' }}>Utilities</option>
                        <option value="Other" {{ (old('category', $expense->category ?? '') == 'Other') ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount ($):</label>
                    <input type="number" id="amount" name="amount" min="0.01" step="0.01" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('amount', $expense->amount ?? '') }}">
                    @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="vendor_name" class="block text-sm font-medium text-gray-700 mb-1">Paid To (Vendor Name, Optional):</label>
                    <input type="text" id="vendor_name" name="vendor_name" placeholder="e.g., Shell Gas Station"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red"
                           value="{{ old('vendor_name', $expense->vendor_name ?? '') }}">
                    @error('vendor_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional):</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-chili-red focus:border-chili-red resize-y">
                        {{ old('notes', $expense->notes ?? '') }}
                    </textarea>
                    @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                {{-- Optional: File upload for receipts --}}
                {{-- <div>
                    <label for="receipt_path" class="block text-sm font-medium text-gray-700 mb-1">Receipt (Optional):</label>
                    <input type="file" id="receipt_path" name="receipt_path" accept="image/*,application/pdf"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-chili-red file:text-white hover:file:bg-tangelo"/>
                    @error('receipt_path')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div> --}}
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('expenses.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-chili-red text-white rounded-md font-semibold hover:bg-tangelo transition-colors duration-200">
                    @if(isset($expense)) Save Changes @else Add Expense @endif
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set current date for new expenses if the field is empty
            const dateInput = document.getElementById('date');
            @if(!isset($expense)) // Only for create mode
                if (!dateInput.value) {
                    dateInput.valueAsDate = new Date();
                }
            @endif
        });
    </script>
</body>
</html>