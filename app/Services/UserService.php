<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class UserService
{
    public function __construct(
        protected User $user
    ) {}

    /**
     * Get all users
     */
    public function getAll(): array
    {
        return Cache::remember('users.all', 3600, function () {
            return $this->user->all()->toArray();
        });
    }

    /**
     * Get a user by ID
     */
    public function find(int $id): ?User
    {
        return Cache::remember("users.{$id}", 3600, function () use ($id) {
            return $this->user->find($id);
        });
    }

    /**
     * Create a new user
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->user->create($data);
        
        Cache::forget('users.all');
        
        return $user;
    }

    /**
     * Update a user
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->find($id);
        
        if (!$user) {
            return null;
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        
        Cache::forget("users.{$id}");
        Cache::forget('users.all');
        
        return $user;
    }

    /**
     * Delete a user
     */
    public function delete(int $id): bool
    {
        $user = $this->find($id);
        
        if (!$user) {
            return false;
        }

        $user->delete();
        
        Cache::forget("users.{$id}");
        Cache::forget('users.all');
        
        return true;
    }

    /**
     * Authenticate a user and generate Sanctum token
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->user->where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        // Create a new personal access token
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user->toArray(),
            'token' => $token
        ];
    }

    /**
     * Validate a Sanctum token
     */
    public function validateToken(string $token): bool
    {
        try {
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            return $tokenModel && !$tokenModel->expires_at?->isPast();
        } catch (\Exception $e) {
            return false;
        }
    }
} 