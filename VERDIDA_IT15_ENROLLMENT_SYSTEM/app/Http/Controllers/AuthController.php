<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function info(): JsonResponse
    {
        return response()->json([
            'message' => 'Use /api/login for API authentication.',
        ]);
    }
}
