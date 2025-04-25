<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $validApiKey = config('api.api_key');

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Invalid API key',
                'timestamp' => now()->toIso8601String(),
            ], 401);
        }

        return $next($request);
    }
} 