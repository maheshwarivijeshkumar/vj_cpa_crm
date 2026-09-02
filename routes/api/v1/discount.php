<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Discount\DiscountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module: Discount
| Prefix: /api/v1/discount
| Auth:   auth:sanctum (set in api.php hub)
|--------------------------------------------------------------------------
*/

// Platform admin: full CRUD
Route::middleware('auth:sanctum')->group(function (): void {

    // Listing + stats
    Route::get('/',       [DiscountController::class, 'index'])->name('index');
    Route::get('/stats',  [DiscountController::class, 'stats'])->name('stats');

    // Single discount
    Route::get('/{id}',            [DiscountController::class, 'show'])->name('show');
    Route::post('/',               [DiscountController::class, 'store'])->name('store');
    Route::patch('/{id}',          [DiscountController::class, 'update'])->name('update');
    Route::post('/{id}/deactivate',[DiscountController::class, 'deactivate'])->name('deactivate');
    Route::delete('/{id}',         [DiscountController::class, 'destroy'])->name('destroy');

    // Tenant user: validate code at checkout (tenant-scoped, no policy needed here — service handles it)
    Route::post('/validate', [DiscountController::class, 'validate'])->name('validate');
});
