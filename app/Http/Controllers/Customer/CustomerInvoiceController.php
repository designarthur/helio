<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Customer; // To identify the customer profile from the authenticated user
use App\Models\User;     // The model used for customer authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerInvoiceController extends Controller
{
    /**
     * Display a listing of the customer's invoices with filters.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed. Please log in as a customer.');
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();

        if (!$customerProfile) {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Customer profile not found. Please contact support.');
        }

        $customerId = $customerProfile->id;
        $currentDate = Carbon::now();

        $query = Invoice::where('customer_id', $customerId)
                        ->with('customer'); // Eager load customer for display (though implicitly already the current customer)

        // Apply filters based on request (e.g., 'all', 'pending', 'overdue', 'paid')
        $filter = $request->input('filter', 'all'); // Default to 'all'

        switch ($filter) {
            case 'pending':
                $query->where('balance_due', '>', 0)
                      ->where('due_date', '>=', $currentDate)
                      ->whereNotIn('status', ['Paid', 'Voided']);
                break;
            case 'overdue':
                $query->where('balance_due', '>', 0)
                      ->where('due_date', '<', $currentDate)
                      ->whereNotIn('status', ['Paid', 'Voided']);
                break;
            case 'paid':
                $query->where('status', 'Paid');
                break;
            case 'all':
            default:
                // No specific status filter for 'all'
                break;
        }

        $invoices = $query->orderBy('issue_date', 'desc')->paginate(10);

        // Fetch counts for badges
        $allCount = Invoice::where('customer_id', $customerId)->count();
        $pendingCount = Invoice::where('customer_id', $customerId)
                               ->where('balance_due', '>', 0)
                               ->where('due_date', '>=', $currentDate)
                               ->whereNotIn('status', ['Paid', 'Voided'])
                               ->count();
        $overdueCount = Invoice::where('customer_id', $customerId)
                               ->where('balance_due', '>', 0)
                               ->where('due_date', '<', $currentDate)
                               ->whereNotIn('status', ['Paid', 'Voided'])
                               ->count();
        $paidCount = Invoice::where('customer_id', $customerId)
                            ->where('status', 'Paid')
                            ->count();

        return view('customer.invoices.index', compact(
            'invoices',
            'filter', // Pass current filter for active tab styling
            'allCount',
            'pendingCount',
            'overdueCount',
            'paidCount',
            'customerProfile' // For sidebar if needed
        ));
    }

    /**
     * Display the specified invoice for the customer.
     */
    public function show(Invoice $invoice)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed.');
        }

        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id)
                                   ->first();
        if (!$customerProfile || $invoice->customer_id !== $customerProfile->id) {
            return redirect()->route('customer.invoices.index')->with('error', 'Unauthorized access to invoice.');
        }
        
        $invoice->load(['customer', 'linkedBooking', 'linkedQuote', 'payments']); // Eager load relationships

        return view('customer.invoices.show', compact('invoice'));
    }
}