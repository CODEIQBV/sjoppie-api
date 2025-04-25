<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Get all products with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllProducts(int $perPage = 10): LengthAwarePaginator
    {
        return Product::query()
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
        return Product::where(is_numeric($identifier) ? 'id' : 'slug', $identifier)->first();
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

        return Product::create($data);
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
} 