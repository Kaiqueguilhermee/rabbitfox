<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Symfony\Component\Mime\MimeTypes;

class PublicGameController extends Controller
{
    /**
     * Serve the public neutral game located in game/play/rabbitAmoung/index.html
     */
    public function rabbitAmoung(Request $request, $path = null)
    {
        $base = base_path('game/play/rabbitAmoung');

        if (empty($path) || $path === '/') {
            $file = $base . DIRECTORY_SEPARATOR . 'index.html';
        } else {
            // sanitize path
            $path = ltrim($path, '/');
            $file = $base . DIRECTORY_SEPARATOR . str_replace(['..','\\'], '', $path);
        }

        if (!File::exists($file)) {
            abort(404);
        }

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mime = 'application/octet-stream';
        try {
            $mimes = MimeTypes::getDefault();
            $m = $mimes->getMimeTypes($ext);
            if (!empty($m)) $mime = $m[0];
        } catch (\Throwable $e) {
            if (in_array($ext, ['css'])) $mime = 'text/css';
            if (in_array($ext, ['js'])) $mime = 'application/javascript';
            if (in_array($ext, ['html','htm'])) $mime = 'text/html';
            if (in_array($ext, ['png'])) $mime = 'image/png';
            if (in_array($ext, ['jpg','jpeg'])) $mime = 'image/jpeg';
            if (in_array($ext, ['svg'])) $mime = 'image/svg+xml';
        }

        $content = File::get($file);
        return response($content, 200)->header('Content-Type', $mime);
    }
}
