<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private readonly ProductVariantService $variantService,
        private readonly ProductPriceService $priceService,
        private readonly ProductStockService $stockService,
        private readonly ProductImageService $imageService,
        private readonly CategoryService $categoryService,
        private readonly TagService $tagService
    ) {}

    /**
     * Get all products with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllProducts(int $perPage = 10): LengthAwarePaginator
    {
        return Product::query()
            ->with(['variants', 'prices', 'stock', 'images', 'categories', 'tags'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get a single product by ID or slug.
     *
     * @param int|string $identifier
     * @return Product|null
     */
    public function getProduct(int|string $identifier): ?Product
    {
        return Product::where(is_numeric($identifier) ? 'id' : 'slug', $identifier)
            ->with(['variants', 'prices', 'stock', 'images', 'categories', 'tags'])
            ->first();
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle duplicate slugs
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $product = Product::create($data);

        // Create initial stock record if provided
        if (isset($data['stock'])) {
            $this->stockService->createStock($product, [
                'available' => $data['stock']['available'] ?? 0,
                'on_hand' => $data['stock']['on_hand'] ?? 0,
                'reserved' => $data['stock']['reserved'] ?? 0,
            ]);
        }

        // Create initial price record if provided
        if (isset($data['price'])) {
            $this->priceService->createPrice($product, [
                'price' => $data['price']['amount'] ?? 0,
                'currency' => $data['price']['currency'] ?? 'EUR',
                'starts_at' => now(),
            ]);
        }

        // Create images if provided
        if (isset($data['images'])) {
            foreach ($data['images'] as $imageData) {
                $this->imageService->createImage($product, [
                    'path' => $imageData['url'],
                    'alt_text' => $imageData['alt'] ?? null,
                    'order' => $imageData['order'] ?? 0,
                ]);
            }
        }

        // Create variants if provided
        if (isset($data['variants'])) {
            foreach ($data['variants'] as $variantData) {
                $variant = $this->variantService->createVariant($product, $variantData);
                
                // Create stock for variant if provided
                if (isset($variantData['stock'])) {
                    $this->stockService->createStock($variant, [
                        'available' => $variantData['stock']['available'] ?? 0,
                        'on_hand' => $variantData['stock']['on_hand'] ?? 0,
                        'reserved' => $variantData['stock']['reserved'] ?? 0,
                    ]);
                }
                
                // Create price for variant if provided
                if (isset($variantData['price'])) {
                    $this->priceService->createPrice($variant, [
                        'price' => $variantData['price']['amount'] ?? 0,
                        'currency' => $variantData['price']['currency'] ?? 'EUR',
                        'starts_at' => now(),
                    ]);
                }
            }
        }

        // Sync tags if provided
        if (isset($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $tagName) {
                $tag = $this->tagService->getTag($tagName) ?? $this->tagService->createTag(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $this->syncTags($product, $tagIds);
        }

        // Sync categories if provided
        if (isset($data['categories'])) {
            $this->syncCategories($product, $data['categories']);
        }

        return $product->load(['stock', 'prices', 'images', 'variants', 'tags', 'categories']);
    }

    /**
     * Update an existing product.
     *
     * @param int|string $identifier
     * @param array $data
     * @return Product|null
     */
    public function updateProduct(int|string $identifier, array $data): ?Product
    {
        $product = $this->getProduct($identifier);
        
        if ($product) {
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            $product->update($data);
        }

        return $product;
    }

    /**
     * Delete a product.
     *
     * @param int|string $identifier
     * @return bool
     */
    public function deleteProduct(int|string $identifier): bool
    {
        $product = $this->getProduct($identifier);
        
        if ($product) {
            $product->delete();
            return true;
        }

        return false;
    }

    public function syncCategories(Product $product, array $categoryIds): void
    {
        $product->categories()->sync($categoryIds);
    }

    public function syncTags(Product $product, array $tagIds): void
    {
        $product->tags()->sync($tagIds);
    }
} 