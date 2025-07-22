<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorRegistrationController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\JunkRemovalJobController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BrandingController;
use App\Http\Controllers\DispatchController;

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
    return view('home'); // Your new homepage
});

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


// --- MAIN VENDOR MODULE ROUTES ---
// These would typically be wrapped in an auth middleware group for authenticated vendors
// For now, they are open for easier development access.

// Equipment Management Routes
Route::resource('equipment', EquipmentController::class);

// Customer Management (Vendor's view of customers) Routes
Route::resource('customers', CustomerController::class);

// Booking Management (Vendor's view of bookings) Routes
Route::resource('bookings', BookingController::class);
// Route for dynamic price calculation (if you use AJAX for this)
Route::get('/api/bookings/calculate-price', [BookingController::class, 'calculatePrice'])->name('api.bookings.calculatePrice');

// Quotes Management (Vendor's view of quotes) Routes
Route::resource('quotes', QuoteController::class);
// Route for converting quote to booking/invoice
Route::post('quotes/{quote}/convert', [QuoteController::class, 'convertToBookingAndInvoice'])->name('quotes.convert');
// Route for dynamic quote total calculation
Route::get('/api/quotes/calculate-total', [QuoteController::class, 'calculateQuoteTotal'])->name('api.quotes.calculateTotal');

// Invoices Management (Vendor's view of invoices) Routes
Route::resource('invoices', InvoiceController::class);
// Route for quick mark as paid
Route::post('invoices/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.markAsPaid');

// Payments Management (Vendor's view of payments) Routes
Route::resource('payments', PaymentController::class);

// Junk Removal Jobs Management Routes
Route::resource('junk_removal_jobs', JunkRemovalJobController::class);

// Branding Module Routes
Route::get('/branding', [BrandingController::class, 'show'])->name('branding.show');
Route::post('/branding', [BrandingController::class, 'update'])->name('branding.update');

// Dispatching & Route Optimization Module Routes
Route::prefix('dispatching')->name('dispatching.')->group(function () {
    Route::get('/', [DispatchController::class, 'show'])->name('show'); // Main dispatcher dashboard
    Route::post('/assign', [DispatchController::class, 'assignJob'])->name('assign'); // For assigning/unassigning jobs
    Route::post('/simulate-optimization', [DispatchController::class, 'simulateRouteOptimization'])->name('simulateOptimization'); // For conceptual optimization
});

// Financials Module Routes (Overview, Reports, and Expense Management)
Route::prefix('financials')->name('financials.')->group(function () {
    Route::get('/overview', [FinancialController::class, 'overview'])->name('overview');
    Route::get('/reports', [FinancialController::class, 'reports'])->name('reports');
    // Expense Management Sub-module Routes
    Route::resource('expenses', ExpenseController::class); // This resource is nested under /financials/
});

// Settings Module Routes (Vendor's settings and internal user management)
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'show'])->name('show'); // Main settings page, handles tabs
    Route::post('/update-profile', [SettingsController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-notifications', [SettingsController::class, 'updateNotifications'])->name('updateNotifications');
    Route::post('/update-integrations', [SettingsController::class, 'updateIntegrations'])->name('updateIntegrations'); // Conceptual
    // Internal Users (Staff) Management (nested under settings)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/create', [SettingsController::class, 'createUser'])->name('create');
        Route::post('/', [SettingsController::class, 'storeUser'])->name('store');
        Route::get('/{user}/edit', [SettingsController::class, 'editUser'])->name('edit');
        Route::put('/{user}', [SettingsController::class, 'updateUser'])->name('update'); // Use PUT for update
        Route::delete('/{user}', [SettingsController::class, 'destroyUser'])->name('destroy'); // Use DELETE for destroy
    });
});


// --- CUSTOMER-FACING MODULE ROUTES (All protected by auth:customer middleware) ---
Route::middleware('auth:customer')->group(function () {
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');

    // Customer Bookings Routes
    Route::resource('customer/bookings', CustomerBookingController::class)->except(['edit', 'update', 'destroy']);

    // Customer Invoices Routes
    Route::resource('customer/invoices', CustomerInvoiceController::class)->only(['index', 'show']);

    // Customer Quotes Routes
    Route::resource('customer/quotes', CustomerQuoteController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('customer/quotes/{quote}/accept', [CustomerQuoteController::class, 'accept'])->name('customer.quotes.accept');
    Route::post('customer/quotes/{quote}/reject', [CustomerQuoteController::class, 'reject'])->name('customer.quotes.reject');

    // Customer Notifications Routes
    Route::get('/customer/notifications', [CustomerNotificationController::class, 'index'])->name('customer.notifications.index');
    Route::post('/customer/notifications/mark-read/{notification}', [CustomerNotificationController::class, 'markAsRead'])->name('customer.notifications.markAsRead');
    Route::post('/customer/notifications/mark-all-read', [CustomerNotificationController::class, 'markAllAsRead'])->name('customer.notifications.markAllAsRead');
    Route::post('/customer/notifications/clear-all', [CustomerNotificationController::class, 'clearAll'])->name('customer.notifications.clearAll');

    // Customer Payment Methods Routes
    Route::resource('customer/payment_methods', CustomerPaymentMethodController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('customer/payment_methods/{customer_payment_method}/set-default', [CustomerPaymentMethodController::class, 'setDefault'])->name('customer.payment_methods.setDefault');

    // Customer Profile Module Routes
    Route::get('customer/profile', [CustomerProfileController::class, 'show'])->name('customer.profile.show');
    Route::post('customer/profile/update-personal', [CustomerProfileController::class, 'updatePersonal'])->name('customer.profile.updatePersonal');
    Route::post('customer/profile/update-addresses', [CustomerProfileController::class, 'updateAddresses'])->name('customer.profile.updateAddresses');
    Route::post('customer/profile/add-service-address', [CustomerProfileController::class, 'addServiceAddress'])->name('customer.profile.addServiceAddress');
    Route::post('customer/profile/update-service-address/{addressId}', [CustomerProfileController::class, 'updateServiceAddress'])->name('customer.profile.updateServiceAddress');
    Route::post('customer/profile/remove-service-address/{addressId}', [CustomerProfileController::class, 'removeServiceAddress'])->name('customer.profile.removeServiceAddress');

});

// --- DRIVER-FACING MODULE ROUTES (All protected by auth:driver middleware) ---
Route::middleware('auth:driver')->group(function () {
    Route::get('/driver/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');

    Route::prefix('driver')->name('driver.')->group(function () {
        Route::resource('assigned_routes', DriverAssignedRouteController::class)->only(['index', 'show']);
        Route::post('assigned_routes/{booking}/mark-arrived', [DriverAssignedRouteController::class, 'markArrived'])->name('assigned_routes.markArrived');
        Route::post('assigned_routes/{booking}/complete-job', [DriverAssignedRouteController::class, 'completeJob'])->name('assigned_routes.completeJob');
        Route::post('assigned_routes/{booking}/report-problem', [DriverAssignedRouteController::class, 'reportProblem'])->name('assigned_routes.reportProblem');

        Route::get('schedule', [DriverScheduleController::class, 'index'])->name('schedule.index');
        Route::post('schedule/time-off-request', [DriverScheduleController::class, 'submitTimeOffRequest'])->name('schedule.submitTimeOffRequest');

        Route::resource('vehicle_inspection', VehicleInspectionController::class)->only(['index', 'store', 'show']);

        Route::get('driver_log', [DriverLogController::class, 'index'])->name('driver_log.index');
        Route::post('driver_log', [DriverLogController::class, 'store'])->name('driver_log.store');
        Route::get('driver_log/past', [DriverLogController::class, 'showPastLogs'])->name('driver_log.past_logs');

        Route::get('messages', [DriverMessageController::class, 'index'])->name('messages.index');
        Route::post('messages', [DriverMessageController::class, 'store'])->name('messages.store');
        Route::post('messages/mark-read/{message}', [DriverMessageController::class, 'markAsRead'])->name('messages.markAsRead');
        Route::post('messages/{user}/mark-conversation-read', [DriverMessageController::class, 'markConversationAsRead'])->name('messages.markConversationAsRead');
        Route::post('messages/start-new-conversation', [DriverMessageController::class, 'startNewConversation'])->name('messages.startNewConversation');

        Route::get('profile', [DriverProfileController::class, 'show'])->name('profile.show');
        Route::post('profile/update-personal', [DriverProfileController::class, 'updatePersonal'])->name('profile.updatePersonal');
        Route::post('profile/update-license', [DriverProfileController::class, 'updateLicense'])->name('profile.updateLicense');
    });
});