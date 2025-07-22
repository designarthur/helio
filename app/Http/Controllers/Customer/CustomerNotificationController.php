<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Customer; // To identify the customer profile from the authenticated user
use App\Models\User;     // The model used for customer authentication
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection; // To use Laravel's collection methods

class CustomerNotificationController extends Controller
{
    /**
     * Display a listing of the customer's notifications with filters.
     * Notifications are conceptually generated from bookings, invoices, quotes, etc.
     * In a real system, you'd have a dedicated 'notifications' table.
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
        $currentDate = Carbon::now();

        // --- Simulate Notifications (as if fetched from a 'notifications' table) ---
        // For a real system, these would be records from a dedicated Notifications table.
        // For now, we'll generate them based on existing data.
        $allNotifications = new Collection();
        $notificationIdCounter = 1; // Simple ID counter for simulated notifications

        // 1. Bookings Notifications
        $bookings = Booking::where('customer_id', $customerId)
                           ->whereIn('status', ['Confirmed', 'Delivered', 'Completed', 'Cancelled'])
                           ->with('equipment')
                           ->get();
        foreach ($bookings as $booking) {
            $message = '';
            $category = 'Bookings';
            $type = 'bookings';
            $read = false; // For simulation, assume new/recent are unread

            if ($booking->status === 'Confirmed' && $booking->rental_start_date->greaterThan($currentDate)) {
                $message = "Your booking #{$booking->id} ({$booking->equipment->type} - {$booking->equipment->size}) is confirmed for delivery on {$booking->rental_start_date->format('M d, Y')}.";
                $read = $booking->updated_at->lt(Carbon::now()->subDays(2)); // Read if updated more than 2 days ago
            } elseif ($booking->status === 'Delivered') {
                $message = "Your booking #{$booking->id} ({$booking->equipment->type} - {$booking->equipment->size}) has been delivered to {$booking->delivery_address}.";
                $read = $booking->updated_at->lt(Carbon::now()->subDays(1)); // Read if updated more than 1 day ago
            } elseif ($booking->status === 'Completed') {
                $message = "Your rental #{$booking->id} ({$booking->equipment->type}) is completed. Thank you!";
                 $read = $booking->updated_at->lt(Carbon::now()->subDays(3));
            } elseif ($booking->status === 'Cancelled') {
                $message = "Your booking #{$booking->id} ({$booking->equipment->type}) has been cancelled as per your request.";
                $read = $booking->updated_at->lt(Carbon::now()->subDays(3));
            }
            if ($message) {
                $allNotifications->push([
                    'id' => 'B'.$notificationIdCounter++,
                    'type' => $type,
                    'category' => $category,
                    'message' => $message,
                    'time' => $booking->updated_at,
                    'read' => $read,
                    'linked_id' => $booking->id,
                    'linked_route' => route('customer.bookings.show', $booking->id)
                ]);
            }
        }

        // 2. Invoices Notifications
        $invoices = Invoice::where('customer_id', $customerId)->get();
        foreach ($invoices as $invoice) {
            $message = '';
            $category = 'Invoices';
            $type = 'invoices';
            $read = false;
            if ($invoice->status === 'Sent' && $invoice->balance_due > 0) {
                $message = "Invoice #{$invoice->invoice_number} for $".number_format($invoice->total_amount, 2)." is due on {$invoice->due_date->format('M d, Y')}. Balance: $".number_format($invoice->balance_due, 2).".";
                $read = $invoice->updated_at->lt(Carbon::now()->subDays(2));
            } elseif ($invoice->status === 'Overdue' && $invoice->balance_due > 0) {
                $message = "ACTION REQUIRED: Invoice #{$invoice->invoice_number} is overdue! Balance: $".number_format($invoice->balance_due, 2).".";
                $read = false; // Overdue invoices are always unread
            } elseif ($invoice->status === 'Paid') {
                $message = "Payment received for Invoice #{$invoice->invoice_number}. Thank you for your payment!";
                $read = $invoice->updated_at->lt(Carbon::now()->subDays(3));
            } elseif ($invoice->status === 'Partially Paid') {
                 $message = "Invoice #{$invoice->invoice_number} is partially paid. Balance remaining: $".number_format($invoice->balance_due, 2).".";
                 $read = $invoice->updated_at->lt(Carbon::now()->subDays(1));
            }
            if ($message) {
                $allNotifications->push([
                    'id' => 'I'.$notificationIdCounter++,
                    'type' => $type,
                    'category' => $category,
                    'message' => $message,
                    'time' => $invoice->updated_at,
                    'read' => $read,
                    'linked_id' => $invoice->id,
                    'linked_route' => route('customer.invoices.show', $invoice->id)
                ]);
            }
        }

        // 3. Quotes Notifications
        $quotes = Quote::where('customer_id', $customerId)->get();
        foreach ($quotes as $quote) {
            $message = '';
            $category = 'Quotes';
            $type = 'quotes';
            $read = false;
            if ($quote->status === 'Sent' || $quote->status === 'Draft') {
                $message = "New quote #{$quote->id} for $".number_format($quote->total_amount, 2)." is available for your review. Expires on {$quote->expiry_date->format('M d, Y')}.";
                $read = false; // New quotes are unread
            } elseif ($quote->status === 'Accepted') {
                $message = "Quote #{$quote->id} has been accepted and your booking is being processed!";
                $read = $quote->updated_at->lt(Carbon::now()->subDays(1));
            } elseif ($quote->status === 'Rejected') {
                $message = "Quote #{$quote->id} has been rejected. Please contact us if you need a revision.";
                $read = $quote->updated_at->lt(Carbon::now()->subDays(2));
            } elseif ($quote->status === 'Expired') {
                $message = "Quote #{$quote->id} has expired. Please submit a new request if still interested.";
                $read = $quote->updated_at->lt(Carbon::now()->subDays(3));
            }
            if ($message) {
                $allNotifications->push([
                    'id' => 'Q'.$notificationIdCounter++,
                    'type' => $type,
                    'category' => $category,
                    'message' => $message,
                    'time' => $quote->updated_at,
                    'read' => $read,
                    'linked_id' => $quote->id,
                    'linked_route' => route('customer.quotes.show', $quote->id)
                ]);
            }
        }
        
        // 4. General Alerts (Conceptual)
        $allNotifications->push([
            'id' => 'A1', 'type' => 'alerts', 'category' => 'Alerts', 'message' => 'System maintenance scheduled for July 28th, 1 AM - 3 AM EST.', 'time' => Carbon::now()->subDays(1), 'read' => false, 'linked_id' => null, 'linked_route' => '#'
        ]);
        $allNotifications->push([
            'id' => 'A2', 'type' => 'alerts', 'category' => 'Alerts', 'message' => 'New features have been added to your dashboard! Check them out.', 'time' => Carbon::now()->subDays(5), 'read' => true, 'linked_id' => null, 'linked_route' => route('customer.dashboard')
        ]);


        // Sort all notifications by time (most recent first)
        $allNotifications = $allNotifications->sortByDesc('time')->values();

        // Apply filtering for the view
        $filter = $request->input('filter', 'all');
        $notifications = $allNotifications->filter(function ($notification) use ($filter) {
            if ($filter === 'all') {
                return true;
            } elseif ($filter === 'unread') {
                return !$notification['read'];
            } else {
                return $notification['type'] === $filter; // e.g., 'bookings', 'invoices', 'quotes', 'alerts'
            }
        });

        // Fetch counts for badges
        $allCount = $allNotifications->count();
        $unreadCount = $allNotifications->where('read', false)->count();
        $bookingsCount = $allNotifications->where('type', 'bookings')->count();
        $invoicesCount = $allNotifications->where('type', 'invoices')->count();
        $quotesCount = $allNotifications->where('type', 'quotes')->count();
        $alertsCount = $allNotifications->where('type', 'alerts')->count();


        return view('customer.notifications.index', compact(
            'notifications',
            'filter',
            'allCount',
            'unreadCount',
            'bookingsCount',
            'invoicesCount',
            'quotesCount',
            'alertsCount',
            'customerProfile' // For sidebar if needed
        ));
    }

    /**
     * Conceptual: Mark a specific notification as read.
     * In a real system, this would update a 'read_at' timestamp in a 'notifications' table.
     */
    public function markAsRead($notificationId)
    {
        // This is a conceptual action. In a real system:
        // 1. Find the notification record for the authenticated customer.
        // 2. Update its 'read' status (e.g., set 'read_at' timestamp).
        // 3. Save to database.
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Conceptual: Mark all notifications for the customer as read.
     */
    public function markAllAsRead()
    {
        // Conceptual action: update all 'unread' notifications for the customer.
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Conceptual: Clear/delete all notifications for the customer.
     */
    public function clearAll()
    {
        // Conceptual action: delete all notifications for the customer.
        return redirect()->back()->with('success', 'All notifications cleared.');
    }
}