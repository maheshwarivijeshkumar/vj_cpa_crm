<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Settings\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module: settings
| Prefix: /api/v1/settings
| Auth:   auth:sanctum + tenant (set in api.php hub)
|--------------------------------------------------------------------------
| Self-service settings for authenticated users (profile, password, notifications).
*/

Route::get('/profile',            [SettingsController::class, 'profile']);
Route::patch('/profile',          [SettingsController::class, 'updateProfile']);
Route::patch('/password',         [SettingsController::class, 'updatePassword']);
Route::get('/notifications',      [SettingsController::class, 'notifications']);
Route::patch('/notifications',    [SettingsController::class, 'updateNotifications']);
