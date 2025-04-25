<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderService
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly StoreService $storeService,
        private readonly AddressService $addressService
    ) {}

    /**
     * Get all orders with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllOrders(int $perPage = 10): LengthAwarePaginator
    {
        return Order::query()
            ->with(['lines', 'payment', 'customer', 'billingAddress', 'shippingAddress'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Generate the next order number
     * 
     * @return string
     */
    private function generateOrderNumber(): string
    {
        $lastOrder = Order::orderBy('order_number', 'desc')->first();
        $lastNumber = $lastOrder ? (int) substr($lastOrder->order_number, 1) : 10000;
        return 'O' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    public function createOrder(array $data): Order
    {
        try {
            return DB::transaction(function () use ($data) {
                // Get store settings for tax calculation
                $store = $this->storeService->getCurrentStore();
                
                // Get and store address data
                $billingAddress = $this->addressService->getAddress($data['billing_address_id']);
                $shippingAddress = $this->addressService->getAddress($data['shipping_address_id']);

                // Create order
                $order = Order::create([
                    'customer_id' => $data['customer_id'],
                    'billing_address_id' => $data['billing_address_id'],
                    'shipping_address_id' => $data['shipping_address_id'],
                    'order_number' => $this->generateOrderNumber(),
                    'status' => 'open',
                    'currency' => $store->currency,
                    'billing_address_data' => $billingAddress->toArray(),
                    'shipping_address_data' => $shippingAddress->toArray(),
                    'notes' => $data['notes'] ?? null,
                ]);

                $subtotal = 0;
                $taxAmount = 0;

                // Create order lines
                foreach ($data['lines'] as $lineData) {
                    $product = Product::findOrFail($lineData['product_id']);
                    
                    // Get the current price from the product
                    $unitPrice = $lineData['unit_price'] ?? $product->price;
                    $quantity = $lineData['quantity'];
                    $lineSubtotal = $unitPrice * $quantity;
                    
                    // Calculate line discount if provided
                    $lineDiscount = $lineData['discount_amount'] ?? 0;
                    $lineSubtotal -= $lineDiscount;

                    // Calculate tax if enabled in store settings
                    $lineTax = 0;
                    if ($store->settings['tax_enabled'] ?? false) {
                        $taxRate = $store->settings['tax_rate'] ?? 0;
                        $lineTax = ($lineSubtotal * $taxRate) / 100;
                    }

                    $orderLine = OrderLine::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_amount' => $lineDiscount,
                        'tax_amount' => $lineTax,
                        'total_price' => $lineSubtotal + $lineTax,
                        'product_data' => $product->toArray(),
                    ]);

                    $subtotal += $lineSubtotal;
                    $taxAmount += $lineTax;
                }

                // Apply order-level discount if provided
                $orderDiscount = $data['discount_amount'] ?? 0;
                $totalAmount = $subtotal + $taxAmount - $orderDiscount;

                // Update order totals
                $order->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $orderDiscount,
                    'total_amount' => $totalAmount,
                ]);

                // Create payment if requested
                if (isset($data['create_payment']) && $data['create_payment']) {
                    $payment = $this->paymentService->createPayment([
                        'customer_id' => $data['customer_id'],
                        'payment_gateway_id' => $data['payment_gateway_id'],
                        'amount' => $totalAmount,
                        'currency' => $store->currency,
                        'description' => "Order #{$order->order_number}",
                        'redirect_url' => $data['redirect_url'] ?? null,
                        'metadata' => [
                            'order_id' => $order->id,
                        ],
                    ]);

                    $order->payment()->save($payment);
                }

                return $order->load(['lines', 'payment']);
            });
        } catch (Throwable $e) {
            Log::error('Error creating order: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getOrder(string $id): ?Order
    {
        return Order::with(['lines', 'payment'])->find($id);
    }

    public function updateOrderStatus(string $id, string $status): bool
    {
        $order = Order::findOrFail($id);
        return $order->update(['status' => $status]);
    }
} 