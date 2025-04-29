<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Get all users
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->userService->getAll()
        ]);
    }

    /**
     * Get a specific user
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Create a new user
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->userService->create($validated);

        return response()->json([
            'data' => $user
        ], 201);
    }

    /**
     * Update a user
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        $user = $this->userService->update($id, $validated);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'data' => $user
        ]);
    }

    /**
     * Delete a user
     */
    public function destroy(int $id): JsonResponse
    {
        $success = $this->userService->delete($id);

        if (!$success) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json(null, 204);
    }

    /**
     * Authenticate a user
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $result = $this->userService->authenticate(
            $validated['email'],
            $validated['password']
        );

        if (!$result) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'data' => $result
        ]);
    }

    /**
     * Logout a user
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
