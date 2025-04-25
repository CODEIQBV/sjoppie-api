<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * Display a listing of the products.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 10);
            $products = $this->productService->getAllProducts($perPage);

            return response()->json($products);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching products',
                'status' => 500,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }

            return response()->json($response, 500);
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'seo_title' => 'nullable|string|max:255',
                'seo_description' => 'nullable|string',
                'slug' => 'nullable|string|max:255',
                'status' => 'required|in:active,concept',
                
                // Stock data
                'stock' => 'nullable|array',
                'stock.available' => 'nullable|integer|min:0',
                'stock.on_hand' => 'nullable|integer|min:0',
                'stock.reserved' => 'nullable|integer|min:0',
                
                // Price data
                'price' => 'nullable|array',
                'price.amount' => 'nullable|numeric|min:0',
                'price.currency' => 'nullable|string|size:3',
                
                // Images data
                'images' => 'nullable|array',
                'images.*.url' => 'required|string|url',
                'images.*.alt' => 'nullable|string|max:255',
                'images.*.order' => 'nullable|integer|min:0',
                
                // Variants data
                'variants' => 'nullable|array',
                'variants.*.title' => 'required|string|max:255',
                'variants.*.option1_name' => 'nullable|string|max:255',
                'variants.*.option1_value' => 'nullable|string|max:255',
                'variants.*.option2_name' => 'nullable|string|max:255',
                'variants.*.option2_value' => 'nullable|string|max:255',
                'variants.*.option3_name' => 'nullable|string|max:255',
                'variants.*.option3_value' => 'nullable|string|max:255',
                'variants.*.sku' => 'nullable|string|max:255',
                'variants.*.barcode' => 'nullable|string|max:255',
                
                // Tags data
                'tags' => 'nullable|array',
                'tags.*' => 'required|string|max:255',
                
                // Categories data
                'categories' => 'nullable|array',
                'categories.*' => 'required|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $product = $this->productService->createProduct($validator->validated());

            return response()->json($product, 201);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the product',
                'status' => 500,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }

            return response()->json($response, 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(string $product): JsonResponse
    {
        try {
            $product = $this->productService->getProduct($product);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json($product);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the product',
                'status' => 500,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }

            return response()->json($response, 500);
        }
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, string $product): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'seo_title' => 'nullable|string|max:255',
                'seo_description' => 'nullable|string',
                'slug' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:active,concept',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $product = $this->productService->updateProduct($product, $validator->validated());

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json($product);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the product',
                'status' => 500,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }

            return response()->json($response, 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(string $product): JsonResponse
    {
        try {
            $deleted = $this->productService->deleteProduct($product);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json(null, 204);
        } catch (Throwable $e) {
            Log::error('Error deleting product: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while deleting the product',
                'status' => 500,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }

            return response()->json($response, 500);
        }
    }
}
