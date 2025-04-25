<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;

class ProductImageService
{
    public function createImage(Product $product, array $data): ProductImage
    {
        return $product->images()->create($data);
    }

    public function updateImage(ProductImage $image, array $data): bool
    {
        return $image->update($data);
    }

    public function deleteImage(ProductImage $image): bool
    {
        return $image->delete();
    }

    public function getImage(int $id): ?ProductImage
    {
        return ProductImage::find($id);
    }

    public function getProductImages(Product $product)
    {
        return $product->images()->orderBy('order')->get();
    }

    public function reorderImages(Product $product, array $imageIds): void
    {
        foreach ($imageIds as $order => $id) {
            $product->images()->where('id', $id)->update(['order' => $order]);
        }
    }
} 