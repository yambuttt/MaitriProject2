<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminSubcategoryController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminProductVariantController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AdminDigiflazzController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaydisiniCallbackController;
use App\Http\Controllers\UserWalletController;
use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Marketplace\MarketplaceCheckoutController;
use App\Http\Controllers\Marketplace\MarketplacePaymentController;
use App\Http\Controllers\Admin\AdminMarketplaceOrderController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AdminRefundController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\DashboardAffiliateController;
use App\Http\Controllers\AdminAffiliateController;

use App\Http\Controllers\AdminAffiliateLevelController;


Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');

Route::get('/product/{product:slug}', [CatalogController::class, 'show'])
    ->name('catalog.product.show');
Route::post('/paydisini/callback', [PaydisiniCallbackController::class, 'handle'])
    ->name('paydisini.callback');


// Password reset (OTP via email)
Route::get('/forgot-password', [PasswordResetController::class, 'showRequest'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'sendCode'])->name('password.forgot.send');

Route::get('/forgot-password/verify', [PasswordResetController::class, 'showVerify'])->name('password.forgot.verify');
Route::post('/forgot-password/verify', [PasswordResetController::class, 'verifyCode'])->name('password.forgot.verify.post');

Route::get('/reset-password', [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset.post');



Route::get('/test-log', function () {
    Log::info('Test log endpoint kepanggil');
    return 'OKE LOG';
});

Route::get('/test-email', function () {
    \Illuminate\Support\Facades\Mail::raw('Email test dari server berhasil!', function ($m) {
        $m->to('adrian.arifin777@gmail.com')
            ->subject('Test SMTP MaitriProject');
    });

    return 'OK';
});

// Route::get('/catalog',                [CatalogController::class,'index'])->name('catalog.index');
// Route::get('/product/{product:slug}', [CatalogController::class,'show'])->name('catalog.product.show');

// Auth pages (UI)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// Auth actions
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::post('/register', [AuthController::class, 'register'])->name('register.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboards
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');  // views/dashboard/user/index.blade.php
    Route::get('/admin', [DashboardController::class, 'admin'])
        ->middleware('admin')
        ->name('admin.dashboard');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/checkout/saldo', [CheckoutController::class, 'checkoutSaldo'])->name('checkout.saldo');




});
Route::get('/invoices/{order}', [CheckoutController::class, 'show'])
    ->name('orders.show');
Route::get('/invoices/{code}', [CheckoutController::class, 'showByCode'])
    ->name('invoices.show');

Route::post('/checkout/paydisini', [CheckoutController::class, 'checkoutPaydisini'])
    ->name('checkout.paydisini');

// halaman pembayaran order (QR / VA / kode minimarket)
Route::get('/orders/{order}/payment/{payment}', [CheckoutController::class, 'showPaydisiniPayment'])
    ->name('orders.payment.show');

// AJAX polling status pembayaran ke Paydisini
Route::get('/orders/payment/{payment}/status', [CheckoutController::class, 'checkPaymentStatus'])
    ->name('orders.payment.status');
Route::post('/orders/payment/{payment}/expire', [CheckoutController::class, 'expirePayment'])
    ->name('orders.payment.expire');

Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/wallet', [UserWalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/pin', [UserWalletController::class, 'updatePin'])->name('wallet.pin.update');

    // ganti route topup lama dengan yang pakai Paydisini
    Route::post('/wallet/topup', [UserWalletController::class, 'topupPaydisini'])
        ->name('wallet.topup');


    Route::get('/wallet', [UserWalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/pin', [UserWalletController::class, 'updatePin'])->name('wallet.pin.update');

    // route topup manual (dev only), nanti bisa kamu batasi ke admin
    Route::post('/wallet/topup', [UserWalletController::class, 'topupPaydisini'])->name('wallet.topup');
    Route::get('/wallet/topup/{topup}', [UserWalletController::class, 'showTopup'])
        ->name('wallet.topup.show');
    Route::get('/wallet/topup/{topup}/status', [UserWalletController::class, 'checkTopupStatus'])
        ->name('wallet.topup.status');
    Route::post('/wallet/topup/{topup}/expire', [UserWalletController::class, 'expireTopup'])
        ->name('wallet.topup.expire');
    // baru — riwayat pembelian produk topup
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');

    // baru — riwayat marketplace
    Route::get('/marketplace-orders', [DashboardController::class, 'marketplaceOrders'])->name('marketplace.orders');
    Route::get('/affiliate', [DashboardAffiliateController::class, 'index'])->name('affiliate');
    Route::post('/affiliate/apply', [DashboardAffiliateController::class, 'apply'])->name('affiliate.apply');


});


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/orders/search', [DashboardController::class, 'searchOrderByCode'])
            ->name('orders.search');
        // routes/web.php (di dalam group admin)
        Route::get('/orders/{code}/detail', [DashboardController::class, 'orderDetail'])
            ->name('orders.detail');
        Route::get('/refunds', [AdminRefundController::class, 'index'])->name('refunds.index');
        Route::get('/refunds/create', [AdminRefundController::class, 'create'])->name('refunds.create');
        Route::post('/refunds/check', [AdminRefundController::class, 'check'])->name('refunds.check');
        Route::post('/refunds', [AdminRefundController::class, 'store'])->name('refunds.store');
        Route::get('/refunds/users/search', [AdminRefundController::class, 'searchUsers'])->name('refunds.users.search');


        Route::get('/affiliates/applications', [AdminAffiliateController::class, 'applications'])->name('affiliates.applications');
        Route::post('/affiliates/applications/{application}/approve', [AdminAffiliateController::class, 'approve'])->name('affiliates.applications.approve');
        Route::post('/affiliates/applications/{application}/reject', [AdminAffiliateController::class, 'reject'])->name('affiliates.applications.reject');

        Route::get('/affiliates', [AdminAffiliateController::class, 'index'])->name('admin.affiliates.index');
        Route::get('/affiliates/{user}', [AdminAffiliateController::class, 'show'])->name('admin.affiliates.show');
        Route::get('/affiliates', [AdminAffiliateController::class, 'index'])
            ->name('affiliates.index');

        Route::get('/affiliates/{user}', [AdminAffiliateController::class, 'show'])
            ->name('affiliates.show');

        // Affiliate Levels (reward settings)
        Route::get('/affiliate-levels', [AdminAffiliateLevelController::class, 'index'])
            ->name('affiliate-levels.index');

        Route::get('/affiliate-levels/create', [AdminAffiliateLevelController::class, 'create'])
            ->name('affiliate-levels.create');

        Route::post('/affiliate-levels', [AdminAffiliateLevelController::class, 'store'])
            ->name('affiliate-levels.store');

        Route::get('/affiliate-levels/{level}/edit', [AdminAffiliateLevelController::class, 'edit'])
            ->name('affiliate-levels.edit');

        Route::put('/affiliate-levels/{level}', [AdminAffiliateLevelController::class, 'update'])
            ->name('affiliate-levels.update');

        Route::post('/affiliate-levels/{level}/toggle', [AdminAffiliateLevelController::class, 'toggle'])
            ->name('affiliate-levels.toggle');



        // ...
    
        Route::get('/digiflazz', [AdminDigiflazzController::class, 'index'])
            ->name('digiflazz.index');

        Route::post('/digiflazz/sync-master', [AdminDigiflazzController::class, 'syncMaster'])
            ->name('digiflazz.sync-master');

        Route::get('/digiflazz/debug-pricelist', [AdminDigiflazzController::class, 'debugPricelist'])
            ->name('digiflazz.debug-pricelist');

        Route::get('/marketplace/orders', [\App\Http\Controllers\AdminMarketplaceOrderController::class, 'index'])
            ->name('marketplace.orders.index');

        Route::get('/marketplace/orders/{order}', [\App\Http\Controllers\AdminMarketplaceOrderController::class, 'show'])
            ->name('marketplace.orders.show');

        Route::post('/marketplace/orders/{order}/status', [\App\Http\Controllers\AdminMarketplaceOrderController::class, 'updateStatus'])
            ->name('marketplace.orders.update-status');
        // ============================
        // Marketplace Catalog (admin)
        // ============================
    
        // Kategori
        Route::get('/marketplace/categories', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'index'])
            ->name('marketplace.categories.index');
        Route::get('/marketplace/categories/create', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'create'])
            ->name('marketplace.categories.create');
        Route::post('/marketplace/categories', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'store'])
            ->name('marketplace.categories.store');
        Route::get('/marketplace/categories/{category}/edit', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'edit'])
            ->name('marketplace.categories.edit');
        Route::post('/marketplace/categories/{category}', [\App\Http\Controllers\AdminMarketplaceCategoryController::class, 'update'])
            ->name('marketplace.categories.update');

        // Produk
        Route::get('/marketplace/products', [\App\Http\Controllers\AdminMarketplaceProductController::class, 'index'])
            ->name('marketplace.products.index');
        Route::get('/marketplace/products/create', [\App\Http\Controllers\AdminMarketplaceProductController::class, 'create'])
            ->name('marketplace.products.create');
        Route::post('/marketplace/products', [\App\Http\Controllers\AdminMarketplaceProductController::class, 'store'])
            ->name('marketplace.products.store');
        Route::get('/marketplace/products/{product}/edit', [\App\Http\Controllers\AdminMarketplaceProductController::class, 'edit'])
            ->name('marketplace.products.edit');
        Route::post('/marketplace/products/{product}', [\App\Http\Controllers\AdminMarketplaceProductController::class, 'update'])
            ->name('marketplace.products.update');

        // Variants per product
        Route::get('/marketplace/products/{product}/variants', [\App\Http\Controllers\AdminMarketplaceVariantController::class, 'index'])
            ->name('marketplace.variants.index');
        Route::get('/marketplace/products/{product}/variants/create', [\App\Http\Controllers\AdminMarketplaceVariantController::class, 'create'])
            ->name('marketplace.variants.create');
        Route::post('/marketplace/products/{product}/variants', [\App\Http\Controllers\AdminMarketplaceVariantController::class, 'store'])
            ->name('marketplace.variants.store');
        Route::get('/marketplace/products/{product}/variants/{variant}/edit', [\App\Http\Controllers\AdminMarketplaceVariantController::class, 'edit'])
            ->name('marketplace.variants.edit');
        Route::post('/marketplace/products/{product}/variants/{variant}', [\App\Http\Controllers\AdminMarketplaceVariantController::class, 'update'])
            ->name('marketplace.variants.update');
        // Produk
        Route::delete('/marketplace/products/{product}', [\App\Http\Controllers\AdminMarketplaceProductController::class, 'destroy'])
            ->name('marketplace.products.destroy');

        // Variants per product
        Route::delete('/marketplace/products/{product}/variants/{variant}', [\App\Http\Controllers\AdminMarketplaceVariantController::class, 'destroy'])
            ->name('marketplace.variants.destroy');



    });



Route::middleware(['auth'])->group(function () {
    Route::middleware('admin')->group(function () {
        // Kategori
        Route::get('/admin/catalog/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/admin/catalog/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/admin/catalog/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/admin/catalog/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
        Route::patch('/admin/catalog/categories/{category}/toggle', [AdminCategoryController::class, 'toggle'])->name('admin.categories.toggle');
        Route::delete('/admin/catalog/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

        Route::get('/admin/catalog/subcategories', [AdminSubcategoryController::class, 'index'])->name('admin.subcategories.index');
        Route::post('/admin/catalog/subcategories', [AdminSubcategoryController::class, 'store'])->name('admin.subcategories.store');
        Route::get('/admin/catalog/subcategories/{subcategory}/edit', [AdminSubcategoryController::class, 'edit'])->name('admin.subcategories.edit');
        Route::put('/admin/catalog/subcategories/{subcategory}', [AdminSubcategoryController::class, 'update'])->name('admin.subcategories.update');
        Route::patch('/admin/catalog/subcategories/{subcategory}/toggle', [AdminSubcategoryController::class, 'toggle'])->name('admin.subcategories.toggle');
        Route::delete('/admin/catalog/subcategories/{subcategory}', [AdminSubcategoryController::class, 'destroy'])->name('admin.subcategories.destroy');



        Route::get('/admin/catalog/products', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::get('/admin/catalog/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
        Route::post('/admin/catalog/products', [AdminProductController::class, 'store'])->name('admin.products.store');
        Route::get('/admin/catalog/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/admin/catalog/products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
        Route::patch('/admin/catalog/products/{product}/toggle', [AdminProductController::class, 'toggle'])->name('admin.products.toggle');
        Route::delete('/admin/catalog/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');

        // helper JSON
        Route::get('/admin/catalog/subcategories/by-category/{category}', [AdminProductController::class, 'subcategoriesByCategory'])
            ->name('admin.ajax.subcategories.byCategory');


        Route::get('/admin/catalog/products/{product}/variants', [AdminProductVariantController::class, 'index'])->name('admin.products.variants.index');
        Route::post('/admin/catalog/products/{product}/variants', [AdminProductVariantController::class, 'store'])->name('admin.products.variants.store');
        Route::get('/admin/catalog/products/{product}/variants/{variant}/edit', [AdminProductVariantController::class, 'edit'])->name('admin.products.variants.edit');
        Route::put('/admin/catalog/products/{product}/variants/{variant}', [AdminProductVariantController::class, 'update'])->name('admin.products.variants.update');
        Route::patch('/admin/catalog/products/{product}/variants/{variant}/toggle', [AdminProductVariantController::class, 'toggle'])->name('admin.products.variants.toggle');
        Route::delete('/admin/catalog/products/{product}/variants/{variant}', [AdminProductVariantController::class, 'destroy'])->name('admin.products.variants.destroy');
        Route::patch('/admin/catalog/products/{product}/variants/{variant}/sort', [AdminProductVariantController::class, 'updateSort'])->name('admin.products.variants.sort');
        Route::patch('/admin/catalog/products/{product}/variants/{variant}/pin', [AdminProductVariantController::class, 'pinToTop'])->name('admin.products.variants.pin');

        // Digiflazz tools
        Route::get('/admin/catalog/products/{product}/variants/digiflazz/search', [AdminProductVariantController::class, 'searchDigiflazz'])->name('admin.products.variants.digiflazz.search');
        Route::post('/admin/catalog/products/{product}/variants/digiflazz/import', [AdminProductVariantController::class, 'importFromDigiflazz'])->name('admin.products.variants.digiflazz.import');
        Route::post('/admin/catalog/products/{product}/variants/digiflazz/sync', [AdminProductVariantController::class, 'syncFromDigiflazz'])->name('admin.products.variants.digiflazz.sync');
        Route::patch(
            '/admin/catalog/products/{product}/variants/bulk',
            [AdminProductVariantController::class, 'bulkUpdate']
        )->name('admin.products.variants.bulk');
        Route::prefix('admin/digiflazz')->name('admin.digiflazz.')->group(function () {
            Route::post('/sync-master', [AdminDigiflazzController::class, 'syncMaster'])
                ->name('sync-master');


            Route::post('/sync-variant-prices', [AdminDigiflazzController::class, 'syncVariantPrices'])
                ->name('sync-variant-prices');
        });



    });
});

Route::prefix('marketplace')->name('marketplace.')->group(function () {

    // halaman list produk marketplace
    Route::get('/', [MarketplaceController::class, 'index'])
        ->name('index');

    // halaman detail produk marketplace
    Route::get('/product/{product:slug}', [MarketplaceController::class, 'show'])
        ->name('product.show');

    // proses submit checkout (post dari halaman detail)
    Route::post('/product/{product:slug}/checkout', [MarketplaceCheckoutController::class, 'createOrder'])
        ->name('checkout.create');

    // halaman checkout detail (setelah create order)
    Route::get('/orders/{order:invoice_number}/checkout', [MarketplaceCheckoutController::class, 'showCheckout'])
        ->name('checkout.show');

    // submit form checkout (email, phone, metode pembayaran)
    Route::post('/orders/{order:invoice_number}/checkout', [MarketplaceCheckoutController::class, 'processCheckout'])
        ->name('checkout.process');

    // halaman pembayaran khusus marketplace
    Route::get('/payment/{payment}', [MarketplacePaymentController::class, 'showPaymentPage'])
        ->name('payment.show');

    // endpoint polling status pembayaran (AJAX)
    Route::get('/payment/{payment}/status', [MarketplacePaymentController::class, 'checkPaymentStatus'])
        ->name('payment.status');

    // halaman invoice marketplace
    Route::get('/invoices/{order:invoice_number}', [MarketplaceController::class, 'invoice'])
        ->name('invoice.show');
});

// routes/web.php

