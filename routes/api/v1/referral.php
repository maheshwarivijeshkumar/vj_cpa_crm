<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Referral\ReferralController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module: Referral
| Prefix: /api/v1/referral
| Auth:   auth:sanctum + tenant (set in api.php hub)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function (): void {

    // Get / generate the referral link
    Route::get('/link',    [ReferralController::class, 'link'])->name('link');

    // Current reward balance
    Route::get('/balance', [ReferralController::class, 'balance'])->name('balance');

    // List referral history
    Route::get('/',        [ReferralController::class, 'index'])->name('index');

    // Redeem points / credit
    Route::post('/redeem', [ReferralController::class, 'redeem'])->name('redeem');
});
