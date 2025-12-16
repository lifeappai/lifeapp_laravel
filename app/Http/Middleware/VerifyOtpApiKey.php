<?php

namespace App\Http\Middleware;

use Closure;

class VerifyOtpApiKey
{
    public function handle($request, Closure $next)
    {
        $key = $request->header('x-api-key');

        if (!$key || $key !== env('OTP_API_KEY')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized request'
            ], 401);
        }

        return $next($request);
    }
}
