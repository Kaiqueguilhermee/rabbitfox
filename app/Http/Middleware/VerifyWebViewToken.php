<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VerifyWebViewToken
{
    /**
     * Handle an incoming request.
     * - Blocks bots/crawlers and redirects them to the public game.
     * - Requires header `X-App-Token` (or `app_token` query) and validates it against cache.
     */
    public function handle(Request $request, Closure $next)
    {
        $path = trim($request->path(), '/');

        // Paths to exclude from token enforcement (public assets, health, rabbit-amoung itself, webhooks, API, etc.)
        $exclusions = [
            '', // homepage
            'rabbit-amoung',
            'open',
            'test',
            'drakon_api',
            'webhook/drakon',
            'api/drakon_api',
            'drakon-test/games',
            'api',
            'assets',
            'css',
            'js',
            'livewire',
            'storage',
        ];

        foreach ($exclusions as $ex) {
            if ($ex === '') continue;
            if (stripos($path, $ex) === 0) {
                return $next($request);
            }
        }

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

        // Accept token from header, Authorization Bearer, cookie or query
        $token = $request->header('X-App-Token') ?: $request->query('app_token');
        if (empty($token)) {
            $authHeader = $request->header('Authorization') ?: $request->header('authorization');
            if (!empty($authHeader) && preg_match('/Bearer\s+(\S+)/i', $authHeader, $m)) {
                $token = $m[1];
            }
        }
        if (empty($token)) {
            $token = $request->cookie('app_token');
        }

        if (empty($token)) {
            Log::info('VerifyWebViewToken: missing token', ['path'=>$path, 'ua'=>substr($ua,0,200)]);
            return redirect('/rabbit-amoung');
        }

        $payload = Cache::get('app_token:' . $token);
        if (empty($payload)) {
            Log::warning('VerifyWebViewToken: invalid/expired token', ['path'=>$path, 'token_preview'=>substr($token,0,8)]);
            return redirect('/rabbit-amoung');
        }

        // token valid -> continue
        return $next($request);
    }
}
