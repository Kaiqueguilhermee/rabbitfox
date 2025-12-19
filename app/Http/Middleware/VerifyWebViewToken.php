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
    // Páginas institucionais e públicas
    'como-funciona',
    'suporte',
    'sobre-nos',
    'banned',

    // Autenticação e recuperação de senha
    'login',
    'logout',
    'register',
    'forgot-password',
    'send-reset-link',
    'reset-password',
    'auth/redirect',
    'auth/callback',

    // APIs abertas e emissão de token
    'api/app/token',
    'api/auth/authentication',
    'api/games/game_launch',
    'api/games/all',
    'api/games/provider',
    'api/user/balance',

    // Webhooks e integrações externas
    'drakon_api',
    'webhook/drakon',
    'api/drakon_api',
    'drakon/webhook',
    'drakon_api/test',
    'drakon-test/games',
    'drakon-launch',

    // Jogo público neutro
    'rabbitAmoung',
    'rabbit-amoung',

    // Assets e arquivos públicos
    'assets',
    'css',
    'js',
    'livewire',
    'storage',

    // Rotas de jogos e categorias públicas
    'game',
    'games',
    'categoria',
    'maintenance',
    'play',

    // Providers e integrações de jogos
    'vgames',
    'fivers',
    'vibragames',
    'vibra',
    'provider',
    'painel/affiliates/join',

    // Outros prefixos de testes e aberturas
    'open',
    'test',
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
                return redirect('/rabbitAmoung');
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
            return redirect('/rabbitAmoung');
        }

        $payload = Cache::get('app_token:' . $token);
        if (empty($payload)) {
            Log::warning('VerifyWebViewToken: invalid/expired token', ['path'=>$path, 'token_preview'=>substr($token,0,8)]);
            return redirect('/rabbitAmoung');
        }

        // token valid -> continue
        return $next($request);
    }
}
