<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Notifications;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * NotificationController — tenant in-app notification inbox.
 *
 * GET  /api/v1/notifications          — list unread + recent notifications
 * GET  /api/v1/notifications/unread-count — count for badge
 * POST /api/v1/notifications/{id}/read  — mark one as read
 * POST /api/v1/notifications/read-all   — mark all as read
 * DELETE /api/v1/notifications/{id}     — dismiss a notification
 */
final class NotificationController extends Controller
{
    /** GET /notifications — paginated list of in-app notifications for the user */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = NotificationLog::query()
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $user->id)
            ->where('channel', 'in_app')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        return ApiResponse::success($notifications);
    }

    /** GET /notifications/unread-count */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = NotificationLog::query()
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $request->user()->id)
            ->where('channel', 'in_app')
            ->whereNull('read_at')
            ->count();

        return ApiResponse::success(['count' => $count]);
    }

    /** POST /notifications/{id}/read — mark one notification as read */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $notification = NotificationLog::query()
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $request->user()->id)
            ->findOrFail($id);

        $notification->update(['read_at' => now()]);

        return ApiResponse::success(null, 'Marked as read.');
    }

    /** POST /notifications/read-all — mark all in-app notifications as read */
    public function markAllRead(Request $request): JsonResponse
    {
        NotificationLog::query()
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $request->user()->id)
            ->where('channel', 'in_app')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return ApiResponse::success(null, 'All notifications marked as read.');
    }

    /** DELETE /notifications/{id} — dismiss/delete a notification */
    public function destroy(Request $request, int $id): JsonResponse
    {
        NotificationLog::query()
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $request->user()->id)
            ->findOrFail($id)
            ->delete();

        return ApiResponse::noContent('Notification dismissed.');
    }
}
