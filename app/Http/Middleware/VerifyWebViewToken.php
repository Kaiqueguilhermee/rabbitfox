<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VerifyWebViewToken
{
    /**
     * Handle an incoming request.
     * - Blocks bots/crawlers and redirects them to the public game.
     * - Requires header `X-App-Token` (or `app_token` query) and validates it against cache.
     */
    public function handle(Request $request, Closure $next)
    {
        $ua = strtolower($request->header('User-Agent', ''));

        $bots = [
            'googlebot','bingbot','slurp','duckduckbot','baiduspider','yandex','facebookexternalhit',
            'facebot','twitterbot','rogerbot','ahrefsbot','semrushbot'
        ];

        foreach ($bots as $b) {
            if (strpos($ua, $b) !== false) {
                return redirect('/rabbit-amoung');
            }
        }

        $token = $request->header('X-App-Token') ?: $request->query('app_token');

        if (!$token) {
            return redirect('/rabbit-amoung');
        }

        $payload = Cache::get('app_token:' . $token);
        if (empty($payload)) {
            return redirect('/rabbit-amoung');
        }

        return $next($request);
    }
}
