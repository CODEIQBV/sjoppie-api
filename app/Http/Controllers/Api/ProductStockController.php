<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProductStockController extends Controller
{
    public function __construct(
        private readonly ProductStockService $stockService
    ) {}

    public function show(Product $product): JsonResponse
    {
        try {
            $stock = $this->stockService->getStock($product);

            if (!$stock) {
                // Create default stock record if none exists
                $stock = $this->stockService->createStock($product, [
                    'available' => 0,
                    'on_hand' => 0,
                    'reserved' => 0,
                ]);
            }

            return response()->json($stock);
        } catch (Throwable $e) {
            Log::error('Error fetching stock: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the stock',
                'status' => 500,
            ], 500);
        }
    }

    public function showAvailable(Product $product): JsonResponse
    {
        try {
            $stock = $this->stockService->getStock($product);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            return response()->json([
                'available' => $stock->available
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching available stock: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the available stock',
                'status' => 500,
            ], 500);
        }
    }

    public function showOnHand(Product $product): JsonResponse
    {
        try {
            $stock = $this->stockService->getStock($product);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            return response()->json([
                'on_hand' => $stock->on_hand
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching on-hand stock: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the on-hand stock',
                'status' => 500,
            ], 500);
        }
    }

    public function showReserved(Product $product): JsonResponse
    {
        try {
            $stock = $this->stockService->getStock($product);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            return response()->json([
                'reserved' => $stock->reserved
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching reserved stock: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the reserved stock',
                'status' => 500,
            ], 500);
        }
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        try {
            $stock = $this->stockService->getStock($product);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'available' => 'sometimes|integer|min:0',
                'on_hand' => 'sometimes|integer|min:0',
                'reserved' => 'sometimes|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->stockService->updateStock($stock, $validator->validated());
            return response()->json($stock->fresh());
        } catch (Throwable $e) {
            Log::error('Error updating stock: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the stock',
                'status' => 500,
            ], 500);
        }
    }

    public function adjustAvailable(Request $request, Product $product): JsonResponse
    {
        try {
            $stock = $this->stockService->getStock($product);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
                'reason' => 'sometimes|string|max:255',
                'action' => 'sometimes|string|in:add,remove,adjust',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->stockService->adjustAvailableStock(
                stock: $stock,
                quantity: $validator->validated()['quantity'],
                reason: $validator->validated()['reason'] ?? null,
                action: $validator->validated()['action'] ?? null
            );

            return response()->json($stock->fresh());
        } catch (Throwable $e) {
            Log::error('Error adjusting available stock: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while adjusting the available stock',
                'status' => 500,
            ], 500);
        }
    }

    public function adjustOnHand(Request $request, Product $product): JsonResponse
    {
        try {
            $stock = $this->stockService->getStock($product);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
                'reason' => 'sometimes|string|max:255',
                'action' => 'sometimes|string|in:add,remove,adjust',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->stockService->adjustOnHandStock(
                stock: $stock,
                quantity: $validator->validated()['quantity'],
                reason: $validator->validated()['reason'] ?? null,
                action: $validator->validated()['action'] ?? null
            );

            return response()->json($stock->fresh());
        } catch (Throwable $e) {
            Log::error('Error adjusting on-hand stock: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while adjusting the on-hand stock',
                'status' => 500,
            ], 500);
        }
    }

    public function adjustReserved(Request $request, Product $product): JsonResponse
    {
        try {
            $stock = $this->stockService->getStock($product);

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
                'reason' => 'sometimes|string|max:255',
                'action' => 'sometimes|string|in:add,remove,adjust',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->stockService->adjustReservedStock(
                stock: $stock,
                quantity: $validator->validated()['quantity'],
                reason: $validator->validated()['reason'] ?? null,
                action: $validator->validated()['action'] ?? null
            );

            return response()->json($stock->fresh());
        } catch (Throwable $e) {
            Log::error('Error adjusting reserved stock: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while adjusting the reserved stock',
                'status' => 500,
            ], 500);
        }
    }
} 