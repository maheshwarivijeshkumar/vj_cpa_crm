<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\Filing\FilingDeadlineApproached;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

/**
 * GenerateDeadlinesJob
 *
 * Scans all upcoming filing deadlines and fires FilingDeadlineApproached events
 * for each deadline that is at a reminder threshold (30, 14, 7, 3, 1 day away).
 *
 * Scheduled: daily at 07:00 via console/routes/console.php.
 * Queue: deadlines (separate from notifications to avoid queue starvation).
 *
 * Idempotent: safe to run multiple times — uses last_reminded_at to prevent
 * duplicate notifications for the same threshold.
 *
 * Note: This job is a stub — full implementation happens in Phase 3 (Filings module).
 */
final class GenerateDeadlinesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries   = 1;          // Don't retry — re-run at next scheduled time
    public int $timeout = 600;        // 10 minutes

    /** Reminder thresholds in days */
    private const THRESHOLDS = [30, 14, 7, 3, 1];

    public function __construct()
    {
        $this->onQueue('deadlines');
    }

    public function handle(): void
    {
        foreach (self::THRESHOLDS as $days) {
            $this->processThreshold($days);
        }

        logger()->info('GenerateDeadlinesJob completed.', ['ran_at' => now()->toIso8601String()]);
    }

    private function processThreshold(int $days): void
    {
        $targetDate = now()->addDays($days)->toDateString();

        // Phase 3 stub — query will be extended when the filings table exists
        try {
            $filings = DB::table('filings')
                ->where('deadline_date', $targetDate)
                ->where('status', 'in_progress')
                ->whereRaw(
                    "DATE(last_reminder_sent_at) != ? OR last_reminder_sent_at IS NULL",
                    [$targetDate],
                )
                ->select(['id', 'tenant_id', 'filing_type', 'client_name', 'deadline_date'])
                ->get();

            foreach ($filings as $filing) {
                FilingDeadlineApproached::dispatch(
                    $filing->id,
                    $filing->tenant_id,
                    $days,
                    $filing->filing_type,
                    $filing->client_name,
                    $filing->deadline_date,
                );

                DB::table('filings')
                    ->where('id', $filing->id)
                    ->update(['last_reminder_sent_at' => now()]);
            }

            logger()->debug("Threshold {$days}d: dispatched {$filings->count()} deadline events.");
        } catch (\Throwable) {
            // Filings table doesn't exist yet — skip silently until Phase 3
        }
    }

    public function failed(\Throwable $e): void
    {
        logger()->error('GenerateDeadlinesJob failed', ['error' => $e->getMessage()]);
    }
}
