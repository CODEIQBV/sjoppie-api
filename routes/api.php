<?php

use App\Http\Controllers\Api\{
    ProductController,
    ProductVariantController,
    ProductPriceController,
    ProductStockController,
    ProductImageController,
    CategoryController,
    TagController,
    CustomerController,
    AddressController,
    StoreController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['api.key', 'api.response'])->group(function () {
    // Store
    Route::get('store', [StoreController::class, 'show']);
    Route::put('store', [StoreController::class, 'update']);

    // Products
    Route::apiResource('products', ProductController::class);

    // Product Variants
    Route::prefix('products/{product}')->group(function () {
        Route::apiResource('variants', ProductVariantController::class);

        // Product Categories & Tags
        Route::post('categories/sync', [ProductController::class, 'syncCategories']);
        Route::post('tags/sync', [ProductController::class, 'syncTags']);
    });

    // Customers
    Route::apiResource('customers', CustomerController::class);
    Route::get('customers/search', [CustomerController::class, 'index']);

    // Customer Addresses
    Route::prefix('customers/{customer}')->group(function () {
        Route::apiResource('addresses', AddressController::class);
        Route::get('addresses/default/{type}', [AddressController::class, 'getDefault']);
    });
});
