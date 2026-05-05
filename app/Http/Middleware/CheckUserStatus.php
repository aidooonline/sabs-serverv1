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

        if ($user && $user->is_disabled) {
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
                'message' => 'Your account has been disabled. Access denied.'
            ], 403);
        }

        return $next($request);
    }
}
