<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Expense;
use App\Models\Customer; // For new customer logic
use App\Models\Vendor; // For authentication fallback
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    /**
     * Display the financial overview dashboard.
     */
    public function overview()
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            $vendor = Vendor::first(); // Fallback for dev if not authenticated
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;

        $currentYear = date('Y');

        // Total Revenue (YTD)
        $totalRevenue = Invoice::where('vendor_id', $vendorId)
                               ->whereIn('status', ['Paid', 'Partially Paid'])
                               ->whereYear('issue_date', $currentYear)
                               ->sum(\DB::raw('total_amount - balance_due')); // Sum of actually paid amount
        $totalRevenue = round($totalRevenue, 2);

        // Outstanding A/R (Accounts Receivable)
        $outstandingAR = Invoice::where('vendor_id', $vendorId)
                                ->whereIn('status', ['Sent', 'Partially Paid', 'Overdue'])
                                ->sum('balance_due');
        $outstandingAR = round($outstandingAR, 2);

        // Total Expenses (YTD)
        $totalExpenses = Expense::where('vendor_id', $vendorId)
                                ->whereYear('date', $currentYear)
                                ->sum('amount');
        $totalExpenses = round($totalExpenses, 2);

        // Total Bookings (YTD)
        $totalBookingsYTD = Booking::where('vendor_id', $vendorId)
                                   ->whereYear('rental_start_date', $currentYear)
                                   ->count();

        // Avg. Booking Value (YTD)
        $totalBookingValueYTD = Booking::where('vendor_id', $vendorId)
                                       ->whereYear('rental_start_date', $currentYear)
                                       ->sum('total_price');
        $avgBookingValue = $totalBookingsYTD > 0 ? round($totalBookingValueYTD / $totalBookingsYTD, 2) : 0;

        // New Customers (YTD) - Simplified: customers whose first booking or creation was this year
        // This requires more complex logic to track first interaction.
        // For simplicity, let's count customers created this year for this vendor.
        $newCustomersYTD = Customer::where('vendor_id', $vendorId)
                                   ->whereYear('created_at', $currentYear)
                                   ->count();
        // A more robust method would check for the earliest booking date per customer.

        // Equipment Utilization (Conceptual/Simplified)
        $totalEquipmentUnits = Equipment::where('vendor_id', $vendorId)->count();
        $daysInYear = 365; // Or 366 for leap year detection
        $totalRentalDays = 0;
        
        $bookingsInCurrentYear = Booking::where('vendor_id', $vendorId)
                                        ->whereYear('rental_start_date', $currentYear)
                                        ->get(); // Fetch bookings for the current year

        foreach ($bookingsInCurrentYear as $booking) {
            $start = $booking->rental_start_date;
            $end = $booking->rental_end_date;
            // Calculate overlapping days within the current year for the booking duration
            $overlapStart = max($start, \Carbon\Carbon::parse($currentYear . '-01-01'));
            $overlapEnd = min($end, \Carbon\Carbon::parse($currentYear . '-12-31'));

            if ($overlapStart <= $overlapEnd) {
                $diffDays = $overlapStart->diffInDays($overlapEnd) + 1;
                $totalRentalDays += $diffDays;
            }
        }
        
        $maxPossibleRentalDays = $totalEquipmentUnits * $daysInYear; // Max theoretical available rental days for current year
        $utilizationRate = $maxPossibleRentalDays > 0 ? round(($totalRentalDays / $maxPossibleRentalDays) * 100, 1) : 0;


        return view('vendor.financials.overview', compact(
            'totalRevenue',
            'outstandingAR',
            'totalExpenses',
            'totalBookingsYTD',
            'avgBookingValue',
            'newCustomersYTD',
            'utilizationRate',
            'vendor' // Pass vendor object if needed for layout
        ));
    }

    /**
     * Display the financial reports page.
     * (Conceptual - report generation would happen here via AJAX or form submissions)
     */
    public function reports(Request $request)
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in to view reports.');
        }
        $vendor = Auth::guard('vendor')->user();
        $vendorId = $vendor->id;

        // Example: Generate "Revenue by Customer" report (simplified)
        $reportData = [];
        $reportTitle = "Select a Report";

        if ($request->has('report_type')) {
            switch ($request->input('report_type')) {
                case 'revenueByCustomer':
                    $reportTitle = 'Revenue by Customer Report';
                    $reportData = Invoice::where('vendor_id', $vendorId)
                                        ->whereIn('status', ['Paid', 'Partially Paid'])
                                        ->select('customer_id', \DB::raw('SUM(total_amount - balance_due) as total_revenue'))
                                        ->groupBy('customer_id')
                                        ->orderByDesc('total_revenue')
                                        ->with('customer') // Eager load customer name
                                        ->get();
                    break;
                case 'revenueByEquipment':
                    $reportTitle = 'Revenue by Equipment Type Report';
                    // This is more complex as invoice items have descriptions, not equipment_id
                    // Need to link back through bookings, or parse item descriptions
                    $reportData = Invoice::where('vendor_id', $vendorId)
                                        ->whereIn('status', ['Paid', 'Partially Paid'])
                                        ->select('items') // Get items JSON
                                        ->get()
                                        ->flatMap(function($invoice) {
                                            $paidAmountRatio = ($invoice->total_amount - $invoice->balance_due) / $invoice->total_amount;
                                            return collect($invoice->items)->map(function($item) use ($paidAmountRatio) {
                                                // Simplified: Extract type from description or link to booking.
                                                // For a real app, invoice items would ideally link directly to equipment.
                                                $equipmentType = 'Other Service';
                                                if (str_contains(strtolower($item['description']), 'dumpster')) $equipmentType = 'Dumpster';
                                                elseif (str_contains(strtolower($item['description']), 'toilet')) $equipmentType = 'Temporary Toilet';
                                                elseif (str_contains(strtolower($item['description']), 'container')) $equipmentType = 'Storage Container';
                                                // You would need to refine this by checking linked_booking_id and then booking->equipment->type
                                                return [
                                                    'type' => $equipmentType,
                                                    'revenue' => $item['amount'] * $paidAmountRatio
                                                ];
                                            });
                                        })
                                        ->groupBy('type')
                                        ->map(function($group) {
                                            return ['type' => $group->first()['type'], 'total_revenue' => $group->sum('revenue')];
                                        })
                                        ->sortByDesc('total_revenue');
                    break;
                case 'salesTax':
                    $reportTitle = 'Sales Tax Collected Report (Conceptual)';
                    $reportData = (object)['message' => 'In a real system, this report would accurately calculate and summarize sales tax collected per jurisdiction (state, county, city) based on invoice line items. <br><strong>Dummy Data:</strong> Total Sales Tax Collected: $500.00 (Example only)'];
                    break;
                case 'arAging':
                    $reportTitle = 'Accounts Receivable Aging Report';
                    $invoices = Invoice::where('vendor_id', $vendorId)
                                        ->where('balance_due', '>', 0)
                                        ->with('customer')
                                        ->get();
                    
                    $arAgingBuckets = [
                        'Current' => [],
                        '1-30 Days' => [],
                        '31-60 Days' => [],
                        '61-90 Days' => [],
                        '90+ Days' => [],
                    ];

                    foreach ($invoices as $invoice) {
                        $daysOverdue = now()->diffInDays($invoice->due_date, false); // false means get negative if due_date is in past

                        if ($daysOverdue >= 0) { // Due date is today or in the future
                            $arAgingBuckets['Current'][] = $invoice;
                        } elseif ($daysOverdue >= -30) { // 1-30 days overdue
                            $arAgingBuckets['1-30 Days'][] = $invoice;
                        } elseif ($daysOverdue >= -60) { // 31-60 days overdue
                            $arAgingBuckets['31-60 Days'][] = $invoice;
                        } elseif ($daysOverdue >= -90) { // 61-90 days overdue
                            $arAgingBuckets['61-90 Days'][] = $invoice;
                        } else { // 90+ days overdue
                            $arAgingBuckets['90+ Days'][] = $invoice;
                        }
                    }
                    $reportData = $arAgingBuckets;

                    break;
                case 'customerStatements':
                    $reportTitle = 'Customer Statements (Summary) - Conceptual';
                    $reportData = (object)['message' => 'This report would generate a detailed statement for each customer, showing all their invoices, payments received, and current outstanding balances over a selected period. <br>(Requires robust transaction logging and aggregation. For demo, consider viewing individual customer financial overviews in the Customer Management module.)'];
                    break;
            }
        }

        return view('vendor.financials.reports', compact('vendor', 'reportData', 'reportTitle'));
    }
}