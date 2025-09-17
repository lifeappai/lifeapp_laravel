<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MasterAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('x-master-token');
        if ($token) {
            $decrypt = Crypt::decryptString($token);
            $component = explode(".", $decrypt);
            if (count($component) > 1) {
                $time = $component[1];
                if (time() - $time < 24*60*60) {
                    $request->mobile_no = $component[0];
                    return $next($request);
                } else {
                    return response()->json([
                        'error' => "token expired"
                    ], 401);
                }
            }
        }
        return response()->json([
            'error' => "Auth failed"
        ], 401);
    }
}
