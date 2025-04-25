<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariant;

class ProductStockService
{
    public function createStock(Product|ProductVariant $stockable, array $data): ProductStock
    {
        return $stockable->stock()->create($data);
    }

    public function updateStock(ProductStock $stock, array $data): bool
    {
        return $stock->update($data);
    }

    public function deleteStock(ProductStock $stock): bool
    {
        return $stock->delete();
    }

    public function getStock(Product|ProductVariant $stockable): ?ProductStock
    {
        return $stockable->stock;
    }

    public function updateAvailableStock(ProductStock $stock, int $quantity): bool
    {
        return $stock->update(['available' => $quantity]);
    }

    public function updateOnHandStock(ProductStock $stock, int $quantity): bool
    {
        return $stock->update(['on_hand' => $quantity]);
    }

    public function updateReservedStock(ProductStock $stock, int $quantity): bool
    {
        return $stock->update(['reserved' => $quantity]);
    }
} 