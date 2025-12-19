<?php

use App\Http\Controllers\Panel\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')
    ->as('profile.')
    ->group(function ()
    {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/store', [ProfileController::class, 'store'])->name('store');
        Route::get('/avatar', [ProfileController::class, 'editAvatar'])->name('editAvatar');
        Route::post('/avatar/upload', [ProfileController::class, 'uploadAvatar'])->name('avatar.upload');
        Route::get('/security', [ProfileController::class, 'security'])->name('security');
    });
