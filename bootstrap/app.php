<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\UuidMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web     : __DIR__ . '/../routes/web.php',
        api     : __DIR__ . '/../routes/API/V1/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health  : '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->throttleApi();
        $middleware->alias([
            'uuid' => UuidMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'error' => 'You are not authenticated to access this resource'
                ], Response::HTTP_UNAUTHORIZED);
            }
        });
    })->create();
