<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Vendor; // For authentication fallback
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // For date calculations

class AnalyticsController extends Controller
{
    /**
     * Display the analytics overview dashboard.
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

        $currentYear = Carbon::now()->year;

        // Total Bookings (YTD)
        $totalBookingsYTD = Booking::where('vendor_id', $vendorId)
                                   ->whereYear('rental_start_date', $currentYear)
                                   ->count();

        // Avg. Booking Value (YTD)
        $totalBookingValueYTD = Booking::where('vendor_id', $vendorId)
                                       ->whereYear('rental_start_date', $currentYear)
                                       ->sum('total_price');
        $avgBookingValue = $totalBookingsYTD > 0 ? round($totalBookingValueYTD / $totalBookingsYTD, 2) : 0;

        // New Customers (YTD)
        $newCustomersYTD = Customer::where('vendor_id', $vendorId)
                                   ->whereYear('created_at', $currentYear)
                                   ->count();

        // Equipment Utilization (YTD)
        $totalEquipmentUnits = Equipment::where('vendor_id', $vendorId)->count();
        $totalRentalDays = 0;
        $bookingsInCurrentYear = Booking::where('vendor_id', $vendorId)
                                        ->whereYear('rental_start_date', $currentYear)
                                        ->get();

        foreach ($bookingsInCurrentYear as $booking) {
            $start = $booking->rental_start_date;
            $end = $booking->rental_end_date;
            $overlapStart = max($start, Carbon::parse($currentYear . '-01-01'));
            $overlapEnd = min($end, Carbon::parse($currentYear . '-12-31'));

            if ($overlapStart <= $overlapEnd) {
                $diffDays = $overlapStart->diffInDays($overlapEnd) + 1;
                $totalRentalDays += $diffDays;
            }
        }
        $daysInCurrentYear = Carbon::create($currentYear)->daysInYear;
        $maxPossibleRentalDays = $totalEquipmentUnits * $daysInCurrentYear;
        $equipmentUtilization = $maxPossibleRentalDays > 0 ? round(($totalRentalDays / $maxPossibleRentalDays) * 100, 1) : 0;

        // You might pull these from FinancialController for consistency
        $totalRevenueYTD = Invoice::where('vendor_id', $vendorId)
                                  ->whereIn('status', ['Paid', 'Partially Paid'])
                                  ->whereYear('issue_date', $currentYear)
                                  ->sum(\DB::raw('total_amount - balance_due'));
        $totalRevenueYTD = round($totalRevenueYTD, 2);

        $totalExpensesYTD = Expense::where('vendor_id', $vendorId)
                                   ->whereYear('date', $currentYear)
                                   ->sum('amount');
        $totalExpensesYTD = round($totalExpensesYTD, 2);


        return view('vendor.analytics.overview', compact(
            'totalBookingsYTD',
            'avgBookingValue',
            'newCustomersYTD',
            'equipmentUtilization',
            'totalRevenueYTD', // Added from financials for completeness
            'totalExpensesYTD', // Added from financials for completeness
            'vendor'
        ));
    }

    /**
     * Display the analytics trends page (with charts data).
     */
    public function trends()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in to view analytics trends.');
        }
        $vendor = Auth::guard('vendor')->user();
        $vendorId = $vendor->id;

        // --- Data for Daily Engagement & Bookings Chart ---
        $dailyLabels = [];
        $dailyVisitors = []; // Dummy/Conceptual
        $dailySocialClicks = []; // Dummy/Conceptual
        $dailyBookings = [];

        $today = Carbon::now();
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateStr = $date->toDateString();
            $dailyLabels[] = $date->format('D, M d'); // Mon, Jul 22

            // Fetch actual booking count for the day
            $dailyBookings[] = Booking::where('vendor_id', $vendorId)
                                      ->whereDate('rental_start_date', $dateStr)
                                      ->count();
            // Dummy data for Visitors and Social Clicks for now
            $dailyVisitors[] = rand(10, 50);
            $dailySocialClicks[] = rand(5, 30);
        }

        // --- Data for Monthly Revenue & Bookings Chart ---
        $monthlyLabels = [];
        $monthlyRevenue = [];
        $monthlyBookingsCount = [];

        for ($i = 11; $i >= 0; $i--) { // Last 12 months including current
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');

            $revenueThisMonth = Invoice::where('vendor_id', $vendorId)
                                      ->whereMonth('issue_date', $month->month)
                                      ->whereYear('issue_date', $month->year)
                                      ->whereIn('status', ['Paid', 'Partially Paid'])
                                      ->sum(\DB::raw('total_amount - balance_due'));
            $monthlyRevenue[] = round($revenueThisMonth, 2);

            $bookingsThisMonth = Booking::where('vendor_id', $vendorId)
                                       ->whereMonth('rental_start_date', $month->month)
                                       ->whereYear('rental_start_date', $month->year)
                                       ->count();
            $monthlyBookingsCount[] = $bookingsThisMonth;
        }


        // Prepare data for Chart.js in JSON format
        $dailyAnalyticsChartData = [
            'labels' => $dailyLabels,
            'datasets' => [
                ['label' => 'Visitors', 'data' => $dailyVisitors],
                ['label' => 'Social Clicks', 'data' => $dailySocialClicks],
                ['label' => 'Bookings', 'data' => $dailyBookings],
            ],
        ];

        $monthlyRevenueBookingsChartData = [
            'labels' => $monthlyLabels,
            'datasets' => [
                ['label' => 'Total Revenue', 'data' => $monthlyRevenue],
                ['label' => 'Bookings Count', 'data' => $monthlyBookingsCount],
            ],
        ];


        return view('vendor.analytics.trends', compact(
            'vendor',
            'dailyAnalyticsChartData',
            'monthlyRevenueBookingsChartData'
        ));
    }

    /**
     * Display the analytics reports page.
     * (Similar to FinancialController reports, but tailored for Analytics)
     */
    public function reports(Request $request)
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in to view analytics reports.');
        }
        $vendor = Auth::guard('vendor')->user();
        $vendorId = $vendor->id;

        $reportData = null;
        $reportTitle = "Select a Report";

        if ($request->has('report_type')) {
            switch ($request->input('report_type')) {
                case 'bookingsByCustomerType':
                    $reportTitle = 'Bookings by Customer Type';
                    $reportData = Customer::where('vendor_id', $vendorId)
                                          ->withCount(['bookings' => function ($query) use ($vendorId) {
                                              $query->where('vendor_id', $vendorId); // Ensure bookings belong to this vendor
                                          }])
                                          ->get()
                                          ->groupBy('customer_type')
                                          ->map(function ($group, $type) {
                                              return ['customer_type' => $type, 'total_bookings' => $group->sum('bookings_count')];
                                          });
                    break;
                case 'revenueByCustomerType':
                    $reportTitle = 'Revenue by Customer Type';
                    $reportData = Invoice::where('vendor_id', $vendorId)
                                         ->whereIn('status', ['Paid', 'Partially Paid'])
                                         ->select('customer_id', \DB::raw('SUM(total_amount - balance_due) as total_revenue'))
                                         ->groupBy('customer_id')
                                         ->with('customer') // Eager load customer for type
                                         ->get()
                                         ->groupBy('customer.customer_type')
                                         ->map(function ($group, $type) {
                                            return ['customer_type' => $type, 'total_revenue' => $group->sum('total_revenue')];
                                         })
                                         ->sortByDesc('total_revenue');
                    break;
                case 'topCustomers':
                    $reportTitle = 'Top Customers by Revenue';
                    $reportData = Invoice::where('vendor_id', $vendorId)
                                        ->whereIn('status', ['Paid', 'Partially Paid'])
                                        ->select('customer_id', \DB::raw('SUM(total_amount - balance_due) as total_revenue'))
                                        ->groupBy('customer_id')
                                        ->orderByDesc('total_revenue')
                                        ->limit(5)
                                        ->with('customer')
                                        ->get();
                    break;
                case 'bookingsByEquipmentType':
                    $reportTitle = 'Bookings by Equipment Type';
                    $reportData = Booking::where('vendor_id', $vendorId)
                                        ->with('equipment')
                                        ->get()
                                        ->groupBy('equipment.type')
                                        ->map(function ($group, $type) {
                                            return ['equipment_type' => $type, 'total_bookings' => $group->count()];
                                        })
                                        ->sortByDesc('total_bookings');
                    break;
                case 'revenueByEquipmentType':
                    $reportTitle = 'Revenue by Equipment Type';
                    $reportData = Invoice::where('vendor_id', $vendorId)
                                         ->whereIn('status', ['Paid', 'Partially Paid'])
                                         ->with(['linkedBooking.equipment']) // Eager load equipment through linked booking
                                         ->get()
                                         ->flatMap(function($invoice) {
                                             $paidAmountRatio = $invoice->total_amount > 0 ? ($invoice->total_amount - $invoice->balance_due) / $invoice->total_amount : 0;
                                             $items = [];
                                             foreach ($invoice->items as $item) { // Iterate over items JSON
                                                $equipmentType = 'Other Service';
                                                // Try to determine equipment type from linked booking's equipment or item description
                                                if ($invoice->linkedBooking && $invoice->linkedBooking->equipment) {
                                                    $equipmentType = $invoice->linkedBooking->equipment->type;
                                                } elseif (isset($item['description'])) {
                                                    if (str_contains(strtolower($item['description']), 'dumpster')) $equipmentType = 'Dumpster';
                                                    elseif (str_contains(strtolower($item['description']), 'toilet')) $equipmentType = 'Temporary Toilet';
                                                    elseif (str_contains(strtolower($item['description']), 'container')) $equipmentType = 'Storage Container';
                                                    elseif (str_contains(strtolower($item['description']), 'junk removal')) $equipmentType = 'Junk Removal';
                                                }
                                                $items[] = [
                                                    'type' => $equipmentType,
                                                    'revenue' => $item['amount'] * $paidAmountRatio
                                                ];
                                             }
                                             return $items;
                                         })
                                         ->groupBy('type')
                                         ->map(function($group) {
                                             return ['type' => $group->first()['type'], 'total_revenue' => $group->sum('revenue')];
                                         })
                                         ->sortByDesc('total_revenue');
                    break;
                case 'equipmentUtilization':
                    $reportTitle = 'Equipment Utilization Rates (Conceptual)';
                    $reportData = (object)['message' => 'This report would calculate the percentage of time each individual equipment unit or type is rented versus its total availability. <br><strong>Dummy Data:</strong> Overall Dumpster Utilization: 75%; Toilet Utilization: 60%; Container Utilization: 80%.'];
                    break;
            }
        }

        return view('vendor.analytics.reports', compact('vendor', 'reportData', 'reportTitle'));
    }

    /**
     * Display conceptual performance insights.
     */
    public function performance()
    {
        if (!Auth::guard('vendor')->check()) {
            return redirect()->route('login')->with('error', 'Please log in to view performance insights.');
        }
        $vendor = Auth::guard('vendor')->user();
        return view('vendor.analytics.performance', compact('vendor'));
    }
}