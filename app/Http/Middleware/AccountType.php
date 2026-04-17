<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountType
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $accountType): Response
    {
        $user = auth()->guard('api')->user();

        if ($user->account_type === $accountType) 
        {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized account type'
        ], 401);
    }
}
