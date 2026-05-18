<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


//User
Schedule::command('app:ban-remove')
    ->everyMinute()
    ->runInBackground()
    ->withoutOverlapping();


Schedule::command('app:delete-otp')
    ->hourly()
    ->runInBackground()
    ->withoutOverlapping();

Schedule::command('app:delete-tokens')
    ->hourly()
    ->runInBackground()
    ->withoutOverlapping();

//Report Generation Command / cron job
Schedule::command('reports:generate')->dailyAt('04:00');

