<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProductImageController extends Controller
{
    public function __construct(
        private readonly ProductImageService $imageService
    ) {}

    public function index(Product $product): JsonResponse
    {
        try {
            $images = $this->imageService->getProductImages($product);
            return response()->json($images);
        } catch (Throwable $e) {
            Log::error('Error fetching images: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching images',
                'status' => 500,
            ], 500);
        }
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'path' => 'required|string|url',
                'alt_text' => 'nullable|string|max:255',
                'order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $image = $this->imageService->createImage($product, $validator->validated());
            return response()->json($image, 201);
        } catch (Throwable $e) {
            Log::error('Error creating image: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the image',
                'status' => 500,
            ], 500);
        }
    }

    public function show(Product $product, int $image): JsonResponse
    {
        try {
            $image = $this->imageService->getImage($image);

            if (!$image || $image->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found',
                ], 404);
            }

            return response()->json($image);
        } catch (Throwable $e) {
            Log::error('Error fetching image: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the image',
                'status' => 500,
            ], 500);
        }
    }

    public function update(Request $request, Product $product, int $image): JsonResponse
    {
        try {
            $image = $this->imageService->getImage($image);

            if (!$image || $image->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'path' => 'sometimes|string|url',
                'alt_text' => 'nullable|string|max:255',
                'order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->imageService->updateImage($image, $validator->validated());
            return response()->json($image->fresh());
        } catch (Throwable $e) {
            Log::error('Error updating image: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the image',
                'status' => 500,
            ], 500);
        }
    }

    public function destroy(Product $product, int $image): JsonResponse
    {
        try {
            $image = $this->imageService->getImage($image);

            if (!$image || $image->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found',
                ], 404);
            }

            $this->imageService->deleteImage($image);
            return response()->json(null, 204);
        } catch (Throwable $e) {
            Log::error('Error deleting image: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while deleting the image',
                'status' => 500,
            ], 500);
        }
    }

    public function reorder(Request $request, Product $product): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'image_ids' => 'required|array',
                'image_ids.*' => 'required|integer|exists:product_images,id,product_id,' . $product->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->imageService->reorderImages($product, $validator->validated()['image_ids']);
            return response()->json($this->imageService->getProductImages($product));
        } catch (Throwable $e) {
            Log::error('Error reordering images: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while reordering images',
                'status' => 500,
            ], 500);
        }
    }
} 