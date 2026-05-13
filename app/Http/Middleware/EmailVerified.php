<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->guard('api')->user();

        if (!$user->email_verified_at) 
        {
            return response()->json([
                'message' => 'Please verify your email address first',
                'user' => $user
            ], 403);
        }

        return $next($request);
    }
}
