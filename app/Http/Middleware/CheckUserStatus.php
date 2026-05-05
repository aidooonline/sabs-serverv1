<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Use the API guard explicitly to get the user from the token
        $user = auth('api')->user();

        // Robust check: Block if is_disabled is true OR is_active is false
        // This ensures sync between different admin panels.
        if ($user && ($user->is_disabled || $user->is_active == 0)) {
            // Revoke Passport token if applicable
            if (method_exists($user, 'token') && $user->token()) {
                $user->token()->revoke();
            }
            
            // Clear custom API token and save
            $user->api_token = null;
            $user->save();

            return response()->json([
                'success' => false,
                'error' => 'account_disabled',
                'message' => 'Your account has been disabled or deactivated. Access denied.'
            ], 403);
        }

        return $next($request);
    }
}
