<?php

declare(strict_types=1);

use App\Http\Controllers\Platform\SettingsController;
use App\Http\Controllers\Platform\TenantController;
use App\Http\Controllers\Platform\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Platform Admin Routes
|--------------------------------------------------------------------------
| All routes here are accessible to platform admins only.
| Middleware: auth, verified, platform.admin (alias registered in bootstrap/app.php)
*/

// ── Dashboard ─────────────────────────────────────────────────────────────────
Route::get('/', fn () => Inertia::render('Platform/Dashboard'))->name('dashboard');

// ── Tenants ───────────────────────────────────────────────────────────────────
Route::prefix('tenants')->name('tenants.')->group(function (): void {
    Route::get('/',              [TenantController::class, 'index'])->name('index');
    Route::get('/{id}',          [TenantController::class, 'show'])->name('show');
    Route::post('/',             [TenantController::class, 'store'])->name('store');
    Route::patch('/{id}',        [TenantController::class, 'update'])->name('update');
    Route::post('/{id}/suspend', [TenantController::class, 'suspend'])->name('suspend');
    Route::post('/{id}/reinstate',[TenantController::class, 'reinstate'])->name('reinstate');
    Route::delete('/{id}',       [TenantController::class, 'destroy'])->name('destroy');
    Route::get('/stats',         [TenantController::class, 'stats'])->name('stats');
});

// ── Users ─────────────────────────────────────────────────────────────────────
Route::prefix('users')->name('users.')->group(function (): void {
    Route::get('/',                     [UserController::class, 'index'])->name('index');
    Route::get('/{id}',                 [UserController::class, 'show'])->name('show');
    Route::patch('/{id}',               [UserController::class, 'update'])->name('update');
    Route::delete('/{id}',              [UserController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/restore',        [UserController::class, 'restore'])->name('restore');
    Route::post('/{id}/force-logout',   [UserController::class, 'forceLogout'])->name('force-logout');
});

// ── Settings ──────────────────────────────────────────────────────────────────
Route::prefix('settings')->name('settings.')->group(function (): void {
    Route::get('/',            [SettingsController::class, 'index'])->name('index');
    Route::patch('/',          [SettingsController::class, 'update'])->name('update');
    Route::post('/clear-cache',[SettingsController::class, 'clearCache'])->name('clear-cache');
});
