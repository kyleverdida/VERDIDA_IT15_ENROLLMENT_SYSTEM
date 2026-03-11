<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.key' => \App\Http\Middleware\EnsureApiKeyIsValid::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request): ?string {
            if (str_starts_with($request->getPathInfo(), '/api/')) {
                return null;
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, \Throwable $e): bool {
            return str_starts_with($request->getPathInfo(), '/api/') || $request->expectsJson();
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            $isApiRequest = str_starts_with($request->getPathInfo(), '/api/');

            if ($isApiRequest || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return null;
        });
    })
    ->create();