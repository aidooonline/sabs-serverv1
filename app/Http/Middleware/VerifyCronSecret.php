<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyCronSecret
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $secretKey = env('LOAN_CRON_SECRET_KEY');

        // Check if the secret key is configured and matches the header
        if (!$secretKey || $request->header('X-Cron-Secret') !== $secretKey) {
            // Log an attempt of unauthorized access
            Log::warning('Unauthorized access attempt to cron endpoint.', ['ip' => $request->ip()]);
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 401);
        }

        return $next($request);
    }
}
