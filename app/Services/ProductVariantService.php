<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantService
{
    public function createVariant(Product $product, array $data): ProductVariant
    {
        return $product->variants()->create($data);
    }

    public function updateVariant(ProductVariant $variant, array $data): bool
    {
        return $variant->update($data);
    }

    public function deleteVariant(ProductVariant $variant): bool
    {
        return $variant->delete();
    }

    public function getVariant(int $id): ?ProductVariant
    {
        return ProductVariant::with(['prices', 'stock'])->find($id);
    }

    public function getProductVariants(Product $product)
    {
        return $product->variants()->with(['prices', 'stock'])->get();
    }
}
