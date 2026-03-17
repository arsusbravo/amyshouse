<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/about', function () {
    return Inertia::render('About', [
        'content' => [
            'zh-TW' => \App\Models\SiteContent::allForLocale('zh-TW'),
            'en' => \App\Models\SiteContent::allForLocale('en'),
        ],
    ]);
})->name('about');

/*
|--------------------------------------------------------------------------
| Auth routes (guest only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/cart', fn () => Inertia::render('Cart'))->name('cart');
    Route::get('/checkout', fn () => Inertia::render('Checkout'))->name('checkout');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        // === View routes (pages) ===

        // Products
        Route::get('products', [Admin\ProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [Admin\ProductController::class, 'create'])->name('products.create');
        Route::get('products/{product}', [Admin\ProductController::class, 'show'])->name('products.show');
        Route::get('products/{product}/edit', [Admin\ProductController::class, 'edit'])->name('products.edit');

        // Clients
        Route::get('clients', [Admin\ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/create', [Admin\ClientController::class, 'create'])->name('clients.create');
        Route::get('clients/{client}', [Admin\ClientController::class, 'show'])->name('clients.show');
        Route::get('clients/{client}/edit', [Admin\ClientController::class, 'edit'])->name('clients.edit');

        // Orders
        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');

        // Content
        Route::get('content', [Admin\ContentController::class, 'index'])->name('content.index');

        // Attributes
        Route::get('attributes', [Admin\AttributeController::class, 'index'])->name('attributes.index');

        // === API routes (mutations) ===

        Route::prefix('api')->name('api.')->group(function () {
            // Products
            Route::post('products', [Admin\Api\ProductController::class, 'store'])->name('products.store');
            Route::put('products/{product}', [Admin\Api\ProductController::class, 'update'])->name('products.update');
            Route::delete('products/{product}', [Admin\Api\ProductController::class, 'destroy'])->name('products.destroy');
            Route::delete('product-images/{image}', [Admin\Api\ProductController::class, 'destroyImage'])->name('products.image.destroy');
            Route::post('product-images/{image}/primary', [Admin\Api\ProductController::class, 'setPrimaryImage'])->name('products.image.primary');

            // Clients
            Route::post('clients', [Admin\Api\ClientController::class, 'store'])->name('clients.store');
            Route::put('clients/{client}', [Admin\Api\ClientController::class, 'update'])->name('clients.update');
            Route::post('clients/{client}/toggle-active', [Admin\Api\ClientController::class, 'toggleActive'])->name('clients.toggle-active');
            Route::post('clients/import-csv', [Admin\Api\ClientController::class, 'importCsv'])->name('clients.import-csv');

            // content
            Route::put('content', [Admin\Api\ContentController::class, 'update'])->name('content.update');
            Route::post('content', [Admin\Api\ContentController::class, 'store'])->name('content.store');
            Route::delete('content/{key}', [Admin\Api\ContentController::class, 'destroy'])->name('content.destroy');

            // Orders
            Route::patch('orders/{order}/status', [Admin\Api\OrderController::class, 'updateStatus'])->name('orders.update-status');

            // Attributes
            Route::post('attributes/types', [Admin\Api\AttributeController::class, 'storeType'])->name('attributes.types.store');
            Route::put('attributes/types/{type}', [Admin\Api\AttributeController::class, 'updateType'])->name('attributes.types.update');
            Route::delete('attributes/types/{type}', [Admin\Api\AttributeController::class, 'destroyType'])->name('attributes.types.destroy');

            Route::post('attributes/materials', [Admin\Api\AttributeController::class, 'storeMaterial'])->name('attributes.materials.store');
            Route::put('attributes/materials/{material}', [Admin\Api\AttributeController::class, 'updateMaterial'])->name('attributes.materials.update');
            Route::delete('attributes/materials/{material}', [Admin\Api\AttributeController::class, 'destroyMaterial'])->name('attributes.materials.destroy');

            Route::post('attributes/colors', [Admin\Api\AttributeController::class, 'storeColor'])->name('attributes.colors.store');
            Route::put('attributes/colors/{color}', [Admin\Api\AttributeController::class, 'updateColor'])->name('attributes.colors.update');
            Route::delete('attributes/colors/{color}', [Admin\Api\AttributeController::class, 'destroyColor'])->name('attributes.colors.destroy');
        });
    });

/*
|--------------------------------------------------------------------------
| Settings routes (all authenticated users)
|--------------------------------------------------------------------------
*/

require __DIR__.'/settings.php';