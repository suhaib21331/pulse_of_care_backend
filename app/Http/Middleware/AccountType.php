<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountType
{
    public function handle(Request $request, Closure $next, string ...$accountTypes): Response
    {
        $user = auth()->guard('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $userType = str_replace('-', '_', $user->account_type);

        $allowedTypes = array_map(function ($type) {
            return str_replace('-', '_', $type);
        }, $accountTypes);

        if (in_array($userType, $allowedTypes, true)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized account type',
            'user_type' => $userType,
            'allowed_types' => $allowedTypes,
        ], 403);
    }
}