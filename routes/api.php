<?php

use App\Http\Controllers\Api\{
    ProductController,
    ProductVariantController,
    ProductPriceController,
    ProductStockController,
    ProductImageController,
    CategoryController,
    TagController
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
    // Products
    Route::apiResource('products', ProductController::class);
    
    // Product Variants
    Route::prefix('products/{product}')->group(function () {
        Route::apiResource('variants', ProductVariantController::class);
        
        // Product Prices
        Route::prefix('variants/{variant}')->group(function () {
            Route::apiResource('prices', ProductPriceController::class);
            Route::apiResource('stock', ProductStockController::class);
        });
        
        // Product Images
        Route::apiResource('images', ProductImageController::class);
        Route::post('images/reorder', [ProductImageController::class, 'reorder']);
        
        // Product Categories & Tags
        Route::post('categories/sync', [ProductController::class, 'syncCategories']);
        Route::post('tags/sync', [ProductController::class, 'syncTags']);
    });
    
    // Categories
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/tree', [CategoryController::class, 'tree']);
    
    // Tags
    Route::apiResource('tags', TagController::class);
});
