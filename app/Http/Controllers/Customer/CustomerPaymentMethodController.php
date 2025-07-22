<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerPaymentMethod;
use App\Models\Customer; // To identify the customer profile from the authenticated user
use App\Models\Invoice; // To potentially pre-fill amount if coming from invoice page
use App\Models\User;     // The model used for customer authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str; // For generating dummy tokens

class CustomerPaymentMethodController extends Controller
{
    /**
     * Display a listing of the customer's payment methods.
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
        $vendorId = $user->vendor_id;

        $paymentMethods = CustomerPaymentMethod::where('customer_id', $customerId)
                                               ->where('vendor_id', $vendorId) // Ensure it belongs to this vendor
                                               ->orderByDesc('is_default') // Default first
                                               ->get();

        // Check if an invoice needs to be paid (passed via query parameters)
        $invoiceToPay = null;
        $amountToPay = null;
        if ($request->has('invoice_id') && $request->has('amount')) {
            $invoiceToPay = Invoice::where('customer_id', $customerId)
                                   ->where('vendor_id', $vendorId)
                                   ->find($request->input('invoice_id'));
            if ($invoiceToPay && $invoiceToPay->balance_due > 0) {
                $amountToPay = $invoiceToPay->balance_due;
            } else {
                 return redirect()->route('customer.invoices.index')->with('info', 'Invoice not found or already paid.');
            }
        }


        return view('customer.payment_methods.index', compact(
            'paymentMethods',
            'customerProfile',
            'invoiceToPay', // Pass invoice object if present
            'amountToPay'   // Pass amount if present
        ));
    }

    /**
     * Show the form for customers to add/edit a payment method.
     */
    public function create(Request $request)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed. Please log in as a customer.');
        }
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile) {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Customer profile not found. Please contact support.');
        }

        // Pass any invoice_id and amount to pre-fill if coming from a "Pay Now" link
        $invoiceId = $request->input('invoice_id');
        $amount = $request->input('amount');

        return view('customer.payment_methods.create-edit', compact('customerProfile', 'invoiceId', 'amount'));
    }

    /**
     * Store a newly created payment method for the customer.
     * This is conceptual; in production, sensitive data is sent to a gateway, which returns a token.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('customer')->user();
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile) {
            return redirect()->back()->with('error', 'Customer profile not found. Please contact support.');
        }

        $vendorId = $user->vendor_id;

        // Validation for non-sensitive data. Actual card validation happens on the client-side
        // and with the payment gateway when the token is generated.
        $validatedData = $request->validate([
            'nickname' => ['nullable', 'string', 'max:255'],
            'card_type' => ['required', 'string', Rule::in(['Visa', 'Mastercard', 'Amex', 'Discover'])], // Based on client-side detection
            'last_four' => ['required', 'string', 'digits:4'],
            'expiry_month' => ['required', 'string', 'digits:2', 'between:1,12'],
            'expiry_year' => ['required', 'string', 'digits:4', 'date_format:Y', 'after_or_equal:' . date('Y')], // Ensure year is YYYY and not past
            // 'token' => ['required', 'string', 'unique:customer_payment_methods,token,NULL,id,vendor_id,' . $vendorId], // Payment gateway token
            'is_default' => ['boolean'],
            // These would be present in the request conceptually from payment gateway forms:
            // 'card_holder_name' => 'nullable|string',
            // 'billing_zip' => 'nullable|string',
        ]);

        // Simulate tokenization from a payment gateway response
        $validatedData['token'] = 'tok_' . Str::random(30); // Dummy token for demonstration

        // Handle setting as default
        if (isset($validatedData['is_default']) && $validatedData['is_default']) {
            CustomerPaymentMethod::where('customer_id', $customerId)->where('vendor_id', $vendorId)->update(['is_default' => false]);
        } else {
            // If no default is set yet, make this the default
            if (CustomerPaymentMethod::where('customer_id', $customerId)->where('vendor_id', $vendorId)->count() === 0) {
                 $validatedData['is_default'] = true;
            }
        }
        
        $paymentMethod = new CustomerPaymentMethod($validatedData);
        $paymentMethod->customer_id = $customerId;
        $paymentMethod->vendor_id = $vendorId;
        $paymentMethod->save();

        // If amount and invoice_id were passed, proceed to record a payment
        if ($request->has('invoice_id') && $request->has('amount')) {
            $invoiceId = $request->input('invoice_id');
            $amountToPay = $request->input('amount');

            $invoice = Invoice::where('id', $invoiceId)
                              ->where('customer_id', $customerId)
                              ->where('vendor_id', $vendorId)
                              ->first();

            if ($invoice && $invoice->balance_due > 0) {
                // We'll create a payment record here using the existing Payment model
                \App\Models\Payment::create([
                    'vendor_id' => $vendorId,
                    'customer_id' => $customerId,
                    'invoice_id' => $invoice->id,
                    'payment_date' => Carbon::now()->toDateString(),
                    'amount' => $amountToPay,
                    'method' => 'Credit Card (via portal)', // Assuming online payment
                    'transaction_id' => 'tx_' . Str::random(20), // Dummy transaction ID
                    'notes' => 'Payment made via customer portal using new card.',
                ]);

                // Update invoice balance and status
                $invoice->balance_due -= $amountToPay;
                if ($invoice->balance_due <= 0) {
                    $invoice->balance_due = 0;
                    $invoice->status = 'Paid';
                } elseif ($invoice->balance_due < $invoice->total_amount) {
                    $invoice->status = 'Partially Paid';
                }
                $invoice->save();

                return redirect()->route('customer.invoices.show', $invoice->id)->with('success', 'Payment method added and invoice ' . $invoice->invoice_number . ' paid successfully!');
            }
        }

        return redirect()->route('customer.payment_methods.index')->with('success', 'Payment method added successfully!');
    }

    /**
     * Show the form for editing the specified payment method.
     * (Direct editing of sensitive financial info is often avoided; new method or credit/refund is preferred).
     */
    public function edit(CustomerPaymentMethod $paymentMethod)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed.');
        }

        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile || $paymentMethod->customer_id !== $customerProfile->id) {
            return redirect()->route('customer.payment_methods.index')->with('error', 'Unauthorized access to payment method.');
        }

        // Note: For editing, you might not be able to re-display the full card number.
        // The form would typically only allow updating nickname, expiry, and default status.
        return view('customer.payment_methods.create-edit', compact('paymentMethod', 'customerProfile'));
    }

    /**
     * Update the specified payment method in storage.
     */
    public function update(Request $request, CustomerPaymentMethod $paymentMethod)
    {
        $user = Auth::guard('customer')->user();
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile || $paymentMethod->customer_id !== $customerProfile->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $vendorId = $user->vendor_id;

        $validatedData = $request->validate([
            'nickname' => ['nullable', 'string', 'max:255'],
            'expiry_month' => ['required', 'string', 'digits:2', 'between:1,12'], // Only allowing expiry update
            'expiry_year' => ['required', 'string', 'digits:4', 'date_format:Y', 'after_or_equal:' . date('Y')],
            'is_default' => ['boolean'],
            // No direct update for token, card_type, last_four as they are from gateway
        ]);

        // Handle setting as default
        if (isset($validatedData['is_default']) && $validatedData['is_default']) {
            CustomerPaymentMethod::where('customer_id', $customerId)->where('vendor_id', $vendorId)->update(['is_default' => false]);
        }
        
        $paymentMethod->update($validatedData);

        return redirect()->route('customer.payment_methods.index')->with('success', 'Payment method updated successfully!');
    }

    /**
     * Remove the specified payment method from storage.
     */
    public function destroy(CustomerPaymentMethod $paymentMethod)
    {
        $user = Auth::guard('customer')->user();
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile || $paymentMethod->customer_id !== $customerProfile->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // If it was the default, try to set another as default (or clear default)
        if ($paymentMethod->is_default) {
            $newDefault = CustomerPaymentMethod::where('customer_id', $customerProfile->id)
                                               ->where('vendor_id', $user->vendor_id)
                                               ->where('id', '!=', $paymentMethod->id)
                                               ->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        // In a real system, you'd also communicate with the payment gateway to "vault" or "delete" the token.
        $paymentMethod->delete();

        return redirect()->route('customer.payment_methods.index')->with('success', 'Payment method removed successfully!');
    }
}