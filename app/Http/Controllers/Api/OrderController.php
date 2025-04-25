<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly AddressService $addressService
    ) {}

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 10);
            $orders = $this->orderService->getAllOrders($perPage);

            return response()->json($orders);
        } catch (Throwable $e) {
            Log::error('Error fetching orders: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching orders',
                'status' => 500,
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|uuid|exists:customers,id',
                'billing_address_id' => 'required_without_all:billing_address,shipping_address|exists:addresses,id',
                'billing_address' => 'required_without_all:billing_address_id,shipping_address|array',
                'billing_address.street' => 'required_with:billing_address|string',
                'billing_address.house_number' => 'required_with:billing_address|string',
                'billing_address.postal_code' => 'required_with:billing_address|string',
                'billing_address.city' => 'required_with:billing_address|string',
                'billing_address.country' => 'required_with:billing_address|string',
                'billing_address.additional_info' => 'nullable|string',
                'shipping_address' => 'required_without:shipping_address_id|array',
                'shipping_address.street' => 'required_with:shipping_address|string',
                'shipping_address.house_number' => 'required_with:shipping_address|string',
                'shipping_address.postal_code' => 'required_with:shipping_address|string',
                'shipping_address.city' => 'required_with:shipping_address|string',
                'shipping_address.country' => 'required_with:shipping_address|string',
                'shipping_address.additional_info' => 'nullable|string',
                'lines' => 'required|array|min:1',
                'lines.*.product_id' => 'required|exists:products,id',
                'lines.*.quantity' => 'required|integer|min:1',
                'lines.*.unit_price' => 'nullable|numeric|min:0',
                'lines.*.discount_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'create_payment' => 'boolean',
                'payment_gateway_id' => 'required_if:create_payment,true|exists:payment_gateways,id',
                'redirect_url' => 'required_if:create_payment,true|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Handle addresses
            if (isset($data['shipping_address'])) {
                $shippingAddress = $this->addressService->createAddress($data['customer_id'], [
                    ...$data['shipping_address'],
                    'type' => 'shipping',
                    'is_default' => false,
                ]);
                $data['shipping_address_id'] = $shippingAddress->id;

                // If no billing address is provided, use shipping address as billing address
                if (!isset($data['billing_address']) && !isset($data['billing_address_id'])) {
                    $billingAddress = $this->addressService->createAddress($data['customer_id'], [
                        ...$data['shipping_address'],
                        'type' => 'billing',
                        'is_default' => false,
                    ]);
                    $data['billing_address_id'] = $billingAddress->id;
                }
            }

            // Handle billing address if provided separately
            if (isset($data['billing_address'])) {
                $billingAddress = $this->addressService->createAddress($data['customer_id'], [
                    ...$data['billing_address'],
                    'type' => 'billing',
                    'is_default' => false,
                ]);
                $data['billing_address_id'] = $billingAddress->id;
            }

            $order = $this->orderService->createOrder($data);

            return response()->json($order, 201);
        } catch (Throwable $e) {
            Log::error('Error creating order: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the order',
                'status' => 500,
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrder($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json($order);
        } catch (Throwable $e) {
            Log::error('Error fetching order: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the order',
                'status' => 500,
            ], 500);
        }
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|string|in:open,processing,shipped,delivered,cancelled,refunded',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $updated = $this->orderService->updateOrderStatus($id, $validator->validated()['status']);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
            ]);
        } catch (Throwable $e) {
            Log::error('Error updating order status: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the order status',
                'status' => 500,
            ], 500);
        }
    }
} 