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
    StoreController,
    PaymentGatewayController
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
    Route::prefix('products/{product:id}')->group(function () {
        Route::apiResource('variants', ProductVariantController::class);

        // Product Prices
        Route::get('prices', [ProductPriceController::class, 'index']);
        Route::post('prices', [ProductPriceController::class, 'store']);
        Route::get('prices/current', [ProductPriceController::class, 'current']);
        Route::get('prices/{price}', [ProductPriceController::class, 'show']);
        Route::put('prices/{price}', [ProductPriceController::class, 'update']);
        Route::delete('prices/{price}', [ProductPriceController::class, 'destroy']);

        // Product Stock
        Route::get('stock', [ProductStockController::class, 'show']);
        Route::get('stock/available', [ProductStockController::class, 'showAvailable']);
        Route::get('stock/on-hand', [ProductStockController::class, 'showOnHand']);
        Route::get('stock/reserved', [ProductStockController::class, 'showReserved']);
        Route::put('stock', [ProductStockController::class, 'update']);
        Route::put('stock/available/adjust', [ProductStockController::class, 'adjustAvailable']);
        Route::put('stock/on-hand/adjust', [ProductStockController::class, 'adjustOnHand']);
        Route::put('stock/reserved/adjust', [ProductStockController::class, 'adjustReserved']);

        // Product Images
        Route::get('images', [ProductImageController::class, 'index']);
        Route::post('images', [ProductImageController::class, 'store']);
        Route::get('images/{image}', [ProductImageController::class, 'show']);
        Route::put('images/{image}', [ProductImageController::class, 'update']);
        Route::delete('images/{image}', [ProductImageController::class, 'destroy']);
        Route::post('images/reorder', [ProductImageController::class, 'reorder']);

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

    // Payment Gateways
    Route::get('payment-gateways/modules', [PaymentGatewayController::class, 'availableModules']);
    Route::apiResource('payment-gateways', PaymentGatewayController::class);
});
