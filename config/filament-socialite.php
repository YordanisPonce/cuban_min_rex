<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OAuth callback middleware
    |--------------------------------------------------------------------------
    |
    | This option defines the middleware that is applied to the OAuth callback url.
    |
    */

    'middleware' => [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    |
    | Enable or disable user registration through social login.
    |
    */

    'registration' => true,

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    |
    | Define the social providers you want to use for login/registration.
    | You can add multiple (google, github, facebook, etc).
    |
    */

    'providers' => [
        'google' => [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect' => env('GOOGLE_REDIRECT_URI'),
        ],
    ],

];
