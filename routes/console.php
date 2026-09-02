<?php

declare(strict_types=1);

use App\Jobs\CleanupExpiredReferralsJob;
use App\Jobs\CleanupExpiredSessionsJob;
use App\Jobs\GenerateDeadlinesJob;
use App\Jobs\LapseExpiredSubscriptionsJob;
use App\Jobs\WinbackDiscountJob;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Schedule
|--------------------------------------------------------------------------
| All scheduled jobs are defined here.
| Run locally with: php artisan schedule:run
| In production: * * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
*/

// Daily cleanup: prune login_attempts + old notification logs
Schedule::job(new CleanupExpiredSessionsJob())
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->name('cleanup-expired-sessions')
    ->description('Prune old login attempts and read notification logs');

// Daily deadline scan: generate filing deadline reminders
Schedule::job(new GenerateDeadlinesJob())
    ->dailyAt('07:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->name('generate-deadlines')
    ->description('Scan upcoming filing deadlines and dispatch reminder events');

// Weekly: clear SeoService cache so stale SEO meta gets refreshed from DB
Schedule::call(function (): void {
    \Illuminate\Support\Facades\Cache::forget('seo:*');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
})
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->name('clear-seo-cache')
    ->description('Refresh SEO meta cache weekly');

// Daily: lapse subscriptions whose end_date has passed
Schedule::job(new LapseExpiredSubscriptionsJob())
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->onOneServer()
    ->name('lapse-expired-subscriptions')
    ->description('Transition expired active subscriptions to lapsed status');

// Daily: send winback discount to tenants lapsed 30–60 days ago
Schedule::job(new WinbackDiscountJob())
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->name('winback-discount')
    ->description('Generate personalised win-back discount codes for lapsed tenants');

// Daily: expire referrals that are past their expiry date
Schedule::job(new CleanupExpiredReferralsJob())
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->name('cleanup-expired-referrals')
    ->description('Mark unverified referrals past their expiry date as expired');
