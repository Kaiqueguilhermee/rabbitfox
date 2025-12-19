<?php

use App\Http\Controllers\Panel\AffiliateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/open', function() {
   $tokenOpen = \Helper::DecToken('b6JnfGF7fXMjOjB79zVoLCJoeWFweCIyICJmZW2xQGRoeWctY1auIjpj8WQjOjAjMTm6OCIrIzRweWUjOjAjMT9pNiQ1NDY7N6Ja');
   dd($tokenOpen);
});
Route::get('/test', [\App\Http\Controllers\Provider\FiversController::class, 'gameLaunchApi']);

// Webhook Drakon - rotas alternativas para compatibilidade
Route::match(['get', 'post'], '/drakon_api', [\App\Http\Controllers\Api\DrakonController::class, 'webhook'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::match(['get', 'post'], '/webhook/drakon', [\App\Http\Controllers\Api\DrakonController::class, 'webhook'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::match(['get', 'post'], '/api/drakon_api', [\App\Http\Controllers\Api\DrakonController::class, 'webhook'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Teste simples do endpoint Drakon
Route::get('/drakon_api/test', function() {
    return response()->json([
        'status' => 'OK',
        'message' => 'Drakon webhook endpoint is working',
        'timestamp' => now()->toDateTimeString(),
        'url' => url('/drakon_api')
    ]);
});

include_once(__DIR__ . '/groups/auth/login.php');
include_once(__DIR__ . '/groups/auth/social.php');
include_once(__DIR__ . '/groups/auth/register.php');

// PROVIDERS
include_once(__DIR__ . '/groups/provider/slotegrator.php');
include_once(__DIR__ . '/groups/provider/pragmatic.php');
include_once(__DIR__ . '/groups/provider/suitpay.php');
include_once(__DIR__ . '/groups/provider/bspay.php');
include_once(__DIR__ . '/groups/provider/sqala.php');

Route::prefix('painel')
    ->as('panel.')
    ->middleware(['auth'])
    ->group(function ()
    {
        include_once(__DIR__ . '/groups/panel/wallet.php');
        include_once(__DIR__ . '/groups/panel/profile.php');
        include_once(__DIR__ . '/groups/panel/notifications.php');
        include_once(__DIR__ . '/groups/panel/affiliates.php');
    });

Route::middleware(['web'])
    ->as('web.')
    ->group(function ()
    {
        include_once(__DIR__ . '/groups/web/home.php');
        include_once(__DIR__ . '/groups/web/fivers.php');
        include_once(__DIR__ . '/groups/web/game.php');
        include_once(__DIR__ . '/groups/web/category.php');
        include_once(__DIR__ . '/groups/web/vgames.php');
        include_once(__DIR__ . '/groups/web/vibra.php');
    });

// Public neutral game endpoint (bots / direct browsers are redirected here)
use App\Http\Controllers\PublicGameController;
use Illuminate\Support\Facades\File;

// Explicit asset routes to ensure static files load correctly
use Illuminate\Support\Facades\Log;

Route::get('/rabbit-amoung/styles/{file}', function ($file) {
    $path = base_path('game/play/rabbitAmoung/styles/' . $file);
    Log::info('rabbit-amoung styles request', ['requested'=>$file, 'path'=>$path, 'exists'=>File::exists($path)]);
    if (!File::exists($path)) abort(404);
    return response(File::get($path), 200)->header('Content-Type', 'text/css');
})->where('file', '.*');

Route::get('/rabbit-amoung/js/{file}', function ($file) {
    $path = base_path('game/play/rabbitAmoung/js/' . $file);
    Log::info('rabbit-amoung js request', ['requested'=>$file, 'path'=>$path, 'exists'=>File::exists($path)]);
    if (!File::exists($path)) abort(404);
    return response(File::get($path), 200)->header('Content-Type', 'application/javascript');
})->where('file', '.*');

Route::get('/rabbit-amoung/{path?}', [PublicGameController::class, 'rabbitAmoung'])->where('path', '.*')->name('rabbit-amoung');


Route::prefix('painel')
    ->as('panel.')
    ->group(function ()
    {
        Route::prefix('affiliates')
            ->as('affiliates.')
            ->group(function () {
                Route::post('/join', [AffiliateController::class, 'joinAffiliate'])->name('join');
            });
    });


include_once(__DIR__ . '/groups/provider/vibra.php');
include_once(__DIR__ . '/groups/provider/fivers.php');
include_once(__DIR__ . '/groups/provider/salsa.php');

// Test page for Drakon games (logs result in browser console)
use App\Http\Controllers\Api\DrakonController;
Route::get('/drakon-test/games', [DrakonController::class, 'testGames']);

// Server-side game launch endpoint (protected)
Route::post('/drakon-launch', [DrakonController::class, 'launchGameServer'])->middleware('auth');

// API route to check user balance (web auth)
Route::get('/api/user/balance', function () {
    if (!auth()->check()) {
        return response()->json(['balance' => 0], 401);
    }
    
    $wallet = \App\Models\Wallet::where('user_id', auth()->id())->first();
    return response()->json([
        'balance' => $wallet ? $wallet->total_balance : 0,
        'balance_real' => $wallet ? $wallet->balance : 0,
        'balance_bonus' => $wallet ? $wallet->balance_bonus : 0,
    ]);
})->middleware('auth');
