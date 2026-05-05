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
        $user = Auth::user();

        if ($user && $user->is_disabled) {
            // Log the user out if they are disabled while having an active session/token
            if (method_exists($user, 'token')) {
                $user->token()->revoke();
            }
            
            // Clear API token to force re-auth
            $user->api_token = null;
            $user->save();

            return response()->json([
                'error' => 'account_disabled',
                'message' => 'Your account has been disabled. Access denied.'
            ], 403);
        }

        return $next($request);
    }
}
