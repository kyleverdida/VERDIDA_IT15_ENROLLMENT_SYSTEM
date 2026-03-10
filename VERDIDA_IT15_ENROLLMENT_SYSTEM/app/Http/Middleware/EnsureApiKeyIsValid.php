<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = (string) config('services.frontend.api_key', '');

        if ($expectedKey === '') {
            return response()->json([
                'message' => 'API key is not configured on the server.',
            ], 500);
        }

        $providedKey = (string) $request->header('X-API-KEY', '');

        if (! hash_equals($expectedKey, $providedKey)) {
            return response()->json([
                'message' => 'Invalid API key.',
            ], 401);
        }

        return $next($request);
    }
}
