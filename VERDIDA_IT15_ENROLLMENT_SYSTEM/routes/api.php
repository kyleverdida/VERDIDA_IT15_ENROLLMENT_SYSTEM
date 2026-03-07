<?php

use App\Http\Controllers\Api\AuthController;

Route::get('/token-test', function () {
    $user = \App\Models\Admin::first();
    return $user->createToken('test')->plainTextToken;
});

Route::post('/login', [AuthController::class, 'login']);