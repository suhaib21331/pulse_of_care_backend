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
    public function handle(Request $request, Closure $next, string ...$accountTypes): Response
    {
        $user = auth()->guard('api')->user();

        if (in_array($user->account_type, $accountTypes, true)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized account type',
        ], 401);
    }
}
