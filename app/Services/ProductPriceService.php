<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductVariant;

class ProductPriceService
{
    public function createPrice(Product|ProductVariant $priceable, array $data): ProductPrice
    {
        return $priceable->prices()->create($data);
    }

    public function updatePrice(ProductPrice $price, array $data): bool
    {
        return $price->update($data);
    }

    public function deletePrice(ProductPrice $price): bool
    {
        return $price->delete();
    }

    public function getCurrentPrice(Product|ProductVariant $priceable): ?ProductPrice
    {
        return $priceable->prices()
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest()
            ->first();
    }

    public function getPrices(Product|ProductVariant $priceable)
    {
        return $priceable->prices()->orderBy('starts_at', 'desc')->get();
    }
} 