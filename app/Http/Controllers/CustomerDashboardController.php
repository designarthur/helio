<?php

namespace App\Http\Controllers;

use App\Models\Customer; // To get the associated customer profile
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Quote; // For customer quotes summary
use App\Models\User; // The model used for customer authentication
use App\Models\Vendor; // For branding settings
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    /**
     * Display the customer dashboard.
     */
    public function index()
    {
        // Get the authenticated User instance (who is a customer)
        $user = Auth::guard('customer')->user();

        if (!$user || $user->role !== 'customer') {
            // This should ideally be caught by middleware, but good to have a fallback
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Authentication failed. Please log in as a customer.');
        }

        // Find the associated Customer profile in the 'customers' table
        // We assume a 'customer' role user in the 'users' table has a corresponding entry in the 'customers' table.
        // This mapping logic needs to be established (e.g., matching by email or linking via a customer_id column on users table).
        // For simplicity, let's assume the email matches a customer, or you might have a customer_id column on your users table.
        // If your 'users' table (for customers) had a foreign key 'customer_profile_id', you'd use that.
        // For now, let's match by email as a common identifier, or pick a dummy customer.
        $customerProfile = Customer::where('email', $user->email)
                                   ->where('vendor_id', $user->vendor_id) // Link to vendor who this customer belongs to
                                   ->first();

        if (!$customerProfile) {
            // Handle case where user account exists but no matching customer profile found
            Auth::guard('customer')->logout();
            return redirect()->route('customer.login')->with('error', 'Customer profile not found. Please contact support.');
        }

        $customerId = $customerProfile->id;
        $vendorId = $user->vendor_id; // Get the vendor this customer belongs to

        // --- Fetch Data for Dashboard KPIs ---
        $currentDate = Carbon::now();

        // Current Rentals (Active bookings)
        $currentRentalsCount = Booking::where('customer_id', $customerId)
                                      ->where('rental_end_date', '>=', $currentDate)
                                      ->whereIn('status', ['Confirmed', 'Delivered'])
                                      ->count();

        // Upcoming Bookings (Confirmed/Pending bookings starting in future)
        $upcomingBookingsCount = Booking::where('customer_id', $customerId)
                                        ->where('rental_start_date', '>', $currentDate)
                                        ->whereIn('status', ['Pending', 'Confirmed'])
                                        ->count();
        
        // Outstanding Balance
        $outstandingBalance = Invoice::where('customer_id', $customerId)
                                     ->where('balance_due', '>', 0)
                                     ->sum('balance_due');
        $outstandingBalance = round($outstandingBalance, 2);

        // Overdue Invoices
        $overdueInvoicesCount = Invoice::where('customer_id', $customerId)
                                       ->where('balance_due', '>', 0)
                                       ->where('due_date', '<', $currentDate)
                                       ->count();

        // Pending Invoices (not overdue, but balance > 0)
        $pendingInvoicesCount = Invoice::where('customer_id', $customerId)
                                       ->where('balance_due', '>', 0)
                                       ->where('due_date', '>=', $currentDate)
                                       ->count();


        // Total Spent This Month (Paid invoices in current month)
        $totalSpentThisMonth = Invoice::where('customer_id', $customerId)
                                      ->whereYear('issue_date', $currentDate->year)
                                      ->whereMonth('issue_date', $currentDate->month)
                                      ->whereIn('status', ['Paid', 'Partially Paid'])
                                      ->sum(\DB::raw('total_amount - balance_due')); // Sum of actually paid amount
        $totalSpentThisMonth = round($totalSpentThisMonth, 2);

        // Completed Rentals This Month
        $completedRentalsThisMonth = Booking::where('customer_id', $customerId)
                                            ->whereYear('rental_end_date', $currentDate->year)
                                            ->whereMonth('rental_end_date', $currentDate->month)
                                            ->where('status', 'Completed')
                                            ->count();

        // Recent Activity (simplified: last 4 bookings or invoices)
        $recentBookings = Booking::where('customer_id', $customerId)
                                 ->orderBy('updated_at', 'desc')
                                 ->limit(4)
                                 ->with('equipment')
                                 ->get();

        $recentInvoices = Invoice::where('customer_id', $customerId)
                                 ->orderBy('updated_at', 'desc')
                                 ->limit(4)
                                 ->get();
        
        // Combine and sort recent activities for display
        $recentActivity = $recentBookings->map(function ($booking) {
            return [
                'type' => 'booking',
                'description' => ($booking->equipment->type ?? 'Equipment') . ' ' . ($booking->equipment->size ?? '') . ' ' . $booking->status,
                'id_ref' => $booking->id,
                'date' => $booking->updated_at,
                'display_text' => ($booking->equipment->type ?? 'Rental') . ' ' . $booking->status . ' (Booking #' . $booking->id . ')',
                'url' => route('customer.bookings.show', $booking->id) // Assuming customer booking show route
            ];
        })->merge($recentInvoices->map(function ($invoice) {
            return [
                'type' => 'invoice',
                'description' => 'Invoice #' . $invoice->invoice_number . ' ' . $invoice->status,
                'id_ref' => $invoice->id,
                'date' => $invoice->updated_at,
                'display_text' => 'Invoice #' . $invoice->invoice_number . ' ' . $invoice->status . ' ($' . number_format($invoice->total_amount, 2) . ')',
                'url' => route('customer.invoices.show', $invoice->id) // Assuming customer invoice show route
            ];
        }))->sortByDesc('date')->take(4); // Take up to 4 most recent


        // Get vendor branding settings for customer portal
        $vendor = Vendor::find($vendorId);
        $brandingSettings = $vendor->branding_settings ?? [];
        $portalBannerText = $brandingSettings['portalBannerText'] ?? 'Welcome to Your Rental Portal!';


        return view('customer.dashboard', compact(
            'user', // The authenticated user object (customer)
            'customerProfile', // The customer's profile from the 'customers' table
            'portalBannerText',
            'currentRentalsCount',
            'upcomingBookingsCount',
            'outstandingBalance',
            'overdueInvoicesCount',
            'pendingInvoicesCount',
            'totalSpentThisMonth',
            'completedRentalsThisMonth',
            'recentActivity'
        ));
    }
}