<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProductStockService
{
    public function __construct(
        private readonly StockLogService $stockLogService
    ) {}

    public function createStock(Product|ProductVariant $stockable, array $data): ProductStock
    {
        return DB::transaction(function () use ($stockable, $data) {
            $stock = $stockable->stock()->create($data);
            
            $this->stockLogService->logInitialStock(
                product: $stockable,
                initialQuantity: $data['available'] ?? 0,
                user: auth()->user()
            );

            return $stock;
        });
    }

    public function updateStock(ProductStock $stock, array $data): bool
    {
        return DB::transaction(function () use ($stock, $data) {
            $previousAvailable = $stock->available;
            $previousOnHand = $stock->on_hand;
            $previousReserved = $stock->reserved;

            $updated = $stock->update($data);

            if ($updated) {
                // Log changes in available stock
                if (isset($data['available']) && $data['available'] !== $previousAvailable) {
                    $this->stockLogService->logStockChange(
                        product: $stock->stockable,
                        quantityChange: $data['available'] - $previousAvailable,
                        action: 'adjust',
                        reason: 'Stock adjustment',
                        user: auth()->user()
                    );
                }

                // Log changes in on-hand stock
                if (isset($data['on_hand']) && $data['on_hand'] !== $previousOnHand) {
                    $this->stockLogService->logStockChange(
                        product: $stock->stockable,
                        quantityChange: $data['on_hand'] - $previousOnHand,
                        action: 'adjust',
                        reason: 'On-hand stock adjustment',
                        user: auth()->user()
                    );
                }

                // Log changes in reserved stock
                if (isset($data['reserved']) && $data['reserved'] !== $previousReserved) {
                    $this->stockLogService->logStockChange(
                        product: $stock->stockable,
                        quantityChange: $data['reserved'] - $previousReserved,
                        action: 'adjust',
                        reason: 'Reserved stock adjustment',
                        user: auth()->user()
                    );
                }
            }

            return $updated;
        });
    }

    public function deleteStock(ProductStock $stock): bool
    {
        return $stock->delete();
    }

    public function getStock(Product|ProductVariant $stockable): ?ProductStock
    {
        return $stockable->stock;
    }

    public function adjustAvailableStock(ProductStock $stock, int $quantity, ?string $reason = null, ?string $action = null): bool
    {
        return DB::transaction(function () use ($stock, $quantity, $reason, $action) {
            $previousAvailable = $stock->available;
            $newAvailable = $previousAvailable + $quantity;

            if ($newAvailable < 0) {
                throw new \InvalidArgumentException('Available stock cannot be negative');
            }

            $updated = $stock->update(['available' => $newAvailable]);

            if ($updated) {
                $this->stockLogService->logStockChange(
                    product: $stock->stockable,
                    quantityChange: $quantity,
                    action: $action ?? ($quantity > 0 ? 'add' : 'remove'),
                    reason: $reason ?? ($quantity > 0 ? 'Available stock addition' : 'Available stock removal'),
                    user: auth()->user()
                );
            }

            return $updated;
        });
    }

    public function adjustOnHandStock(ProductStock $stock, int $quantity, ?string $reason = null, ?string $action = null): bool
    {
        return DB::transaction(function () use ($stock, $quantity, $reason, $action) {
            $previousOnHand = $stock->on_hand;
            $newOnHand = $previousOnHand + $quantity;

            if ($newOnHand < 0) {
                throw new \InvalidArgumentException('On-hand stock cannot be negative');
            }

            $updated = $stock->update(['on_hand' => $newOnHand]);

            if ($updated) {
                $this->stockLogService->logStockChange(
                    product: $stock->stockable,
                    quantityChange: $quantity,
                    action: $action ?? ($quantity > 0 ? 'add' : 'remove'),
                    reason: $reason ?? ($quantity > 0 ? 'On-hand stock addition' : 'On-hand stock removal'),
                    user: auth()->user()
                );
            }

            return $updated;
        });
    }

    public function adjustReservedStock(ProductStock $stock, int $quantity, ?string $reason = null, ?string $action = null): bool
    {
        return DB::transaction(function () use ($stock, $quantity, $reason, $action) {
            $previousReserved = $stock->reserved;
            $newReserved = $previousReserved + $quantity;

            if ($newReserved < 0) {
                throw new \InvalidArgumentException('Reserved stock cannot be negative');
            }

            $updated = $stock->update(['reserved' => $newReserved]);

            if ($updated) {
                $this->stockLogService->logStockChange(
                    product: $stock->stockable,
                    quantityChange: $quantity,
                    action: $action ?? ($quantity > 0 ? 'add' : 'remove'),
                    reason: $reason ?? ($quantity > 0 ? 'Reserved stock addition' : 'Reserved stock removal'),
                    user: auth()->user()
                );
            }

            return $updated;
        });
    }

    public function adjustStock(ProductStock $stock, int $quantity, ?string $reason = null, ?string $action = null): bool
    {
        return $this->adjustAvailableStock($stock, $quantity, $reason, $action);
    }
} 