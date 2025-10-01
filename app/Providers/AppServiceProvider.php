<?php

namespace App\Providers;

use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);


        Storage::disk('public')->buildTemporaryUrlsUsing(function (string $path, DateTime $expiration, array $options) {
            return URL::temporarySignedRoute(
                'public.files.download', // nombre de ruta firmada
                $expiration,
                array_merge($options, ['path' => $path])
            );
        });
    }
}
