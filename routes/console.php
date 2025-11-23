<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily period generation
Schedule::command('appointments:generate-periods')->dailyAt('00:00');

// Expire subscriptions whose end date has passed (runs daily at midnight)
Schedule::command('subscriptions:expire')->dailyAt('00:00');
