<?php

use Illuminate\Support\Facades\Route;

// Main Controllers
use App\Http\Controllers\VendorRegistrationController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\JunkRemovalJobController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BrandingController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SettingsController;

// Authentication Controllers
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\DriverLoginController;
use App\Http\Controllers\Auth\VendorLoginController;

// Customer-facing Module Controllers
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerBookingController;
use App\Http\Controllers\Customer\CustomerInvoiceController;
use App\Http\Controllers\Customer\CustomerQuoteController;
use App\Http\Controllers\Customer\CustomerNotificationController;
use App\Http\Controllers\Customer\CustomerPaymentMethodController;
use App\Http\Controllers\Customer\CustomerProfileController;

// Driver-facing Module Controllers
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\Driver\DriverAssignedRouteController;
use App\Http\Controllers\Driver\DriverScheduleController;
use App\Http\Controllers\Driver\VehicleInspectionController;
use App\Http\Controllers\Driver\DriverLogController;
use App\Http\Controllers\Driver\DriverMessageController;
use App\Http\Controllers\Driver\DriverProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- PUBLIC ROUTES ---

// Homepage
Route::get('/', function () {
    return view('home');
})->name('home');

// Vendor Registration Routes
Route::get('/register/vendor', [VendorRegistrationController::class, 'create'])->name('vendor.register');
Route::post('/register/vendor', [VendorRegistrationController::class, 'store'])->name('vendor.register.store');
Route::get('/register/vendor/success', [VendorRegistrationController::class, 'success'])->name('vendor.registration.success');

// Main Login Page (for Vendors)
Route::get('/login', [VendorLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [VendorLoginController::class, 'login'])->name('vendor.login.attempt');
Route::post('/vendor/logout', [VendorLoginController::class, 'logout'])->name('vendor.logout');

// Customer Authentication Routes
Route::get('/customer/login', [CustomerLoginController::class, 'showLoginForm'])->name('customer.login');
Route::post('/customer/login', [CustomerLoginController::class, 'login'])->name('customer.login.attempt');
Route::post('/customer/logout', [CustomerLoginController::class, 'logout'])->name('customer.logout');

// Driver Authentication Routes
Route::get('/driver/login', [DriverLoginController::class, 'showLoginForm'])->name('driver.login');
Route::post('/driver/login', [DriverLoginController::class, 'login'])->name('driver.login.attempt');
Route::post('/driver/logout', [DriverLoginController::class, 'logout'])->name('driver.logout');

// --- VENDOR MODULE ROUTES ---
// Protected by vendor authentication middleware

Route::middleware('auth:vendor')->group(function () {
    
    // Vendor Dashboard
    Route::get('/vendor/dashboard', [VendorDashboardController::class, 'index'])->name('vendor.dashboard');

    // Equipment Management Routes
    Route::resource('equipment', EquipmentController::class);

    // Customer Management Routes
    Route::resource('customers', CustomerController::class);

    // Driver Management Routes  
    Route::resource('drivers', DriverController::class);

    // Booking Management Routes
    Route::resource('bookings', BookingController::class);
    // Route for dynamic price calculation (AJAX)
    Route::get('/api/bookings/calculate-price', [BookingController::class, 'calculatePrice'])->name('api.bookings.calculatePrice');

    // Quotes Management Routes
    Route::resource('quotes', QuoteController::class);
    // Route for converting quote to booking/invoice
    Route::post('quotes/{quote}/convert', [QuoteController::class, 'convertToBookingAndInvoice'])->name('quotes.convert');
    // Route for dynamic quote total calculation
    Route::post('/api/quotes/calculate-total', [QuoteController::class, 'calculateQuoteTotal'])->name('api.quotes.calculateTotal');

    // Invoices Management Routes
    Route::resource('invoices', InvoiceController::class);
    // Route for quick mark as paid
    Route::post('invoices/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.markAsPaid');

    // Payments Management Routes
    Route::resource('payments', PaymentController::class);

    // Junk Removal Jobs Management Routes
    Route::resource('junk_removal_jobs', JunkRemovalJobController::class);

    // Analytics Module Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/overview', [AnalyticsController::class, 'overview'])->name('overview');
        Route::get('/trends', [AnalyticsController::class, 'trends'])->name('trends');
        Route::get('/reports', [AnalyticsController::class, 'reports'])->name('reports');
        Route::get('/performance', [AnalyticsController::class, 'performance'])->name('performance');
    });

    // Financials Module Routes
    Route::prefix('financials')->name('financials.')->group(function () {
        Route::get('/overview', [FinancialController::class, 'overview'])->name('overview');
        Route::get('/reports', [FinancialController::class, 'reports'])->name('reports');
        // Expense Management Sub-module Routes
        Route::resource('expenses', ExpenseController::class);
    });

    // Branding Module Routes
    Route::get('/branding', [BrandingController::class, 'show'])->name('branding.show');
    Route::post('/branding', [BrandingController::class, 'update'])->name('branding.update');

    // Dispatching & Route Optimization Module Routes
    Route::prefix('dispatching')->name('dispatching.')->group(function () {
        Route::get('/', [DispatchController::class, 'show'])->name('show');
        Route::post('/assign', [DispatchController::class, 'assignJob'])->name('assign');
        Route::post('/simulate-optimization', [DispatchController::class, 'simulateRouteOptimization'])->name('simulateOptimization');
    });

    // Settings Module Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'show'])->name('show');
        Route::post('/update-profile', [SettingsController::class, 'updateProfile'])->name('updateProfile');
        Route::post('/update-notifications', [SettingsController::class, 'updateNotifications'])->name('updateNotifications');
        Route::post('/update-integrations', [SettingsController::class, 'updateIntegrations'])->name('updateIntegrations');
        
        // Internal Users (Staff) Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/create', [SettingsController::class, 'createUser'])->name('create');
            Route::post('/', [SettingsController::class, 'storeUser'])->name('store');
            Route::get('/{user}/edit', [SettingsController::class, 'editUser'])->name('edit');
            Route::put('/{user}', [SettingsController::class, 'updateUser'])->name('update');
            Route::delete('/{user}', [SettingsController::class, 'destroyUser'])->name('destroy');
        });
    });

});

// --- CUSTOMER-FACING MODULE ROUTES ---
// Protected by customer authentication middleware

Route::middleware('auth:customer')->group(function () {
    
    // Customer Dashboard
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');

    Route::prefix('customer')->name('customer.')->group(function () {
        
        // Customer Bookings Routes
        Route::resource('bookings', CustomerBookingController::class, ['as' => 'customer'])->except(['edit', 'update', 'destroy']);

        // Customer Invoices Routes  
        Route::resource('invoices', CustomerInvoiceController::class, ['as' => 'customer'])->only(['index', 'show']);

        // Customer Quotes Routes
        Route::resource('quotes', CustomerQuoteController::class, ['as' => 'customer'])->only(['index', 'show', 'create', 'store']);
        Route::post('quotes/{quote}/accept', [CustomerQuoteController::class, 'accept'])->name('quotes.accept');
        Route::post('quotes/{quote}/reject', [CustomerQuoteController::class, 'reject'])->name('quotes.reject');

        // Customer Notifications Routes
        Route::get('notifications', [CustomerNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/mark-read/{notification}', [CustomerNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('notifications/mark-all-read', [CustomerNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::post('notifications/clear-all', [CustomerNotificationController::class, 'clearAll'])->name('notifications.clearAll');

        // Customer Payment Methods Routes
        Route::resource('payment_methods', CustomerPaymentMethodController::class, ['as' => 'customer'])->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('payment_methods/{customer_payment_method}/set-default', [CustomerPaymentMethodController::class, 'setDefault'])->name('payment_methods.setDefault');

        // Customer Profile Routes
        Route::get('profile', [CustomerProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [CustomerProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/update-personal', [CustomerProfileController::class, 'updatePersonal'])->name('profile.updatePersonal');
        Route::post('profile/update-addresses', [CustomerProfileController::class, 'updateAddresses'])->name('profile.updateAddresses');
        Route::post('profile/add-service-address', [CustomerProfileController::class, 'addServiceAddress'])->name('profile.addServiceAddress');
        Route::post('profile/update-service-address/{addressId}', [CustomerProfileController::class, 'updateServiceAddress'])->name('profile.updateServiceAddress');
        Route::post('profile/remove-service-address/{addressId}', [CustomerProfileController::class, 'removeServiceAddress'])->name('profile.removeServiceAddress');

    });

});

// --- DRIVER-FACING MODULE ROUTES ---
// Protected by driver authentication middleware

Route::middleware('auth:driver')->group(function () {
    
    // Driver Dashboard
    Route::get('/driver/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');

    Route::prefix('driver')->name('driver.')->group(function () {
        
        // Driver Assigned Routes/Jobs
        Route::resource('assigned_routes', DriverAssignedRouteController::class, ['as' => 'driver'])->only(['index', 'show']);
        Route::post('assigned_routes/{booking}/mark-arrived', [DriverAssignedRouteController::class, 'markArrived'])->name('assigned_routes.markArrived');
        Route::post('assigned_routes/{booking}/complete-job', [DriverAssignedRouteController::class, 'completeJob'])->name('assigned_routes.completeJob');
        Route::post('assigned_routes/{booking}/report-problem', [DriverAssignedRouteController::class, 'reportProblem'])->name('assigned_routes.reportProblem');

        // Driver Schedule Routes
        Route::get('schedule', [DriverScheduleController::class, 'index'])->name('schedule.index');
        Route::post('schedule/time-off-request', [DriverScheduleController::class, 'submitTimeOffRequest'])->name('schedule.submitTimeOffRequest');
        Route::get('schedule/calendar', [DriverScheduleController::class, 'calendar'])->name('schedule.calendar');

        // Vehicle Inspection Routes (DVIR)
        Route::resource('vehicle_inspection', VehicleInspectionController::class, ['as' => 'driver'])->only(['index', 'store', 'show']);

        // Driver Log Routes (HOS)
        Route::get('driver_log', [DriverLogController::class, 'index'])->name('driver_log.index');
        Route::post('driver_log', [DriverLogController::class, 'store'])->name('driver_log.store');
        Route::get('driver_log/past', [DriverLogController::class, 'showPastLogs'])->name('driver_log.past_logs');

        // Driver Messages Routes
        Route::get('messages', [DriverMessageController::class, 'index'])->name('messages.index');
        Route::post('messages', [DriverMessageController::class, 'store'])->name('messages.store');
        Route::post('messages/mark-read/{message}', [DriverMessageController::class, 'markAsRead'])->name('messages.markAsRead');
        Route::post('messages/{user}/mark-conversation-read', [DriverMessageController::class, 'markConversationAsRead'])->name('messages.markConversationAsRead');
        Route::post('messages/start-new-conversation', [DriverMessageController::class, 'startNewConversation'])->name('messages.startNewConversation');

        // Driver Profile Routes
        Route::get('profile', [DriverProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [DriverProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [DriverProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/update-personal', [DriverProfileController::class, 'updatePersonal'])->name('profile.updatePersonal');
        Route::post('profile/update-license', [DriverProfileController::class, 'updateLicense'])->name('profile.updateLicense');
        Route::post('profile/update-certifications', [DriverProfileController::class, 'updateCertifications'])->name('profile.updateCertifications');

    });

});

// --- API ROUTES FOR AJAX CALLS ---
// These can be called from authenticated pages

Route::middleware(['web'])->prefix('api')->name('api.')->group(function () {
    
    // Equipment and pricing calculations
    Route::get('equipment/{equipment}/pricing', [EquipmentController::class, 'getPricing'])->name('equipment.pricing');
    
    // Dynamic calculations for quotes and bookings
    Route::post('calculate-booking-price', [BookingController::class, 'calculatePrice'])->name('calculate.booking.price');
    Route::post('calculate-quote-total', [QuoteController::class, 'calculateQuoteTotal'])->name('calculate.quote.total');
    
    // Customer search for vendor forms
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('drivers/search', [DriverController::class, 'search'])->name('drivers.search');
    
    // Real-time notifications (if implemented)
    Route::get('notifications/unread-count', function() {
        return response()->json(['count' => 0]); // Placeholder
    })->name('notifications.unread.count');

});

// --- FALLBACK ROUTES ---

// Catch-all for undefined routes - redirect based on authentication
Route::fallback(function () {
    if (auth('vendor')->check()) {
        return redirect()->route('vendor.dashboard');
    } elseif (auth('customer')->check()) {
        return redirect()->route('customer.dashboard');
    } elseif (auth('driver')->check()) {
        return redirect()->route('driver.dashboard');
    } else {
        return redirect()->route('home');
    }
});