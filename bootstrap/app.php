<?php

use BookStoreAPI\SharedKernel\Domain\Exceptions\ValidationException;
use BookStoreAPI\SharedKernel\Infrastructure\Http\Middlewares\CorrelationIdMiddleware;
use BookStoreAPI\SharedKernel\Infrastructure\Http\Middlewares\LogContextMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
       $middleware->append(CorrelationIdMiddleware::class);
       $middleware->append(LogContextMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Force JSON for all API routes (or all requests, your call)
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });

        // Optional: customize the JSON shape per exception type
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->getErrors(),
                    'correlation_id' => $request->attributes->get('correlationId'),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Error',
                    'correlation_id' => $request->attributes->get('correlationId'),
                ], $e->getStatusCode());
            }
        });

        // Catch-all for anything else
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => app()->environment('production')
                        ? 'Server Error'
                        : $e->getMessage(),
                    'correlation_id' => $request->attributes->get('correlationId'),
                ], 500);
            }
        });
    })->create();
