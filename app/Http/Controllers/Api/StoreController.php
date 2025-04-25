<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * @var StoreService
     */
    protected $storeService;

    /**
     * Create a new controller instance.
     *
     * @param StoreService $storeService
     */
    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * Get store details.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $store = $this->storeService->getCurrentStore();
        
        return response()->json([
            'data' => $store,
        ]);
    }

    /**
     * Update store details.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'support_email' => 'sometimes|email|max:255',
            'vat_number' => 'sometimes|string|max:255|nullable',
            'is_active' => 'sometimes|boolean',
            'phone' => 'sometimes|string|max:255|nullable',
            'address' => 'sometimes|string|max:255|nullable',
            'city' => 'sometimes|string|max:255|nullable',
            'postal_code' => 'sometimes|string|max:255|nullable',
            'country' => 'sometimes|string|max:255|nullable',
            'currency' => 'sometimes|string|size:3',
            'timezone' => 'sometimes|string|max:255',
            'settings' => 'sometimes|array',
        ]);

        $store = $this->storeService->updateStore($validated);

        return response()->json([
            'data' => $store,
            'message' => 'Store details updated successfully.',
        ]);
    }
} 