<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Customer; // To identify the customer profile from the authenticated user
use App\Models\Equipment; // For new quote request form
use App\Models\User; // The model used for customer authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class CustomerQuoteController extends Controller
{
    /**
     * Display a listing of the customer's quotes with filters.
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

        $query = Quote::where('customer_id', $customerId)
                      ->with('customer'); // Eager load customer for display (though implicitly already the current customer)

        // Apply filters based on request (e.g., 'all', 'pending', 'accepted', 'expired', 'rejected')
        $filter = $request->input('filter', 'all'); // Default to 'all'

        switch ($filter) {
            case 'pending':
                $query->whereIn('status', ['Draft', 'Sent']);
                break;
            case 'accepted':
                $query->where('status', 'Accepted');
                break;
            case 'expired':
                $query->where('expiry_date', '<', $currentDate)
                      ->whereNotIn('status', ['Accepted', 'Rejected']); // Expired if not accepted/rejected explicitly
                break;
            case 'rejected':
                $query->where('status', 'Rejected');
                break;
            case 'all':
            default:
                // No specific status filter for 'all'
                break;
        }

        $quotes = $query->orderBy('quote_date', 'desc')->paginate(10);

        // Fetch counts for badges
        $allCount = Quote::where('customer_id', $customerId)->count();
        $pendingCount = Quote::where('customer_id', $customerId)
                               ->whereIn('status', ['Draft', 'Sent'])
                               ->count();
        $acceptedCount = Quote::where('customer_id', $customerId)
                               ->where('status', 'Accepted')
                               ->count();
        $expiredCount = Quote::where('customer_id', $customerId)
                               ->where('expiry_date', '<', $currentDate)
                               ->whereNotIn('status', ['Accepted', 'Rejected'])
                               ->count();
        $rejectedCount = Quote::where('customer_id', $customerId)
                               ->where('status', 'Rejected')
                               ->count();

        return view('customer.quotes.index', compact(
            'quotes',
            'filter', // Pass current filter for active tab styling
            'allCount',
            'pendingCount',
            'acceptedCount',
            'expiredCount',
            'rejectedCount',
            'customerProfile' // For sidebar if needed
        ));
    }

    /**
     * Show the form for customers to request a new quote.
     */
    public function create()
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

        $vendorId = $user->vendor_id;
        // Fetch equipment for dropdowns - potentially only 'Available' ones relevant for new quotes
        $availableEquipment = Equipment::where('vendor_id', $vendorId)
                                       ->where('status', 'Available') // Only available equipment
                                       ->get();

        return view('customer.quotes.create', compact('availableEquipment', 'customerProfile'));
    }

    /**
     * Store a newly created quote request from the customer.
     * Note: Status will be 'Draft' or 'Sent' for vendor to review.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('customer')->user();
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile) {
            return redirect()->back()->with('error', 'Customer profile not found. Please contact support.');
        }

        $vendorId = $user->vendor_id;

        $validatedData = $request->validate([
            'expiry_date' => ['nullable', 'date', 'after_or_equal:today'],
            'items' => ['required', 'array', 'min:1'], // Expecting an array of items
            'items.*.equipment_id' => ['required', 'exists:equipment,id,vendor_id,' . $vendorId],
            'items.*.rental_days' => ['required', 'integer', 'min:1'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'pickup_fee' => ['nullable', 'numeric', 'min:0'],
            'damage_waiver' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        // Calculate item totals and overall total amount (preliminary)
        $totalAmount = 0;
        $processedItems = [];
        foreach ($validatedData['items'] as $itemData) {
            $equipment = Equipment::find($itemData['equipment_id']);
            if (!$equipment || $equipment->vendor_id !== $vendorId) { // Double check equipment belongs to vendor
                return redirect()->back()->with('error', 'One or more selected equipment items are invalid or not available.')->withInput();
            }
            $itemTotalPrice = ($equipment->base_daily_rate * $itemData['rental_days']);
            $totalAmount += $itemTotalPrice;

            $processedItems[] = [
                'equipment_id' => $itemData['equipment_id'],
                'rental_days' => $itemData['rental_days'],
                'unit_price' => $equipment->base_daily_rate,
                'item_total_price' => $itemTotalPrice,
            ];
        }

        $totalAmount += ($validatedData['delivery_fee'] ?? 0);
        $totalAmount += ($validatedData['pickup_fee'] ?? 0);
        $totalAmount += ($validatedData['damage_waiver'] ?? 0);

        $quote = Quote::create([
            'vendor_id' => $vendorId,
            'customer_id' => $customerProfile->id, // Use the authenticated customer's profile ID
            'quote_date' => now()->toDateString(), // Set current date
            'expiry_date' => $validatedData['expiry_date'],
            'items' => $processedItems, // Store processed items
            'delivery_fee' => $validatedData['delivery_fee'] ?? 0,
            'pickup_fee' => $validatedData['pickup_fee'] ?? 0,
            'damage_waiver' => $validatedData['damage_waiver'] ?? 0,
            'total_amount' => $totalAmount, // This is the customer's estimated total
            'notes' => $validatedData['notes'],
            'status' => 'Pending', // New requests from customer typically start as 'Pending' for vendor review
            'linked_booking_id' => null,
            'linked_invoice_id' => null,
        ]);

        return redirect()->route('customer.quotes.index')->with('success', 'Your quote request has been submitted successfully! The vendor will review and send you a formal quote.');
    }

    /**
     * Display the specified quote for the customer.
     */
    public function show(Quote $quote)
    {
        $user = Auth::guard('customer')->user();
        if (!$user || $user->role !== 'customer') {
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed.');
        }

        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile || $quote->customer_id !== $customerProfile->id) {
            return redirect()->route('customer.quotes.index')->with('error', 'Unauthorized access to quote.');
        }
        
        $quote->load(['customer', 'linkedBooking', 'linkedInvoice']); // Eager load relationships

        // Fetch full equipment details for items in the quote (similar to vendor side)
        $quoteItemsWithDetails = collect($quote->items)->map(function ($item) {
            $equipment = Equipment::find($item['equipment_id']);
            $item['equipment_details'] = $equipment ? [
                'type' => $equipment->type,
                'size' => $equipment->size,
                'internal_id' => $equipment->internal_id,
            ] : null;
            $item['description'] = $equipment ? ($equipment->type . ' (' . $equipment->size . ')') : 'Unknown Equipment'; // For display consistency
            return $item;
        });


        return view('customer.quotes.show', compact('quote', 'quoteItemsWithDetails'));
    }

    /**
     * Handle customer accepting a quote.
     * This will typically set the quote status to 'Accepted' and notify the vendor.
     * The vendor's system (via their QuoteController or an automated process) would then create the Booking and Invoice.
     */
    public function accept(Quote $quote)
    {
        $user = Auth::guard('customer')->user();
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile || $quote->customer_id !== $customerProfile->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if (in_array($quote->status, ['Accepted', 'Rejected', 'Expired'])) {
            return redirect()->back()->with('info', 'This quote cannot be accepted.');
        }

        $quote->update(['status' => 'Accepted']);

        // In a real application, you would also:
        // 1. Notify the vendor (e.g., email, in-app notification).
        // 2. Potentially trigger a backend job for the vendor to convert this to a booking/invoice.
        //    (The vendor's system would then run its convertToBookingAndInvoice logic).

        return redirect()->route('customer.quotes.index')->with('success', 'Quote ' . $quote->id . ' accepted! The vendor has been notified and will proceed with your booking.');
    }

    /**
     * Handle customer rejecting a quote.
     */
    public function reject(Quote $quote)
    {
        $user = Auth::guard('customer')->user();
        $customerProfile = Customer::where('email', $user->email)->where('vendor_id', $user->vendor_id)->first();
        if (!$customerProfile || $quote->customer_id !== $customerProfile->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if (in_array($quote->status, ['Accepted', 'Rejected', 'Expired'])) {
            return redirect()->back()->with('info', 'This quote cannot be rejected.');
        }

        $quote->update(['status' => 'Rejected']);

        // In a real application, you would also:
        // 1. Notify the vendor.

        return redirect()->route('customer.quotes.index')->with('success', 'Quote ' . $quote->id . ' rejected. The vendor has been notified.');
    }
}