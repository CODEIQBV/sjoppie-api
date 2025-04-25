<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_gateway_id' => 'required|exists:payment_gateways,id',
                'customer_id' => 'required|exists:customers,id',
                'amount' => 'required|numeric|min:0.01',
                'currency' => 'sometimes|string|size:3',
                'description' => 'required|string|max:255',
                'redirect_url' => 'required|url',
                'metadata' => 'sometimes|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $payment = $this->paymentService->createPayment($validator->validated());

            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => [
                    'payment' => $payment,
                    'checkout_url' => $payment->gateway_response['checkout_url'] ?? null,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error creating payment: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the payment',
                'status' => 500,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }

            return response()->json($response, 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPayment($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                    'status' => 404,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $payment,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching payment: ' . $e->getMessage(), [
                'exception' => $e,
                'payment_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the payment',
                'status' => 500,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }

            return response()->json($response, 500);
        }
    }

    public function webhook(Request $request, string $gateway): JsonResponse
    {
        try {
            // First find the payment using the gateway payment ID
            $payment = Payment::where('gateway_payment_id', $request->input('id'))->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                    'status' => 404,
                ], 404);
            }

            // Now we can safely use the payment's associated gateway
            $this->paymentService->handleWebhook($payment->paymentGateway, $request->all());

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Webhook processed successfully',
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('Error processing webhook: ' . $e->getMessage(), [
                'exception' => $e,
                'gateway' => $gateway,
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the webhook',
                'status' => 500,
            ], 500);
        }
    }
} 