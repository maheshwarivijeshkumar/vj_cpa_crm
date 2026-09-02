<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Module: notifications
| Prefix: /api/v1/notifications
| Auth:   auth:sanctum + tenant (set in api.php hub)
|--------------------------------------------------------------------------
| In-app notification inbox for authenticated tenant users.
*/

Route::get('/',                       [NotificationController::class, 'index']);
Route::get('/unread-count',           [NotificationController::class, 'unreadCount']);
Route::post('/read-all',              [NotificationController::class, 'markAllRead']);
Route::post('/{id}/read',             [NotificationController::class, 'markRead']);
Route::delete('/{id}',                [NotificationController::class, 'destroy']);
