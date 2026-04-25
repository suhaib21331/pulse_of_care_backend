<?php

namespace App\Services\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    public function login($request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) 
        {
            return 
            [
                'status_code' => 401,
                'message' => 'Invalid email or password'
            ];
        }

        if (!$user->is_profile_completed) 
        {
            return [
                'status_code' => 403,
                'message' => 'Please complete your profile first',
                'user' => [
                    'id' => $user->id,
                    'account_type' => $user->account_type,
                    'is_profile_completed' => $user->is_profile_completed,
                ]
            ];
        }

        $user->load($user->account_type);

        $token = JWTAuth::fromUser($user);

        return [
            'status_code' => 200,
            'token' => $token,
            'user' => $user,
            'profile' => $user->{$user->account_type},
            'message' => 'Login successful'
        ];
    }

    public function me()
    {
        $user = auth()->guard('api')->user();

        $user->load($user->account_type);

        return 
        [
            'status_code' => 200,
            'user' => $user,
            'profile' => $user->{$user->account_type},
            'message' => 'User profile retrieved successfully'
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
