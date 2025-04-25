<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProductPriceController extends Controller
{
    public function __construct(
        private readonly ProductPriceService $priceService
    ) {}

    public function index(Product $product): JsonResponse
    {
        try {
            $prices = $this->priceService->getPrices($product);
            return response()->json($prices);
        } catch (Throwable $e) {
            Log::error('Error fetching prices: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching prices',
                'status' => 500,
            ], 500);
        }
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'price' => 'required|numeric|min:0',
                'compare_at_price' => 'nullable|numeric|min:0',
                'taxable' => 'nullable|boolean',
                'currency' => 'required|string|size:3',
                'starts_at' => 'required|date',
                'ends_at' => 'nullable|date|after:starts_at',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $price = $this->priceService->createPrice($product, $validator->validated());
            return response()->json($price, 201);
        } catch (Throwable $e) {
            Log::error('Error creating price: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the price',
                'status' => 500,
            ], 500);
        }
    }

    public function show(Product $product, int $price): JsonResponse
    {
        try {
            $price = $this->priceService->getPrice($price);

            if (!$price || $price->priceable_id !== $product->id || $price->priceable_type !== Product::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Price not found',
                ], 404);
            }

            return response()->json($price);
        } catch (Throwable $e) {
            Log::error('Error fetching price: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the price',
                'status' => 500,
            ], 500);
        }
    }

    public function update(Request $request, Product $product, int $price): JsonResponse
    {
        try {
            $price = $this->priceService->getPrice($price);

            if (!$price || $price->priceable_id !== $product->id || $price->priceable_type !== Product::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Price not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'price' => 'sometimes|numeric|min:0',
                'compare_at_price' => 'nullable|numeric|min:0',
                'taxable' => 'nullable|boolean',
                'currency' => 'sometimes|string|size:3',
                'starts_at' => 'sometimes|date',
                'ends_at' => 'nullable|date|after:starts_at',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->priceService->updatePrice($price, $validator->validated());
            return response()->json($price->fresh());
        } catch (Throwable $e) {
            Log::error('Error updating price: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the price',
                'status' => 500,
            ], 500);
        }
    }

    public function destroy(Product $product, int $price): JsonResponse
    {
        try {
            $price = $this->priceService->getPrice($price);

            if (!$price || $price->priceable_id !== $product->id || $price->priceable_type !== Product::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Price not found',
                ], 404);
            }

            $this->priceService->deletePrice($price);
            return response()->json(null, 204);
        } catch (Throwable $e) {
            Log::error('Error deleting price: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while deleting the price',
                'status' => 500,
            ], 500);
        }
    }

    public function current(Product $product): JsonResponse
    {
        try {
            $price = $this->priceService->getCurrentPrice($product);
            return response()->json($price);
        } catch (Throwable $e) {
            Log::error('Error fetching current price: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the current price',
                'status' => 500,
            ], 500);
        }
    }
} 