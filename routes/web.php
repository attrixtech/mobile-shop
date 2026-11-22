<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ImeiController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RepairJobController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarrantyController;
use Illuminate\Support\Facades\Route;

// Public Root & Storefront
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('shop.index');
});

Route::get('/shop', [StorefrontController::class, 'index'])->name('shop.index');
Route::get('/shop/{id}', [StorefrontController::class, 'show'])->name('shop.show');
Route::get('/store', [StorefrontController::class, 'index'])->name('storefront.index');
Route::get('/store/{id}', [StorefrontController::class, 'show'])->name('storefront.show');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated ERP App
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/switch-branch', [AuthController::class, 'switchBranch'])->name('branch.switch');

    // POS & Sales
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search-products', [PosController::class, 'searchProducts'])->name('pos.search');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{id}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{id}/thermal', [SaleController::class, 'thermal'])->name('sales.thermal');
    Route::get('/sales/{id}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::get('/sales/{id}/return', [SaleController::class, 'createReturn'])->name('sales.return');
    Route::post('/sales/{id}/return', [SaleController::class, 'processReturn'])->name('sales.return.post');

    // Products & Catalog
    Route::resource('products', ProductController::class);

    // IMEI & Serial Number Tracking
    Route::get('/imei', [ImeiController::class, 'index'])->name('imei.index');
    Route::get('/imei/{id}', [ImeiController::class, 'show'])->name('imei.show');
    Route::post('/imei', [ImeiController::class, 'store'])->name('imei.store');
    Route::post('/imei/{id}/status', [ImeiController::class, 'updateStatus'])->name('imei.update-status');

    // Purchases & Suppliers
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::post('/purchases/{id}/payment', [PurchaseController::class, 'recordPayment'])->name('purchases.payment');
    Route::resource('suppliers', SupplierController::class);

    // Inventory & Stock
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('/inventory/transfers', [InventoryController::class, 'transfers'])->name('inventory.transfers');
    Route::post('/inventory/transfers', [InventoryController::class, 'storeTransfer'])->name('inventory.transfers.store');
    Route::get('/inventory/adjustments', [InventoryController::class, 'adjustments'])->name('inventory.adjustments');
    Route::post('/inventory/adjustments', [InventoryController::class, 'storeAdjustment'])->name('inventory.adjustments.store');

    // Mobile Exchange & Used Phones
    Route::get('/exchange', [ExchangeController::class, 'index'])->name('exchange.index');
    Route::get('/exchange/create', [ExchangeController::class, 'create'])->name('exchange.create');
    Route::post('/exchange', [ExchangeController::class, 'store'])->name('exchange.store');
    Route::post('/exchange/used-device/{id}', [ExchangeController::class, 'updateUsedDevice'])->name('exchange.used-device.update');

    // Repairs & Service Center
    Route::get('/repairs', [RepairJobController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/create', [RepairJobController::class, 'create'])->name('repairs.create');
    Route::post('/repairs', [RepairJobController::class, 'store'])->name('repairs.store');
    Route::post('/repairs/technicians', [RepairJobController::class, 'storeTechnician'])->name('repairs.technicians.store');
    Route::get('/repairs/{id}', [RepairJobController::class, 'show'])->name('repairs.show');
    Route::post('/repairs/{id}/status', [RepairJobController::class, 'updateStatus'])->name('repairs.status');
    Route::post('/repairs/{id}/add-item', [RepairJobController::class, 'addItem'])->name('repairs.add-item');
    Route::delete('/repairs/{id}/items/{itemId}', [RepairJobController::class, 'removeItem'])->name('repairs.remove-item');
    Route::get('/repairs/{id}/print', [RepairJobController::class, 'print'])->name('repairs.print');

    // Warranty Tracking
    Route::get('/warranty', [WarrantyController::class, 'index'])->name('warranty.index');

    // Expenses & Accounting
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/accounting', [AccountingController::class, 'index'])->name('accounting.index');
    Route::get('/accounting/journal', [AccountingController::class, 'journalEntries'])->name('accounting.journal');
    Route::get('/accounting/cash-register', [AccountingController::class, 'cashRegister'])->name('accounting.cash-register');
    Route::post('/accounting/cash-register/open', [AccountingController::class, 'openRegister'])->name('accounting.cash-register.open');
    Route::post('/accounting/cash-register/{id}/close', [AccountingController::class, 'closeRegister'])->name('accounting.cash-register.close');

    // Customers CRM
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::resource('customers', CustomerController::class);

    // Reports & Analytics
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/profitability', [ReportController::class, 'profitability'])->name('reports.profitability');
    Route::get('/reports/gst', [ReportController::class, 'gst'])->name('reports.gst');

    // Tenant & Branch Settings
    Route::get('/settings/company', [SettingController::class, 'company'])->name('settings.company');
    Route::post('/settings/company', [SettingController::class, 'updateCompany'])->name('settings.company.update');
    Route::get('/settings/branches', [SettingController::class, 'branches'])->name('settings.branches');
    Route::post('/settings/branches', [SettingController::class, 'storeBranch'])->name('settings.branches.store');
    Route::get('/settings/users', [SettingController::class, 'users'])->name('settings.users');
    Route::post('/settings/users', [SettingController::class, 'storeUser'])->name('settings.users.store');
});

// SaaS Super Admin
Route::middleware(['auth'])->prefix('superadmin')->group(function () {
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/tenants', [SuperAdminController::class, 'tenants'])->name('superadmin.tenants');
    Route::post('/tenants/{id}', [SuperAdminController::class, 'updateTenantStatus'])->name('superadmin.tenants.update');
    Route::get('/plans', [SuperAdminController::class, 'plans'])->name('superadmin.plans');
    Route::post('/plans', [SuperAdminController::class, 'storePlan'])->name('superadmin.plans.store');
});
