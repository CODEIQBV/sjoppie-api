<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
            'api.response' => \App\Http\Middleware\ApiResponseMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'v1/payments/webhook*'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
