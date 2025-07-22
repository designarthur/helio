<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helly - Expense Details: {{ $expense->description }}</title>
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
    <div class="bg-white p-8 rounded-lg shadow-xl w-11/12 max-w-md relative max-h-[90vh] overflow-y-auto">
        <button onclick="window.history.back()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl font-bold">&times;</button>
        <h3 class="text-2xl font-bold text-chili-red mb-6 border-b pb-3 border-gray-200">
            Expense Details: <span id="detailExpenseId">{{ $expense->id }}</span>
        </h3>

        <div class="space-y-4 text-gray-700">
            <p><strong>Date:</strong> <span id="detailExpenseDate">{{ $expense->date->format('Y-m-d') }}</span></p>
            <p><strong>Description:</strong> <span id="detailExpenseDescription">{{ $expense->description }}</span></p>
            <p><strong>Category:</strong> <span id="detailExpenseCategory">{{ $expense->category }}</span></p>
            <p><strong>Amount:</strong> $<span id="detailExpenseAmount">{{ number_format($expense->amount, 2) }}</span></p>
            <p><strong>Paid To:</strong> <span id="detailExpenseVendorName">{{ $expense->vendor_name ?? 'N/A' }}</span></p>
            <p><strong>Notes:</strong> <span id="detailExpenseNotes">{{ $expense->notes ?? 'N/A' }}</span></p>
            {{-- Optional: Display receipt image/link here if you implement file storage --}}
            {{-- @if($expense->receipt_path)
            <p><strong>Receipt:</strong> <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="text-blue-600 hover:underline">View Receipt</a></p>
            @endif --}}
        </div>
        <div class="mt-6 flex justify-end">
            <a href="{{ route('expenses.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md font-semibold hover:bg-gray-400 transition-colors duration-200">Close</a>
        </div>
    </div>
</body>
</html>