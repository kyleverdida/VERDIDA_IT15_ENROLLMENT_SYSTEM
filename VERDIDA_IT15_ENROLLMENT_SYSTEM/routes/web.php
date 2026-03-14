<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Backend API is running.',
        'docs' => '/docs/API_DOCUMENTATION.md',
    ]);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Not Found. Use /api/* endpoints.',
    ], 404);
});