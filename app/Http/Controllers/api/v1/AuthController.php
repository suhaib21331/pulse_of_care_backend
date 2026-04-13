<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;

class AuthController
{
    public $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request);

        if (!isset($result['token'])) {
            return response()->json([
                'message' => $result['message']
            ], 401);
        }

        return response()->json([
            'token' => $result['token'],
            'message' => $result['message']
        ], 200);
    }

    /*public function logout()
    {
        $result = $this->authService->logout();

        return response()->json($result, 200);
    }*/
}
