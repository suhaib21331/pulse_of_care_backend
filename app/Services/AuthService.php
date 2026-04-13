<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function login($request): array
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return [
                'message' => 'Invalid email or password'
            ];
        }

        return [
            'token' => $token,
            'message' => 'Login successful'
        ];
    }

    /*public function logout(): array
    {
        try {
            JWTAuth::parseToken()->invalidate();

            return [
                'status' => 200,
                'data' => [
                    'message' => 'Logout successful'
                ]
            ];
        } catch (\Throwable $throwable) {
            return [
                'status' => 401,
                'data' => [
                    'message' => 'Invalid or missing token'
                ]
            ];
        }
    }*/
}
