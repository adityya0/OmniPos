<?php

use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\PrinterSettingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Dropshipper\CatalogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return \Inertia\Inertia::render('Welcome');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modul Operasional Toko & Supplier (Diproteksi Khusus Pemilik Toko / Staf Toko)
    Route::middleware('supplier')->group(function () {
        // Modul Kasir Toko (Point of Sales Offline)
        Route::get('/admin/pos', [PosController::class, 'index'])->name('admin.pos.index');
        Route::post('/admin/pos/checkout', [PosController::class, 'checkout'])->name('admin.pos.checkout');
        Route::post('/admin/pos/pricing-mode', [PosController::class, 'updatePricingMode'])->name('admin.pos.pricing-mode');

        // Modul Manajemen Produk & Inventory (Admin & Tim Media)
        Route::post('/admin/categories', [ProductController::class, 'storeCategory'])->name('admin.categories.store');
        Route::get('/admin/products/generate-sku', [ProductController::class, 'generateSku'])->name('admin.products.generate-sku');
        Route::get('/admin/products/template', [ProductController::class, 'downloadTemplate'])->name('admin.products.template');
        Route::post('/admin/products/import', [ProductController::class, 'import'])->name('admin.products.import');
        Route::post('/admin/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('admin.products.bulk-delete');
        Route::post('/admin/products/bulk-restock', [ProductController::class, 'bulkRestock'])->name('admin.products.bulk-restock');
        Route::post('/admin/products/tiers/bulk-update', [ProductController::class, 'bulkUpdateTiers'])->name('admin.products.tiers.bulk-update');
        Route::post('/admin/products/{id}/restock', [ProductController::class, 'quickRestock'])->name('admin.products.restock');
        Route::resource('/admin/products', ProductController::class)->names('admin.products');

        // Modul Analisis & Prediksi Stok Dinamis
        Route::get('/admin/stock-analytics', [\App\Http\Controllers\Admin\StockAnalyticsController::class, 'index'])->name('admin.stock-analytics.index');
        Route::put('/admin/stock-analytics/parameters', [\App\Http\Controllers\Admin\StockAnalyticsController::class, 'updateParameters'])->name('admin.stock-analytics.parameters');

        // Modul Akumulasi Penjualan, Riwayat & Laba Rugi
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::delete('/admin/reports/orders/{order}', [ReportController::class, 'destroyOrder'])->name('admin.reports.orders.destroy');
        Route::post('/admin/reports/orders/bulk-delete', [ReportController::class, 'bulkDestroyOrders'])->name('admin.reports.orders.bulk-delete');

        // Modul Pengaturan Printer & Kustomisasi Struk
        Route::get('/admin/printer', [PrinterSettingController::class, 'index'])->name('admin.printer.index');
        Route::put('/admin/printer', [PrinterSettingController::class, 'update'])->name('admin.printer.update');

        // Modul Pusat Pengaturan & Profil Toko / Kasir
        Route::get('/admin/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings.index');
        Route::put('/admin/settings/pos-pricing-mode', [\App\Http\Controllers\Admin\SettingController::class, 'updatePosPricingMode'])->name('admin.settings.pos-pricing-mode');
        Route::put('/admin/settings/store-profile', [\App\Http\Controllers\Admin\SettingController::class, 'updateStoreProfile'])->name('admin.settings.store-profile');

        // Modul Antrean Packing Dropship
        Route::get('/admin/packing', [\App\Http\Controllers\Admin\PackingQueueController::class, 'index'])->name('admin.packing.index');
        Route::match(['get', 'post'], '/admin/packing/{order}/approve', [\App\Http\Controllers\Admin\PackingQueueController::class, 'approve'])->name('admin.packing.approve');
        Route::match(['get', 'post'], '/admin/packing/{order}/reject', [\App\Http\Controllers\Admin\PackingQueueController::class, 'reject'])->name('admin.packing.reject');
        Route::match(['get', 'post'], '/admin/packing/{order}/cancel', [\App\Http\Controllers\Admin\PackingQueueController::class, 'cancel'])->name('admin.packing.cancel');
        Route::match(['get', 'post', 'patch'], '/admin/packing/{order}/status', [\App\Http\Controllers\Admin\PackingQueueController::class, 'updateStatus'])->name('admin.packing.status');

        // Modul Mitra Dropshipper
        Route::get('/admin/dropshippers', [\App\Http\Controllers\Admin\PartnerDropshipperController::class, 'index'])->name('admin.dropshippers.index');
        Route::post('/admin/dropshippers', [\App\Http\Controllers\Admin\PartnerDropshipperController::class, 'store'])->name('admin.dropshippers.store');
        Route::patch('/admin/dropshippers/{id}/status', [\App\Http\Controllers\Admin\PartnerDropshipperController::class, 'updateStatus'])->name('admin.dropshippers.status');
        Route::post('/admin/dropshippers/{id}/approve-disconnect', [\App\Http\Controllers\Admin\PartnerDropshipperController::class, 'approveDisconnect'])->name('admin.dropshippers.approve-disconnect');
        Route::post('/admin/dropshippers/{id}/reject-disconnect', [\App\Http\Controllers\Admin\PartnerDropshipperController::class, 'rejectDisconnect'])->name('admin.dropshippers.reject-disconnect');

        // Modul Keuangan & Saldo Dropship
        Route::get('/admin/dropship-finances', [\App\Http\Controllers\Admin\DropshipFinanceController::class, 'index'])->name('admin.dropship-finances.index');
        Route::get('/admin/dropship-finances/{id}', [\App\Http\Controllers\Admin\DropshipFinanceController::class, 'show'])->name('admin.dropship-finances.show');
        Route::post('/admin/dropship-finances/{id}/top-up', [\App\Http\Controllers\Admin\DropshipFinanceController::class, 'topUp'])->name('admin.dropship-finances.top-up');
        Route::post('/admin/dropship-finances/{id}/mark-paid', [\App\Http\Controllers\Admin\DropshipFinanceController::class, 'markAsPaid'])->name('admin.dropship-finances.mark-paid');

        // Modul Kelola Staf Toko (Tim Media & Tim Packing)
        Route::get('/admin/staff', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('admin.staff.index');
        Route::post('/admin/staff', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->name('admin.staff.store');
        Route::delete('/admin/staff/{staff}', [\App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('admin.staff.destroy');
    });

    // Modul Dropshipper: Profil, Ketentuan, Katalog Stok Realtime, Pilih Penyuplai (Maks 5) & Pesanan
    Route::get('/dropshipper/profile', [ProfileController::class, 'edit'])->name('dropshipper.profile');
    Route::get('/dropshipper/catalog', [CatalogController::class, 'index'])->name('dropshipper.catalog.index');
    Route::get('/dropshipper/suppliers', [\App\Http\Controllers\Dropshipper\SupplierPartnerController::class, 'index'])->name('dropshipper.suppliers.index');
    Route::post('/dropshipper/suppliers/{supplier}/connect', [\App\Http\Controllers\Dropshipper\SupplierPartnerController::class, 'connect'])->name('dropshipper.suppliers.connect');
    Route::post('/dropshipper/suppliers/{supplier}/disconnect', [\App\Http\Controllers\Dropshipper\SupplierPartnerController::class, 'disconnect'])->name('dropshipper.suppliers.disconnect');
    Route::post('/dropshipper/suppliers/{supplier}/request-disconnect', [\App\Http\Controllers\Dropshipper\SupplierPartnerController::class, 'requestDisconnect'])->name('dropshipper.suppliers.request-disconnect');
    Route::post('/dropshipper/suppliers/{supplier}/cancel-disconnect', [\App\Http\Controllers\Dropshipper\SupplierPartnerController::class, 'cancelDisconnect'])->name('dropshipper.suppliers.cancel-disconnect');
    Route::get('/dropshipper/orders', [\App\Http\Controllers\Dropshipper\OrderController::class, 'index'])->name('dropshipper.orders.index');
    Route::post('/dropshipper/orders', [\App\Http\Controllers\Dropshipper\OrderController::class, 'store'])->name('dropshipper.orders.store');
    Route::post('/dropshipper/orders/{order}/cancel', [\App\Http\Controllers\Dropshipper\OrderController::class, 'cancel'])->name('dropshipper.orders.cancel');
});

// Master Control Panel Khusus Pemilik Sistem (URL Terproteksi)
use App\Http\Controllers\MasterAdminController;

Route::prefix(MasterAdminController::MASTER_URL)->group(function () {
    Route::get('/', [MasterAdminController::class, 'showLogin'])->name('master.login');
    Route::post('/auth', [MasterAdminController::class, 'authenticate'])->name('master.auth');
    Route::get('/logout', [MasterAdminController::class, 'logout'])->name('master.logout');
    Route::get('/dashboard', [MasterAdminController::class, 'index'])->name('master.dashboard');
    Route::get('/login-as/{id}', [MasterAdminController::class, 'loginAs'])->name('master.loginAs');
    Route::post('/reset-password/{id}', [MasterAdminController::class, 'resetPassword'])->name('master.resetPassword');
    Route::post('/link-supplier', [MasterAdminController::class, 'linkSupplier'])->name('master.linkSupplier');
    Route::post('/unlink-supplier', [MasterAdminController::class, 'unlinkSupplier'])->name('master.unlinkSupplier');
    Route::post('/create-user', [MasterAdminController::class, 'storeUser'])->name('master.storeUser');
});

Route::get('/master-return', [MasterAdminController::class, 'exitImpersonate'])->name('master.exit');

require __DIR__.'/auth.php';

