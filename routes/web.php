<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\DrugController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Dokter\DashboardController as DokterDashboard;
use App\Http\Controllers\Dokter\VisitController;
use App\Http\Controllers\Dokter\BillingController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Kasir\POSController;
use App\Http\Controllers\Kasir\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboard;
use App\Http\Controllers\Portal\PortalController;
use Illuminate\Support\Facades\Route;

// Home redirect
Route::get('/', fn() => redirect()->route('login'));

// Auth routes (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Admin/Owner routes
    Route::prefix('admin')->name('admin.')->middleware('role:owner,admin')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        
        // Master Data
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('drugs', DrugController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::post('categories', [ProductCategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category}', [ProductCategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [ProductCategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Customers
        Route::resource('customers', CustomerController::class);
        
        // Users
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Stock
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/adjust', [StockController::class, 'adjust'])->name('stock.adjust');
        Route::get('/stock/movements', [StockController::class, 'movements'])->name('stock.movements');
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
            Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
            Route::get('/payments', [ReportController::class, 'payments'])->name('payments');
            Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
        });

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/company', [SettingController::class, 'updateCompany'])->name('settings.company');
        Route::post('/settings/tax', [SettingController::class, 'updateTax'])->name('settings.tax');
        Route::post('/settings/payment-methods', [SettingController::class, 'updatePaymentMethods'])->name('settings.payment-methods');
        Route::post('/settings/numbering', [SettingController::class, 'updateNumbering'])->name('settings.numbering');
    });

    // Dokter routes
    Route::prefix('dokter')->name('dokter.')->middleware('role:owner,dokter')->group(function () {
        Route::get('/dashboard', [DokterDashboard::class, 'index'])->name('dashboard');
        Route::resource('visits', VisitController::class)->except(['destroy']);
        Route::post('visits/{visit}/complete', [VisitController::class, 'complete'])->name('visits.complete');
        Route::post('visits/{visit}/items', [VisitController::class, 'addItem'])->name('visits.items.add');
        Route::delete('visits/{visit}/items/{item}', [VisitController::class, 'removeItem'])->name('visits.items.remove');
        Route::get('search-customer', [VisitController::class, 'searchCustomer'])->name('search-customer');
        Route::resource('billings', BillingController::class)->except(['destroy', 'edit', 'update']);
        Route::post('billings/{billing}/items', [BillingController::class, 'addItem'])->name('billings.items.add');
        Route::delete('billings/{billing}/items/{item}', [BillingController::class, 'removeItem'])->name('billings.items.remove');
        Route::post('billings/{billing}/complete', [BillingController::class, 'complete'])->name('billings.complete');
    });

    // Kasir routes
    Route::prefix('kasir')->name('kasir.')->middleware('role:owner,kasir')->group(function () {
        Route::get('/dashboard', [KasirDashboard::class, 'index'])->name('dashboard');
        
        // POS
        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::post('/pos/order', [POSController::class, 'createOrder'])->name('pos.create-order');
        Route::post('/pos/order/{order}/items', [POSController::class, 'addItem'])->name('pos.add-item');
        Route::delete('/pos/order/{order}/items/{item}', [POSController::class, 'removeItem'])->name('pos.remove-item');
        Route::post('/pos/order/{order}/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
        Route::get('/pos/receipt/{order}', [POSController::class, 'receipt'])->name('pos.receipt');
        
        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/process', [PaymentController::class, 'process'])->name('payments.process');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    });

    // Invoices (shared)
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
        Route::post('/{invoice}/email', [InvoiceController::class, 'email'])->name('email');
    });

    // Customer Portal
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/dashboard', [PortalDashboard::class, 'index'])->name('dashboard');
        Route::get('/pets', [PortalController::class, 'pets'])->name('pets');
        Route::get('/pets/{pet}', [PortalController::class, 'petShow'])->name('pets.show');
        Route::get('/visits', [PortalController::class, 'visits'])->name('visits');
        Route::get('/visits/{visit}', [PortalController::class, 'visitShow'])->name('visits.show');
        Route::get('/invoices', [PortalController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}', [PortalController::class, 'invoiceShow'])->name('invoices.show');
        Route::get('/invoices/{invoice}/download', [PortalController::class, 'invoiceDownload'])->name('invoices.download');
        Route::get('/prescriptions', [PortalController::class, 'prescriptions'])->name('prescriptions');
        Route::get('/prescriptions/{prescription}', [PortalController::class, 'prescriptionShow'])->name('prescriptions.show');
        Route::get('/profile', [PortalController::class, 'profile'])->name('profile.edit');
        Route::put('/profile', [PortalController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [PortalController::class, 'changePassword'])->name('password.change');
    });
});
