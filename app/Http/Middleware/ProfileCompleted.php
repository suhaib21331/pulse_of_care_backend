<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->guard('api')->user();

        if (!$user->is_profile_completed) 
        {
            return response()->json([
                'message' => 'Please complete your profile first',
                'user' => $user
            ], 403);
        }

        return $next($request);
    }
}
