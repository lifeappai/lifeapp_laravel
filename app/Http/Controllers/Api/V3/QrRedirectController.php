<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QrRedirectController extends Controller
{
    public function redirectToApp($type, $id)
    {
        $token = uniqid('qr_', true);

        Cache::put("qr_redirect_$token", [
            'type' => $type,
            'id'   => $id,
        ], now()->addHours(24));

        return redirect()->away("https://api.life-lab.org/qr-install?token=$token");
    }

    public function installPage(Request $request)
    {
        $token = $request->query('token');
        return view('qr-install', compact('token'));
    }

    public function getPendingRedirect(Request $request)
    {
        $token = $request->query('token');
        $redirect = Cache::get("qr_redirect_$token");

        if ($redirect) {
            Cache::forget("qr_redirect_$token");
            return response()->json(['status' => true, 'data' => $redirect]);
        }

        return response()->json(['status' => false, 'message' => 'No pending redirect']);
    }
}

