<?php

declare(strict_types=1);

namespace App\Listeners\Filing;

use App\Events\Filing\FilingDeadlineApproached;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendDeadlineReminderNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int    $tries = 3;

    public function handle(FilingDeadlineApproached $event): void
    {
        // TODO Phase 3 — sends in-app + email notification to assigned accountant
        // and optional client reminder based on tenant notification preferences.
        logger()->info('Deadline reminder queued', [
            'filing_id'   => $event->filingId,
            'tenant_id'   => $event->tenantId,
            'days_due'    => $event->daysUntilDue,
            'filing_type' => $event->filingType,
        ]);
    }
}
