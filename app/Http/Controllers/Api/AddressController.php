<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AddressController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService
    ) {}

    /**
     * Display a listing of the customer's addresses.
     */
    public function index(string $customerId): JsonResponse
    {
        try {
            $addresses = $this->addressService->getCustomerAddresses($customerId);

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $addresses,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching addresses',
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

    /**
     * Store a newly created address.
     */
    public function store(Request $request, string $customerId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:billing,shipping',
                'street' => 'required|string|max:255',
                'house_number' => 'required|string|max:20',
                'postal_code' => 'required|string|max:20',
                'city' => 'required|string|max:255',
                'country' => 'required|string|max:2',
                'is_default' => 'boolean',
                'additional_info' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $address = $this->addressService->createAddress($customerId, $validator->validated());

            return response()->json([
                'success' => true,
                'status' => 201,
                'data' => $address,
                'timestamp' => now()->toIso8601String(),
            ], 201);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the address',
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

    /**
     * Display the specified address.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $address = $this->addressService->getAddress($id);

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $address,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the address',
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

    /**
     * Update the specified address.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|in:billing,shipping',
                'street' => 'sometimes|string|max:255',
                'house_number' => 'sometimes|string|max:20',
                'postal_code' => 'sometimes|string|max:20',
                'city' => 'sometimes|string|max:255',
                'country' => 'sometimes|string|max:2',
                'is_default' => 'boolean',
                'additional_info' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $updated = $this->addressService->updateAddress($id, $validator->validated());

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found',
                ], 404);
            }

            $address = $this->addressService->getAddress($id);

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $address,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the address',
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

    /**
     * Remove the specified address.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->addressService->deleteAddress($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Address not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => 204,
                'timestamp' => now()->toIso8601String(),
            ], 204);
        } catch (Throwable $e) {
            Log::error('Error deleting address: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while deleting the address',
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

    /**
     * Get the default address for a specific type.
     */
    public function getDefault(string $customerId, string $type): JsonResponse
    {
        try {
            $address = $this->addressService->getDefaultAddress($customerId, $type);

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'No default address found for the specified type',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $address,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the default address',
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