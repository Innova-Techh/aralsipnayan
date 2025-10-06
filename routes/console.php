<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule weekly section leaderboard badges (Every Sunday at 11:59 PM)
Schedule::command('leaderboard:award-weekly-section-badges')
    ->weekly()
    ->sundays()
    ->at('23:59')
    ->timezone('Asia/Manila');

// Schedule monthly school leaderboard badges (Last day of month at 11:59 PM)
Schedule::command('leaderboard:award-monthly-school-badges')
    ->monthlyOn(Carbon\Carbon::now()->endOfMonth()->day, '23:59')
    ->timezone('Asia/Manila');
