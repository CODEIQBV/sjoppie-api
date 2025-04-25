<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            // Skip if the response is already in our format
            if ($response->headers->get('Content-Type') === 'application/json' && 
                isset(json_decode($response->getContent(), true)['success'])) {
                return $response;
            }

            // Get the original content
            $content = $response->getContent();
            $status = $response->getStatusCode();

            // Create standardized response
            $data = [
                'success' => $status >= 200 && $status < 300,
                'status' => $status,
                'data' => $content ? json_decode($content, true) : null,
                'timestamp' => now()->toIso8601String(),
            ];

            // Set the new content
            $response->setContent(json_encode($data));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (Throwable $e) {
            $response = [
                'success' => false,
                'status' => 500,
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred',
                'timestamp' => now()->toIso8601String(),
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