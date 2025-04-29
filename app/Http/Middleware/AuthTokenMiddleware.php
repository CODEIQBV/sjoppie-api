<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Missing authentication token',
                'timestamp' => now()->toIso8601String(),
            ], 401);
        }

        // Let Sanctum handle the token validation
        if (!auth('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'message' => 'Invalid or expired token',
                'timestamp' => now()->toIso8601String(),
            ], 401);
        }

        return $next($request);
    }
} 