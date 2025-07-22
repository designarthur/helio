<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice; // For linking payments to invoices
use App\Models\Customer; // For linking payments to customers
use App\Models\Vendor; // For authentication fallback
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
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

        $query = Payment::where('vendor_id', $vendorId)
                        ->with(['customer', 'invoice']); // Eager load customer and invoice for display

        // Apply filters (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('transaction_id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('method', 'like', '%' . $searchTerm . '%')
                  ->orWhere('notes', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('customer', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('invoice', function ($q) use ($searchTerm) {
                      $q->where('invoice_number', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(10);

        return view('vendor.payments.index', compact('payments', 'vendor'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request) // Can receive invoice_id from request
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to record payments.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        // Fetch all invoices for the vendor, order by balance_due or issue date
        $invoices = Invoice::where('vendor_id', $vendorId)
                           ->orderBy('due_date', 'asc')
                           ->get();

        // Pre-fill invoice_id and amount if passed from a quick action (e.g., from invoice show page)
        $selectedInvoice = null;
        if ($request->has('invoice_id')) {
            $selectedInvoice = Invoice::where('vendor_id', $vendorId)
                                      ->find($request->input('invoice_id'));
        }

        return view('vendor.payments.record-payment', compact('customers', 'invoices', 'selectedInvoice'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId],
            'invoice_id' => ['nullable', 'exists:invoices,id,vendor_id,' . $vendorId], // Optional link to invoice
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', Rule::in(['Credit Card', 'ACH', 'Check', 'Cash', 'Manual Mark as Paid'])],
            'transaction_id' => ['nullable', 'string', 'max:255', Rule::unique('payments')->where(function($query) use ($vendorId) {
                return $query->where('vendor_id', $vendorId);
            })], // Unique per vendor
            'notes' => ['nullable', 'string'],
        ]);

        // Create payment
        $payment = new Payment($validatedData);
        $payment->vendor_id = $vendorId;
        $payment->save();

        // Update linked invoice's balance due and status
        if ($payment->invoice_id) {
            $invoice = Invoice::find($payment->invoice_id);
            if ($invoice) {
                $invoice->balance_due -= $payment->amount;

                if ($invoice->balance_due <= 0) {
                    $invoice->balance_due = 0;
                    $invoice->status = 'Paid';
                } elseif ($invoice->balance_due < $invoice->total_amount) {
                    $invoice->status = 'Partially Paid';
                }
                $invoice->save();
            }
        }

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully!');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        if (!Auth::guard('vendor')->check() || $payment->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('payments.index')->with('error', 'Unauthorized access to payment.');
        }

        $payment->load(['customer', 'invoice']); // Eager load relationships

        return view('vendor.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     * (Often payments are not directly "edited" but rather reversed or credited,
     * but this method is kept for resource route completeness).
     */
    public function edit(Payment $payment)
    {
        if (!Auth::guard('vendor')->check() || $payment->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('payments.index')->with('error', 'Unauthorized access to payment.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        $invoices = Invoice::where('vendor_id', $vendorId)->get();

        return view('vendor.payments.record-payment', compact('payment', 'customers', 'invoices'));
    }

    /**
     * Update the specified payment in storage.
     * (Careful with updating payments, usually create a new reversing entry or credit memo).
     */
    public function update(Request $request, Payment $payment)
    {
        if (!Auth::guard('vendor')->check() || $payment->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $vendorId = Auth::guard('vendor')->id();

        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId],
            'invoice_id' => ['nullable', 'exists:invoices,id,vendor_id,' . $vendorId],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', Rule::in(['Credit Card', 'ACH', 'Check', 'Cash', 'Manual Mark as Paid'])],
            'transaction_id' => ['nullable', 'string', 'max:255', Rule::unique('payments')->ignore($payment->id, 'id')->where(function($query) use ($vendorId) {
                return $query->where('vendor_id', $vendorId);
            })],
            'notes' => ['nullable', 'string'],
        ]);

        // Store original amount and invoice ID for balance recalculation
        $originalAmount = $payment->amount;
        $originalInvoiceId = $payment->invoice_id;

        // Update payment
        $payment->update($validatedData);

        // Recalculate balances for affected invoices
        // If invoice changed, revert old invoice and update new one
        if ($originalInvoiceId !== $payment->invoice_id) {
            // Revert original invoice's balance
            if ($originalInvoiceId) {
                $oldInvoice = Invoice::find($originalInvoiceId);
                if ($oldInvoice) {
                    $oldInvoice->balance_due += $originalAmount;
                    $oldInvoice->status = ($oldInvoice->balance_due >= $oldInvoice->total_amount) ? 'Sent' : 'Partially Paid';
                    $oldInvoice->save();
                }
            }
            // Update new invoice's balance with the new amount
            if ($payment->invoice_id) {
                $newInvoice = Invoice::find($payment->invoice_id);
                if ($newInvoice) {
                    $newInvoice->balance_due -= $payment->amount;
                    $newInvoice->status = ($newInvoice->balance_due <= 0) ? 'Paid' : 'Partially Paid';
                    $newInvoice->save();
                }
            }
        } else { // Same invoice, just amount changed
            if ($payment->invoice_id) {
                $invoice = Invoice::find($payment->invoice_id);
                if ($invoice) {
                    // Adjust balance due by the difference
                    $invoice->balance_due += ($originalAmount - $payment->amount);
                    $invoice->status = ($invoice->balance_due <= 0) ? 'Paid' : 'Partially Paid';
                    $invoice->save();
                }
            }
        }

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully!');
    }

    /**
     * Remove the specified payment from storage.
     * (Usually, payments are not deleted but reversed via credit notes for auditability).
     */
    public function destroy(Payment $payment)
    {
        if (!Auth::guard('vendor')->check() || $payment->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Revert linked invoice's balance due and status
        if ($payment->invoice_id) {
            $invoice = Invoice::find($payment->invoice_id);
            if ($invoice) {
                $invoice->balance_due += $payment->amount;
                $invoice->status = ($invoice->balance_due >= $invoice->total_amount) ? 'Sent' : 'Partially Paid'; // Or 'Overdue' if due_date passed
                $invoice->save();
            }
        }

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully!');
    }
}