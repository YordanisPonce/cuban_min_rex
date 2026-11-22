<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManifestController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $name = config('app.name');

        return response()->json([
            'id'               => '/',
            'name'             => $name . ' - Música, Remixes y Descargas',
            'short_name'       => Str::limit($name, 12, ''),
            'description'      => config('app.description'),
            'lang'             => 'es',
            'start_url'        => '/',
            'scope'            => '/',
            'display'          => 'standalone',
            'orientation'      => 'portrait',
            'background_color' => '#000000',
            'theme_color'      => '#000000',
            'icons' => [
                ['src' => asset('android-chrome-192x192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => asset('android-chrome-512x512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
                ['src' => asset('maskable-icon-512x512.png'),  'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
        ], 200, ['Content-Type' => 'application/manifest+json'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
