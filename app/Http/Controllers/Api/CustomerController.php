<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {}

    /**
     * Display a listing of the customers.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search');

            $customers = $search
                ? $this->customerService->searchCustomers($search, $perPage)
                : $this->customerService->getAllCustomers($perPage);

            return response()->json($customers);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching customers',
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
     * Store a newly created customer.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone_number' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'vat_number' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
                'is_active' => 'boolean',

                // Address data
                'address' => 'nullable|array',
                'address.type' => 'required_with:address|in:billing,shipping',
                'address.street' => 'required_with:address|string|max:255',
                'address.house_number' => 'required_with:address|string|max:20',
                'address.postal_code' => 'required_with:address|string|max:20',
                'address.city' => 'required_with:address|string|max:255',
                'address.country' => 'required_with:address|string|max:2',
                'address.is_default' => 'boolean',
                'address.additional_info' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $customer = $this->customerService->createCustomer($validator->validated());

            return response()->json($customer, 201);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the customer',
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
     * Display the specified customer.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $customer = $this->customerService->getCustomer($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            return response()->json($customer);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the customer',
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
     * Update the specified customer.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:customers,email,' . $id,
                'phone_number' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'vat_number' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $customer = $this->customerService->updateCustomer($id, $validator->validated());

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            return response()->json($customer);
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while updating the customer',
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
     * Remove the specified customer.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $deleted = $this->customerService->deleteCustomer($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            return response()->json(null, 204);
        } catch (Throwable $e) {
            Log::error('Error deleting customer: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while deleting the customer',
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
