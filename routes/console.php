<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily at 12:00 PM IST — discover trends, research, write, and schedule 8 posts.
Schedule::command('blogify:generate-posts')
    ->dailyAt('12:00')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->runInBackground();

// Market opening briefing — weekdays at 9:30 AM IST (NSE/BSE open 9:15).
Schedule::command('blogify:generate-posts --market=open')
    ->weekdays()
    ->at('09:30')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->runInBackground();

// Market closing wrap — weekdays at 4:00 PM IST (NSE/BSE close 3:30).
Schedule::command('blogify:generate-posts --market=close')
    ->weekdays()
    ->at('16:00')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping()
    ->runInBackground();

// Every 5 minutes — publish any scheduled post whose time has arrived.
Schedule::command('blogify:publish-due')
    ->everyFiveMinutes()
    ->withoutOverlapping();
