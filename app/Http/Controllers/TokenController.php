<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TokenController extends Controller
{
    /**
     * Issue a short-lived token for the mobile WebView.
     * Expect optional `device_id` in the request body.
     */
    public function issue(Request $request)
    {
        $device = $request->input('device_id');
        try {
            $token = bin2hex(random_bytes(32));
        } catch (\Exception $e) {
            $token = uniqid('', true) . bin2hex(openssl_random_pseudo_bytes(16));
        }

        $ttl = config('app.webview_token_ttl', 3600);

        Cache::put('app_token:' . $token, [
            'device' => $device,
            'issued_at' => time(),
        ], $ttl);

        return response()->json([
            'token' => $token,
            'expires_in' => $ttl,
        ]);
    }
}
