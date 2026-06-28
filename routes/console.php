<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:auto-pay-command')->monthlyOn(1, '23:59');

Schedule::command('queue:work database --stop-when-empty --max-time=3600 --tries=1')
    ->everyMinute()
    ->withoutOverlapping();