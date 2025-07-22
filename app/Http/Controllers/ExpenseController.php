<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Vendor; // For authentication fallback
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            $vendor = Vendor::first(); // Fallback for dev if not authenticated
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;

        $query = Expense::where('vendor_id', $vendorId);

        // Apply filters (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('vendor_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('notes', 'like', '%' . $searchTerm . '%');
            });
        }
        if ($request->has('category_filter') && $request->input('category_filter') !== null) {
            $query->where('category', $request->input('category_filter'));
        }

        $expenses = $query->orderBy('date', 'desc')->paginate(10);

        return view('vendor.financials.expenses.index', compact('expenses', 'vendor'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to record expenses.');
        }

        return view('vendor.financials.expenses.create-edit');
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['Fuel', 'Maintenance', 'Salaries', 'Office Supplies', 'Marketing', 'Utilities', 'Other'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            // 'receipt_path' => ['nullable', 'image', 'max:2048'], // For file uploads
        ]);

        // Handle file upload if you implement it
        // if ($request->hasFile('receipt_path')) {
        //     $validatedData['receipt_path'] = $request->file('receipt_path')->store('receipts', 'public');
        // }

        // Set nullable fields to null if they become empty strings from the form
        foreach (['vendor_name', 'notes'] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }

        $expense = new Expense($validatedData);
        $expense->vendor_id = $vendorId;
        $expense->save();

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully!');
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        if (!Auth::guard('vendor')->check() || $expense->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('expenses.index')->with('error', 'Unauthorized access to expense.');
        }

        return view('vendor.financials.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        if (!Auth::guard('vendor')->check() || $expense->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('expenses.index')->with('error', 'Unauthorized access to expense.');
        }

        return view('vendor.financials.expenses.create-edit', compact('expense'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        if (!Auth::guard('vendor')->check() || $expense->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['Fuel', 'Maintenance', 'Salaries', 'Office Supplies', 'Marketing', 'Utilities', 'Other'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            // 'receipt_path' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle file upload if you implement it (and update path)
        // if ($request->hasFile('receipt_path')) {
        //     // Delete old receipt if exists
        //     if ($expense->receipt_path) {
        //         Storage::disk('public')->delete($expense->receipt_path);
        //     }
        //     $validatedData['receipt_path'] = $request->file('receipt_path')->store('receipts', 'public');
        // }

        // Set nullable fields to null if they become empty strings from the form
        foreach (['vendor_name', 'notes'] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }
        
        $expense->update($validatedData);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        if (!Auth::guard('vendor')->check() || $expense->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Optional: Delete associated receipt file if implementing file storage
        // if ($expense->receipt_path) {
        //     Storage::disk('public')->delete($expense->receipt_path);
        // }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }
}