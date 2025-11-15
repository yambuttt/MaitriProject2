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

Route::view('/', 'pages.landing')->name('landing');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');

Route::get('/product/{product:slug}', [CatalogController::class, 'show'])
    ->name('catalog.product.show');




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