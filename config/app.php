<?php

use App\Models\SeoText;

$seo = rescue(fn () => SeoText::first(), null, false);

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => $seo?->app_name ?? env('APP_NAME', 'CubanPool'),
    
    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    'keywords' => $seo?->app_keywords ?: env('APP_KEYWORDS', 'CubanPool, cuban pool, música, remixes, dj, descargas, mp3, sets, mixes, electrónica, edm, descargar música, escuchar música online, dj tools, producer tools, club music, dance music'),

    'description' => $seo?->app_description ?: env('APP_DESCRIPTION', 'Escucha y descarga música, remixes y contenido exclusivo para DJs.'),

    'logo' => env('APP_URL').'/assets/img/favicon/logo.png',

    'logo_alter' => env('APP_URL').'/assets/img/favicon/logo.png',

    'logo_32x32' => env('APP_URL').'/assets/img/favicon/logo-32x32.png',

    'logo_180x180' => env('APP_URL').'/assets/img/favicon/logo-180x180.png',

    'logo_192x192' => env('APP_URL').'/assets/img/favicon/logo-192x192.png',

    'logo_512x512' => env('APP_URL').'/assets/img/favicon/logo-512x512.png',

    'og_image' => env('APP_OG_IMAGE', env('APP_URL').'/assets/img/favicon/logo.png'),

    'twitter' => env('APP_TWITTER', ''),

    'og_locale' => env('APP_OG_LOCALE', 'es_ES'),

    'social' => array_values(array_filter([
        env('APP_INSTAGRAM', ''),
        env('APP_YOUTUBE', ''),
        env('APP_FACEBOOK', ''),
    ])),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'es'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'es'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'es_ES'),
    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
