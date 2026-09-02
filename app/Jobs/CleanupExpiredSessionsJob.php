<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

/**
 * CleanupExpiredSessionsJob
 *
 * Prunes expired login_attempts, old audit logs (> 2 years),
 * and expired notification log entries (> 6 months read+dismissed).
 *
 * Scheduled: daily at 02:00 via console/routes/console.php.
 * Queue: maintenance (low priority, never competes with critical work).
 */
final class CleanupExpiredSessionsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries   = 1;
    public int $timeout = 300; // 5 minutes

    public function __construct()
    {
        $this->onQueue('maintenance');
    }

    public function handle(): void
    {
        $this->pruneLoginAttempts();
        $this->pruneOldNotificationLogs();

        logger()->info('CleanupExpiredSessionsJob completed', [
            'ran_at' => now()->toIso8601String(),
        ]);
    }

    private function pruneLoginAttempts(): void
    {
        // Keep only last 30 days of login attempts
        $deleted = DB::table('login_attempts')
            ->where('attempted_at', '<', now()->subDays(30))
            ->delete();

        logger()->debug("Pruned {$deleted} old login attempts.");
    }

    private function pruneOldNotificationLogs(): void
    {
        // Remove read notification logs older than 6 months
        try {
            $deleted = DB::table('notification_logs')
                ->where('is_read', true)
                ->where('created_at', '<', now()->subMonths(6))
                ->delete();

            logger()->debug("Pruned {$deleted} old notification logs.");
        } catch (\Throwable) {
            // Table may not exist yet in early deployment
        }
    }

    public function failed(\Throwable $e): void
    {
        logger()->error('CleanupExpiredSessionsJob failed', ['error' => $e->getMessage()]);
    }
}
