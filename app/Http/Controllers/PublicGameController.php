<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class PublicGameController extends Controller
{
    /**
     * Serve the public neutral game located in game/play/rabbitAmoung/index.html
     */
    public function rabbitAmoung(Request $request)
    {
        $path = base_path('game/play/rabbitAmoung/index.html');
        if (!File::exists($path)) {
            abort(404);
        }

        $content = File::get($path);
        return response($content, 200)->header('Content-Type', 'text/html');
    }
}
