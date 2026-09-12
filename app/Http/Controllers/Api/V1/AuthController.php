<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $payload = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt(['email' => $payload['email'], 'password' => $payload['password']])) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password tidak valid.',
                'data' => null,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'code' => 401,
                ],
            ], 401);
        }

        $user = Auth::user();
        $token = Str::random(80);

        $user->forceFill([
            'api_token' => $token,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'level' => $user->level,
                ],
            ],
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'code' => 200,
            ],
        ], 200);
    }

    public function me(Request $request)
    {
        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Profil pengguna berhasil diambil.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'level' => $user->level,
            ],
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'code' => 200,
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = auth('api')->user();

        $user->forceFill([
            'api_token' => null,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
            'data' => null,
            'meta' => [
                'timestamp' => now()->toIso8601String(),
                'code' => 200,
            ],
        ], 200);
    }
}
