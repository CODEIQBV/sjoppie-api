<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PaymentGatewayController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayService $paymentGatewayService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $gateways = PaymentGateway::all();
            
            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $gateways,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching payment gateways: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching payment gateways',
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

    public function availableModules(): JsonResponse
    {
        try {
            $modules = $this->paymentGatewayService->getAvailableModules();
            
            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $modules,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching available modules: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching available modules',
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

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'module_name' => 'required|string',
                'is_active' => 'boolean',
                'is_test_mode' => 'boolean',
                'configuration' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $gateway = $this->paymentGatewayService->createGateway($validator->validated());
            
            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $gateway,
                'timestamp' => now()->toIso8601String(),
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error creating payment gateway: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the payment gateway',
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

    public function show(PaymentGateway $paymentGateway): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $paymentGateway,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching payment gateway: ' . $e->getMessage(), [
                'exception' => $e,
                'gateway_id' => $paymentGateway->id,
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the payment gateway',
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

    public function update(Request $request, PaymentGateway $paymentGateway): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string',
                'is_active' => 'boolean',
                'is_test_mode' => 'boolean',
                'configuration' => 'array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $gateway = $this->paymentGatewayService->updateGateway($paymentGateway, $validator->validated());
            
            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $gateway,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('Error updating payment gateway: ' . $e->getMessage(), [
                'exception' => $e,
                'gateway_id' => $paymentGateway->id,
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the payment gateway',
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

    public function destroy(PaymentGateway $paymentGateway): JsonResponse
    {
        try {
            $paymentGateway->delete();
            
            return response()->json([
                'success' => true,
                'status' => 204,
                'timestamp' => now()->toIso8601String(),
            ], 204);
        } catch (Throwable $e) {
            Log::error('Error deleting payment gateway: ' . $e->getMessage(), [
                'exception' => $e,
                'gateway_id' => $paymentGateway->id,
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while deleting the payment gateway',
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
} 