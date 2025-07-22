<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Customer; // For dropdowns
use App\Models\Equipment; // For dropdowns and price calculation
use App\Models\Booking; // For converting quote to booking
use App\Models\Invoice; // For converting quote to invoice
use App\Models\Vendor; // For authentication fallback
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // For validation rules

class QuoteController extends Controller
{
    /**
     * Display a listing of the quotes.
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

        $query = Quote::where('vendor_id', $vendorId)
                      ->with('customer'); // Eager load customer for display

        // Apply filters (from your HTML filters)
        if ($request->has('search') && $request->input('search') !== null) {
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('notes', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('customer', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        if ($request->has('status_filter') && $request->input('status_filter') !== null) {
            $query->where('status', $request->input('status_filter'));
        }

        $quotes = $query->orderBy('quote_date', 'desc')->paginate(10);

        return view('vendor.quotes.index', compact('quotes', 'vendor'));
    }

    /**
     * Show the form for creating a new quote.
     */
    public function create()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in as a vendor to create quotes.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        $equipment = Equipment::where('vendor_id', $vendorId)->get(); // All equipment for dropdown

        return view('vendor.quotes.create-edit', compact('customers', 'equipment'));
    }

    /**
     * Store a newly created quote in storage.
     */
    public function store(Request $request)
    {
        $vendorId = Auth::guard('vendor')->id();
        if (!$vendorId) {
            return redirect()->back()->with('error', 'Vendor not authenticated.');
        }

        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:today'],
            'items' => ['required', 'array', 'min:1'], // Expecting an array of items
            'items.*.equipment_id' => ['required', 'exists:equipment,id,vendor_id,' . $vendorId],
            'items.*.rental_days' => ['required', 'integer', 'min:1'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'pickup_fee' => ['nullable', 'numeric', 'min:0'],
            'damage_waiver' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'])],
        ]);

        // Calculate item totals and overall total amount
        $totalAmount = 0;
        $processedItems = [];
        foreach ($validatedData['items'] as $itemData) {
            $equipment = Equipment::find($itemData['equipment_id']);
            if (!$equipment) {
                return redirect()->back()->with('error', 'One or more selected equipment items are invalid.')->withInput();
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

        // Create the quote
        $quote = Quote::create([
            'vendor_id' => $vendorId,
            'customer_id' => $validatedData['customer_id'],
            'quote_date' => now()->toDateString(), // Set current date
            'expiry_date' => $validatedData['expiry_date'],
            'items' => $processedItems, // Store processed items
            'delivery_fee' => $validatedData['delivery_fee'] ?? 0,
            'pickup_fee' => $validatedData['pickup_fee'] ?? 0,
            'damage_waiver' => $validatedData['damage_waiver'] ?? 0,
            'total_amount' => $totalAmount,
            'notes' => $validatedData['notes'],
            'status' => $validatedData['status'],
        ]);

        return redirect()->route('quotes.index')->with('success', 'Quote created successfully!');
    }

    /**
     * Display the specified quote.
     */
    public function show(Quote $quote)
    {
        if (!Auth::guard('vendor')->check() || $quote->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('quotes.index')->with('error', 'Unauthorized access to quote.');
        }

        $quote->load(['customer']); // Eager load customer

        // Fetch full equipment details for items in the quote
        $quoteItemsWithDetails = collect($quote->items)->map(function ($item) {
            $equipment = Equipment::find($item['equipment_id']);
            $item['equipment_details'] = $equipment ? [
                'type' => $equipment->type,
                'size' => $equipment->size,
                'internal_id' => $equipment->internal_id,
            ] : null;
            return $item;
        });

        return view('vendor.quotes.show', compact('quote', 'quoteItemsWithDetails'));
    }

    /**
     * Show the form for editing the specified quote.
     */
    public function edit(Quote $quote)
    {
        if (!Auth::guard('vendor')->check() || $quote->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->route('quotes.index')->with('error', 'Unauthorized access to quote.');
        }

        $vendorId = Auth::guard('vendor')->id();
        $customers = Customer::where('vendor_id', $vendorId)->get();
        $equipment = Equipment::where('vendor_id', $vendorId)->get();

        return view('vendor.quotes.create-edit', compact('quote', 'customers', 'equipment'));
    }

    /**
     * Update the specified quote in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        if (!Auth::guard('vendor')->check() || $quote->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $vendorId = Auth::guard('vendor')->id();

        $validatedData = $request->validate([
            'customer_id' => ['required', 'exists:customers,id,vendor_id,' . $vendorId],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.equipment_id' => ['required', 'exists:equipment,id,vendor_id,' . $vendorId],
            'items.*.rental_days' => ['required', 'integer', 'min:1'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'pickup_fee' => ['nullable', 'numeric', 'min:0'],
            'damage_waiver' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::in(['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'])],
        ]);

        $totalAmount = 0;
        $processedItems = [];
        foreach ($validatedData['items'] as $itemData) {
            $equipment = Equipment::find($itemData['equipment_id']);
            if (!$equipment) {
                return redirect()->back()->with('error', 'One or more selected equipment items are invalid.')->withInput();
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

        $quote->update([
            'customer_id' => $validatedData['customer_id'],
            'expiry_date' => $validatedData['expiry_date'],
            'items' => $processedItems,
            'delivery_fee' => $validatedData['delivery_fee'] ?? 0,
            'pickup_fee' => $validatedData['pickup_fee'] ?? 0,
            'damage_waiver' => $validatedData['damage_waiver'] ?? 0,
            'total_amount' => $totalAmount,
            'notes' => $validatedData['notes'],
            'status' => $validatedData['status'],
        ]);

        return redirect()->route('quotes.index')->with('success', 'Quote updated successfully!');
    }

    /**
     * Remove the specified quote from storage.
     */
    public function destroy(Quote $quote)
    {
        if (!Auth::guard('vendor')->check() || $quote->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $quote->delete();

        return redirect()->route('quotes.index')->with('success', 'Quote deleted successfully!');
    }

    /**
     * Helper method to calculate quote total (can be used by AJAX if needed)
     */
    public function calculateQuoteTotal(Request $request)
    {
        // This method calculates the total price of a quote based on provided items and fees
        $request->validate([
            'items' => ['array', 'nullable'], // Can be empty or null initially
            'items.*.equipment_id' => ['required_with:items', 'exists:equipment,id'],
            'items.*.rental_days' => ['required_with:items', 'integer', 'min:1'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'pickup_fee' => ['nullable', 'numeric', 'min:0'],
            'damage_waiver' => ['nullable', 'numeric', 'min:0'],
        ]);

        $totalAmount = 0;
        $itemsData = $request->input('items', []);

        foreach ($itemsData as $itemData) {
            $equipment = Equipment::find($itemData['equipment_id']);
            if ($equipment) {
                $totalAmount += ($equipment->base_daily_rate * $itemData['rental_days']);
            }
        }

        $totalAmount += ($request->input('delivery_fee') ?? 0);
        $totalAmount += ($request->input('pickup_fee') ?? 0);
        $totalAmount += ($request->input('damage_waiver') ?? 0);

        return response()->json(['total_amount' => round($totalAmount, 2)]);
    }


    /**
     * Converts a quote to a booking and an invoice.
     * This is a simplified concept; a real implementation would be more complex with business logic and status updates.
     */
    public function convertToBookingAndInvoice(Request $request, Quote $quote)
    {
        if (!Auth::guard('vendor')->check() || $quote->vendor_id !== Auth::guard('vendor')->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($quote->status === 'Accepted' || $quote->linked_booking_id || $quote->linked_invoice_id) {
            return redirect()->back()->with('error', 'This quote has already been accepted or converted.');
        }

        // --- 1. Create Booking from Quote ---
        // Assuming the first item in the quote is the primary equipment for the booking
        $firstQuoteItem = collect($quote->items)->first();
        if (!$firstQuoteItem) {
            return redirect()->back()->with('error', 'Quote has no items to create a booking.');
        }

        $equipment = Equipment::find($firstQuoteItem['equipment_id']);
        if (!$equipment) {
            return redirect()->back()->with('error', 'Equipment from quote not found for booking creation.');
        }

        // Default booking dates for simplicity, adjust as per quote's items
        $rentalStartDate = $quote->quote_date;
        $rentalEndDate = $quote->expiry_date ?: now()->addDays($firstQuoteItem['rental_days']);

        $booking = Booking::create([
            'vendor_id' => $quote->vendor_id,
            'customer_id' => $quote->customer_id,
            'equipment_id' => $firstQuoteItem['equipment_id'],
            'rental_start_date' => $rentalStartDate,
            'rental_end_date' => $rentalEndDate,
            'delivery_address' => $quote->customer->billing_address ?? 'N/A', // Use customer's billing address
            'pickup_address' => $quote->customer->billing_address ?? 'N/A', // Assuming same as delivery for now
            'status' => 'Confirmed', // Set status to confirmed upon conversion
            'total_price' => $quote->total_amount, // Use quote total price
            'booking_notes' => 'Generated from Quote ' . $quote->id . '. ' . ($quote->notes ?? ''),
            'driver_id' => null, // Initially unassigned
            // Populate type-specific booking details from quote, if available/relevant
            // E.g., 'estimated_tonnage' => $quote->estimated_tonnage_from_quote_if_exists,
        ]);

        // Update equipment status if it's now booked
        $equipment->update(['status' => 'On Rent']);


        // --- 2. Create Invoice from Quote ---
        $invoiceItems = collect($quote->items)->map(function ($item) use ($equipment) {
            return [
                'description' => ($equipment->type ?? 'Equipment') . ' (' . ($equipment->size ?? 'N/A') . ') Rental for ' . $item['rental_days'] . ' days',
                'amount' => $item['item_total_price'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['rental_days'],
            ];
        })->toArray();

        // Add additional fees as separate invoice items
        if ($quote->delivery_fee > 0) {
            $invoiceItems[] = ['description' => 'Delivery Fee', 'amount' => $quote->delivery_fee, 'unit_price' => $quote->delivery_fee, 'quantity' => 1];
        }
        if ($quote->pickup_fee > 0) {
            $invoiceItems[] = ['description' => 'Pickup Fee', 'amount' => $quote->pickup_fee, 'unit_price' => $quote->pickup_fee, 'quantity' => 1];
        }
        if ($quote->damage_waiver > 0) {
            $invoiceItems[] = ['description' => 'Damage Waiver', 'amount' => $quote->damage_waiver, 'unit_price' => $quote->damage_waiver, 'quantity' => 1];
        }
        // Add disposal/overage fees here if they were part of quote total

        $invoice = Invoice::create([
            'vendor_id' => $quote->vendor_id,
            'customer_id' => $quote->customer_id,
            'invoice_number' => 'INV-' . (Invoice::where('vendor_id', $quote->vendor_id)->count() + 1), // Simple unique number
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(), // Due in 30 days
            'items' => $invoiceItems,
            'total_amount' => $quote->total_amount,
            'balance_due' => $quote->total_amount,
            'status' => 'Sent',
            'linked_booking_id' => $booking->id,
            'linked_quote_id' => $quote->id,
        ]);


        // --- 3. Update Quote Status and Link ---
        $quote->update([
            'status' => 'Accepted',
            'linked_booking_id' => $booking->id,
            'linked_invoice_id' => $invoice->id,
        ]);

        return redirect()->route('quotes.show', $quote->id)->with('success', 'Quote accepted and converted to Booking ' . $booking->id . ' and Invoice ' . $invoice->invoice_number . ' successfully!');
    }
}