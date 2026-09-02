<?php

declare(strict_types=1);

use App\Http\Controllers\Platform\AuditLogController;
use App\Http\Controllers\Platform\Blog\BlogController;
use App\Http\Controllers\Platform\FeatureFlagController;
use App\Http\Controllers\Platform\LoginAttemptController;
use App\Http\Controllers\Platform\NotificationTemplateController;
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
Route::get('/', fn () => Inertia::render('Platform/Dashboard/Index'))->name('dashboard');

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

// ── Blog / CMS ────────────────────────────────────────────────────────────────
Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::get('/',              [BlogController::class, 'index'])->name('index');
    Route::get('/create',        [BlogController::class, 'create'])->name('create');
    Route::post('/',             [BlogController::class, 'store'])->name('store');
    Route::get('/{id}/edit',     [BlogController::class, 'edit'])->name('edit');
    Route::patch('/{id}',        [BlogController::class, 'update'])->name('update');
    Route::delete('/{id}',       [BlogController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/status', [BlogController::class, 'updateStatus'])->name('status');
});

// ── Notification Templates ────────────────────────────────────────────────────
Route::prefix('notifications')->name('notifications.')->group(function (): void {
    Route::get('/',             [NotificationTemplateController::class, 'index'])->name('index');
    Route::get('/{id}/edit',    [NotificationTemplateController::class, 'edit'])->name('edit');
    Route::patch('/{id}',       [NotificationTemplateController::class, 'update'])->name('update');
    Route::post('/{id}/preview',[NotificationTemplateController::class, 'preview'])->name('preview');
});

// ── Feature Flags ─────────────────────────────────────────────────────────────
Route::prefix('feature-flags')->name('feature-flags.')->group(function (): void {
    Route::get('/',            [FeatureFlagController::class, 'index'])->name('index');
    Route::post('/{id}/toggle',[FeatureFlagController::class, 'toggle'])->name('toggle');
    Route::patch('/{id}',      [FeatureFlagController::class, 'update'])->name('update');
});

// ── Audit Logs ────────────────────────────────────────────────────────────────
Route::prefix('audit-logs')->name('audit-logs.')->group(function (): void {
    Route::get('/', [AuditLogController::class, 'index'])->name('index');
});

// ── Login Attempts ────────────────────────────────────────────────────────────
Route::prefix('login-attempts')->name('login-attempts.')->group(function (): void {
    Route::get('/', [LoginAttemptController::class, 'index'])->name('index');
});
