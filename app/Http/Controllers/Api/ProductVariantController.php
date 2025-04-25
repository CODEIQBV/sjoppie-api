<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductVariantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProductVariantController extends Controller
{
    public function __construct(
        private readonly ProductVariantService $variantService
    ) {}

    public function index(Product $product): JsonResponse
    {
        try {
            $variants = $this->variantService->getProductVariants($product);
            return response()->json($variants);
        } catch (Throwable $e) {
            Log::error('Error fetching variants: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching variants',
                'status' => 500,
            ], 500);
        }
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'option1_name' => 'nullable|string|max:255',
                'option1_value' => 'nullable|string|max:255',
                'option2_name' => 'nullable|string|max:255',
                'option2_value' => 'nullable|string|max:255',
                'option3_name' => 'nullable|string|max:255',
                'option3_value' => 'nullable|string|max:255',
                'sku' => 'nullable|string|unique:product_variants,sku',
                'barcode' => 'nullable|string|unique:product_variants,barcode',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $variant = $this->variantService->createVariant($product, $validator->validated());
            return response()->json($variant, 201);
        } catch (Throwable $e) {
            Log::error('Error creating variant: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the variant',
                'status' => 500,
            ], 500);
        }
    }

    public function show(Product $product, int $variant): JsonResponse
    {
        try {
            $variant = $this->variantService->getVariant($variant);

            if (!$variant || $variant->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant not found',
                ], 404);
            }

            return response()->json($variant);
        } catch (Throwable $e) {
            Log::error('Error fetching variant: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the variant',
                'status' => 500,
            ], 500);
        }
    }

    public function update(Request $request, Product $product, int $variant): JsonResponse
    {
        try {
            $variant = $this->variantService->getVariant($variant);

            if (!$variant || $variant->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'option1_name' => 'nullable|string|max:255',
                'option1_value' => 'nullable|string|max:255',
                'option2_name' => 'nullable|string|max:255',
                'option2_value' => 'nullable|string|max:255',
                'option3_name' => 'nullable|string|max:255',
                'option3_value' => 'nullable|string|max:255',
                'sku' => 'nullable|string|unique:product_variants,sku,' . $variant->id,
                'barcode' => 'nullable|string|unique:product_variants,barcode,' . $variant->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->variantService->updateVariant($variant, $validator->validated());
            return response()->json($variant->fresh());
        } catch (Throwable $e) {
            Log::error('Error updating variant: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the variant',
                'status' => 500,
            ], 500);
        }
    }

    public function destroy(Product $product, int $variant): JsonResponse
    {
        try {
            $variant = $this->variantService->getVariant($variant);

            if (!$variant || $variant->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant not found',
                ], 404);
            }

            $this->variantService->deleteVariant($variant);
            return response()->json(null, 204);
        } catch (Throwable $e) {
            Log::error('Error deleting variant: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while deleting the variant',
                'status' => 500,
            ], 500);
        }
    }
} 