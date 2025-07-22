<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Vendor; // For authentication fallback
use App\Models\Customer; // For dropdowns/relationships
use App\Models\Booking; // For linking manual invoices to bookings
use App\Models\Payment; // For marking as paid / recording payments
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            $vendor = Vendor::first();
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;

        $query = Invoice::where('vendor_id', $vendorId)
                        ->with('customer'); // Eager load customer for display

        // Apply filters (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('notes', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('customer', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        if ($request->has('status_filter') && $request->input('status_filter') !== null) {
            $query->where('status', $request->input('status_filter'));
        }

        $invoices = $query->orderBy('issue_date', 'desc')->paginate(10);

        return view('vendor.invoices.index', compact('invoices', 'vendor'));
    }

    /**
     * Show the form for creating a new invoice.
     * (Typically used for manual invoices not linked to bookings/quotes)
     */
    public function create()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to create invoices.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        $bookings = Booking::where('vendor_id', $vendorId)->get(); // For linking manual invoices

        return view('vendor.invoices.create-edit', compact('customers', 'bookings'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'items' => ['required', 'array', 'min:1'], // Array of invoice line items
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(['Draft', 'Sent', 'Partially Paid', 'Paid', 'Overdue', 'Voided'])],
            'linked_booking_id' => ['nullable', 'exists:bookings,id,vendor_id,' . $vendorId], // Optional link to a booking
        ]);

        $totalAmount = 0;
        foreach ($validatedData['items'] as $item) {
            $totalAmount += $item['amount'];
        }

        // Set initial balance due based on total amount and status
        $balanceDue = $totalAmount;
        if (in_array($validatedData['status'], ['Paid', 'Voided'])) {
            $balanceDue = 0; // If marked paid/voided on creation, balance is 0
        } elseif ($validatedData['status'] === 'Partially Paid') {
            // For partially paid on creation, user would typically input initial paid amount, not just status
            // For now, let's assume it's still total amount, needs manual payment record later
        }

        // Generate a simple invoice number
        $invoiceNumber = 'INV-' . (Invoice::where('vendor_id', $vendorId)->count() + 1 + mt_rand(100, 999)); // Add random part to avoid conflict if multiple vendors add at same time

        $invoice = Invoice::create([
            'vendor_id' => $vendorId,
            'customer_id' => $validatedData['customer_id'],
            'invoice_number' => $invoiceNumber,
            'issue_date' => $validatedData['issue_date'],
            'due_date' => $validatedData['due_date'],
            'items' => $validatedData['items'],
            'total_amount' => $totalAmount,
            'balance_due' => $balanceDue,
            'status' => $validatedData['status'],
            'notes' => $validatedData['notes'],
            'linked_booking_id' => $validatedData['linked_booking_id'] ?? null,
            // linked_quote_id will be handled by QuoteController's convertToBookingAndInvoice method
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice ' . $invoice->invoice_number . ' created successfully!');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        if (!Auth::guard('vendor')->check() || $invoice->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('invoices.index')->with('error', 'Unauthorized access to invoice.');
        }

        $invoice->load(['customer', 'linkedBooking', 'linkedQuote', 'payments']); // Eager load relationships

        return view('vendor.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        if (!Auth::guard('vendor')->check() || $invoice->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('invoices.index')->with('error', 'Unauthorized access to invoice.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        $bookings = Booking::where('vendor_id', $vendorId)->get();

        return view('vendor.invoices.create-edit', compact('invoice', 'customers', 'bookings'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if (!Auth::guard('vendor')->check() || $invoice->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $vendorId = Auth::guard('vendor')->id();

        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'items' => ['required', 'array', 'min:1'], // Array of invoice line items
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(['Draft', 'Sent', 'Partially Paid', 'Paid', 'Overdue', 'Voided'])],
            'linked_booking_id' => ['nullable', 'exists:bookings,id,vendor_id,' . $vendorId],
        ]);

        $totalAmount = 0;
        foreach ($validatedData['items'] as $item) {
            $totalAmount += $item['amount'];
        }

        // Calculate new balance_due based on current payments and new total amount
        $totalPaid = $invoice->payments->sum('amount');
        $newBalanceDue = $totalAmount - $totalPaid;

        // Ensure status reflects balance due
        if ($newBalanceDue <= 0) {
            $validatedData['status'] = 'Paid';
            $newBalanceDue = 0;
        } elseif ($newBalanceDue < $totalAmount && $totalPaid > 0) {
            $validatedData['status'] = 'Partially Paid';
        } elseif ($newBalanceDue == $totalAmount && $totalPaid == 0) {
            // Keep original status if it was sent/overdue etc. and no payments were applied
            // Or set to 'Sent' if it was a draft
            if ($validatedData['status'] === 'Draft') {
                $validatedData['status'] = 'Sent';
            }
        }
        // If status was manually set to 'Overdue', keep it if balance > 0 and due_date passed

        $validatedData['total_amount'] = $totalAmount;
        $validatedData['balance_due'] = $newBalanceDue;

        // Set nullable fields to null if they become empty strings from the form
        foreach (['notes', 'linked_booking_id'] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }

        $invoice->update($validatedData);

        return redirect()->route('invoices.index')->with('success', 'Invoice ' . $invoice->invoice_number . ' updated successfully!');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        if (!Auth::guard('vendor')->check() || $invoice->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Before deleting invoice, potentially un-link it from quotes
        if ($invoice->linkedQuote) {
            $invoice->linkedQuote->update(['linked_invoice_id' => null, 'status' => 'Rejected']); // Revert quote status or mark rejected
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice ' . $invoice->invoice_number . ' deleted successfully!');
    }

    /**
     * Mark an invoice as paid (full payment, without payment details).
     * This is a quick action, payment details would be in Payments module.
     */
    public function markAsPaid(Invoice $invoice)
    {
        if (!Auth::guard('vendor')->check() || $invoice->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($invoice->balance_due <= 0) {
            return redirect()->back()->with('info', 'Invoice is already fully paid or voided.');
        }

        // Record a payment for the full balance due
        Payment::create([
            'vendor_id' => $invoice->vendor_id,
            'customer_id' => $invoice->customer_id,
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => $invoice->balance_due,
            'method' => 'Manual Mark as Paid', // Or "Online Payment" if coming from gateway
            'notes' => 'Marked as paid from invoice details.',
        ]);

        // Update invoice balance and status
        $invoice->update([
            'balance_due' => 0,
            'status' => 'Paid',
        ]);

        return redirect()->back()->with('success', 'Invoice ' . $invoice->invoice_number . ' marked as paid successfully!');
    }
}