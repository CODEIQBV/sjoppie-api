<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockLogService
{
    public function __construct()
    {
        // Initialize service
    }

    public function logStockChange(
        Product $product,
        int $quantityChange,
        string $action,
        ?string $reason = null,
        ?array $metadata = null,
        ?User $user = null
    ): StockLog {
        return DB::transaction(function () use ($product, $quantityChange, $action, $reason, $metadata, $user) {
            $stock = $product->stock;
            
            if (!$stock) {
                throw new \InvalidArgumentException('Stock record not found for product');
            }

            $previousQuantity = $stock->available;
            $newQuantity = $previousQuantity + $quantityChange;

            if ($newQuantity < 0) {
                throw new \InvalidArgumentException('Stock cannot be negative');
            }

            $stockLog = StockLog::create([
                'product_id' => $product->id,
                'user_id' => $user?->id,
                'quantity_change' => $quantityChange,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'action' => $action,
                'reason' => $reason,
                'metadata' => $metadata,
            ]);

            return $stockLog;
        });
    }

    public function logInitialStock(Product $product, int $initialQuantity, ?User $user = null): StockLog
    {
        return $this->logStockChange(
            product: $product,
            quantityChange: $initialQuantity,
            action: 'initial',
            reason: 'Initial stock setup',
            user: $user
        );
    }

    public function logStockAddition(Product $product, int $quantity, ?string $reason = null, ?User $user = null): StockLog
    {
        return $this->logStockChange(
            product: $product,
            quantityChange: $quantity,
            action: 'add',
            reason: $reason,
            user: $user
        );
    }

    public function logStockRemoval(Product $product, int $quantity, ?string $reason = null, ?User $user = null): StockLog
    {
        return $this->logStockChange(
            product: $product,
            quantityChange: -$quantity,
            action: 'remove',
            reason: $reason,
            user: $user
        );
    }

    public function logStockAdjustment(Product $product, int $newQuantity, ?string $reason = null, ?User $user = null): StockLog
    {
        $quantityChange = $newQuantity - $product->quantity;
        
        return $this->logStockChange(
            product: $product,
            quantityChange: $quantityChange,
            action: 'adjust',
            reason: $reason,
            user: $user
        );
    }
} 