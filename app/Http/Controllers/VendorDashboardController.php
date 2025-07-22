<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Quote;
use App\Models\Equipment; // For equipment type bookings
use App\Models\Vendor;    // For current vendor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VendorDashboardController extends Controller
{
    /**
     * Display the vendor dashboard.
     */
    public function index()
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            // Fallback for development if not authenticated, remove in production
            $vendor = Vendor::first();
            if (!$vendor) {
                return redirect()->route('vendor.register')->with('error', 'No vendor found. Please register first.');
            }
        }
        $vendorId = $vendor->id;

        // --- Fetch Data for Dashboard KPIs ---
        $currentDate = Carbon::now();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // Total Revenue (This Month)
        $totalRevenue = Invoice::where('vendor_id', $vendorId)
                               ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
                               ->whereIn('status', ['Paid', 'Partially Paid'])
                               ->sum(\DB::raw('total_amount - balance_due')); // Sum of actually paid amount
        $totalRevenue = round($totalRevenue, 2);

        // Total Bookings (This Month)
        $totalBookings = Booking::where('vendor_id', $vendorId)
                                ->whereBetween('rental_start_date', [$startOfMonth, $endOfMonth])
                                ->count();

        // Outstanding A/R (Accounts Receivable)
        $outstandingAR = Invoice::where('vendor_id', $vendorId)
                                ->whereIn('status', ['Sent', 'Partially Paid', 'Overdue'])
                                ->sum('balance_due');
        $outstandingAR = round($outstandingAR, 2);

        // Total Expenses (This Month)
        $totalExpenses = Expense::where('vendor_id', $vendorId)
                                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                                ->sum('amount');
        $totalExpenses = round($totalExpenses, 2);

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

        $monthlyRevenueBookingsChartData = [
            'labels' => $monthlyLabels,
            'datasets' => [
                ['label' => 'Total Revenue', 'data' => $monthlyRevenue],
                ['label' => 'Bookings Count', 'data' => $monthlyBookingsCount],
            ],
        ];

        // --- Data for Equipment Type Bookings Chart (Pie Chart) ---
        $equipmentTypeBookings = Booking::where('vendor_id', $vendorId)
                                        ->with('equipment')
                                        ->get()
                                        ->groupBy('equipment.type')
                                        ->map(function ($group) {
                                            return $group->count();
                                        });

        $equipmentTypeBookingsChartData = [
            'labels' => $equipmentTypeBookings->keys(),
            'datasets' => [['data' => $equipmentTypeBookings->values()]],
        ];
        
        // --- Pending Requests (for the list at the bottom) ---
        // Fetch pending quotes and bookings
        $pendingQuotes = Quote::where('vendor_id', $vendorId)
                              ->whereIn('status', ['Draft', 'Sent'])
                              ->orderBy('quote_date', 'asc')
                              ->with('customer')
                              ->limit(5) // Show top 5
                              ->get();
        
        $pendingBookings = Booking::where('vendor_id', $vendorId)
                                  ->where('status', 'Pending')
                                  ->orderBy('rental_start_date', 'asc')
                                  ->with(['customer', 'equipment'])
                                  ->limit(5) // Show top 5
                                  ->get();

        $pendingRequests = collect();

        foreach ($pendingBookings as $booking) {
            $pendingRequests->push([
                'type' => 'booking',
                'description' => 'New Booking Request: ' . ($booking->equipment->type ?? 'N/A') . ' (' . ($booking->equipment->size ?? 'N/A') . ')',
                'customer_info' => $booking->customer->name ?? 'N/A',
                'due_date' => $booking->rental_start_date->format('M d, Y'),
                'status' => $booking->status,
                'status_class' => 'bg-ut-orange', // Class for status badge
                'url' => route('bookings.show', $booking->id) // Link to full details
            ]);
        }

        foreach ($pendingQuotes as $quote) {
            $pendingRequests->push([
                'type' => 'quote',
                'description' => 'New Quote Request: ' . (count($quote->items) > 0 ? ($quote->items[0]['description'] ?? 'Multiple Items') : 'N/A'),
                'customer_info' => $quote->customer->name ?? 'N/A',
                'due_date' => $quote->expiry_date ? $quote->expiry_date->format('M d, Y') : 'N/A',
                'status' => $quote->status,
                'status_class' => 'bg-yellow-500', // Class for status badge
                'url' => route('quotes.show', $quote->id) // Link to full details
            ]);
        }

        $pendingRequests = $pendingRequests->sortBy('due_date')->take(5); // Get top 5 sorted by due date

        return view('vendor.dashboard', compact(
            'totalRevenue',
            'totalBookings',
            'outstandingAR',
            'totalExpenses',
            'monthlyRevenueBookingsChartData',
            'equipmentTypeBookingsChartData',
            'pendingRequests'
        ));
    }
}