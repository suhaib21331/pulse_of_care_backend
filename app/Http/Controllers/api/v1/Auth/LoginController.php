<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Http\Requests\LoginRequest;
use App\Services\Auth\LoginService;

class LoginController
{
    public $authService;

    public function __construct(LoginService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request);

        if (($result['status_code']) !== 200) 
        {
            return response()->json([
                'message' => $result['message'],
                'user' => $result['user'] ?? null,
            ], $result['status_code']);
        }

        return response()->json([
            'token' => $result['token'],
            'user' => $result['user'],
            'message' => $result['message']
        ], 200);
    }

    /*public function logout()
    {
        $result = $this->authService->logout();

        return response()->json($result, 200);
    }*/
}
