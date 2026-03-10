<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        $email = strtolower(trim(strip_tags($validated['email'])));
        $admin = Admin::where('email', $email)->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $tokenName = trim(strip_tags($validated['device_name'] ?? 'react-client'));
        $token = $admin->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Authenticated successfully.',
            'token' => $token,
            'user' => $admin,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}