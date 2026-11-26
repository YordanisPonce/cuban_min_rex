<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:auto-pay-command')->monthlyOn(1, '23:59');

Schedule::command('queue:work database --stop-when-empty --max-time=3600 --tries=1')
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command('sitemap:generate', function () {
    $routes = [
        'home',             // Inicio
        'djs',              // Djs
        'remixes',          // Remixes
        'mixes',            // Mixes
        'videos',           // Videos
        'collection.index', // Packs
        'plans',            // Planes
        'radio',            // Emisora
    ];

    $sitemap = Sitemap::create();

    foreach ($routes as $name) {
        $sitemap->add(Url::create(route($name)));
    }

    $sitemap->writeToFile(public_path('sitemap.xml'));

    $this->info('Sitemap generado con ' . count($routes) . ' URLs.');
})->purpose('Genera public/sitemap.xml con las páginas principales');