<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module: Subscription
| Prefix: /api/v1/subscription
| Auth:   auth:sanctum + tenant (set in api.php hub)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function (): void {

    // Tenant user: view their own subscriptions
    Route::get('/',         [SubscriptionController::class, 'index'])->name('index');
    Route::get('/current',  [SubscriptionController::class, 'current'])->name('current');
    Route::get('/{id}',     [SubscriptionController::class, 'show'])->name('show');

    // Tenant firm owner: renew
    Route::post('/renew',   [SubscriptionController::class, 'renew'])->name('renew');

    // Cancel (firm owner cancels own, platform admin cancels any)
    Route::post('/{id}/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');

    // Platform admin only
    Route::post('/',        [SubscriptionController::class, 'store'])->name('store');
    Route::get('/stats',    [SubscriptionController::class, 'stats'])->name('stats');
});
