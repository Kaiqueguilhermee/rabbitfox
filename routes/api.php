<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

include_once(__DIR__ . '/groups/api/provider/vgames.php');

// Drakon API integration routes
use App\Http\Controllers\Api\DrakonController;

Route::post('/auth/authentication', [DrakonController::class, 'authenticate']);
Route::get('/games/game_launch', [DrakonController::class, 'gameLaunch']);
Route::get('/games/all', [DrakonController::class, 'allGames']);
Route::get('/games/provider', [DrakonController::class, 'providers']);

// Drakon Webhook - múltiplas rotas para compatibilidade (GET e POST)
Route::match(['get', 'post'], '/drakon_api', [DrakonController::class, 'webhook']);
Route::match(['get', 'post'], '/webhook/drakon', [DrakonController::class, 'webhook']);
Route::match(['get', 'post'], '/drakon/webhook', [DrakonController::class, 'webhook']);
