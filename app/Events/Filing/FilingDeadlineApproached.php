<?php

declare(strict_types=1);

namespace App\Events\Filing;

use App\Models\Tenant;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired by GenerateDeadlinesJob when a filing deadline is N days away.
 * Listener: SendDeadlineReminderNotification.
 */
final class FilingDeadlineApproached
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly int    $filingId,       // ID of the filing record
        public readonly int    $tenantId,
        public readonly int    $daysUntilDue,   // 30 | 14 | 7 | 3 | 1
        public readonly string $filingType,     // 'T1' | 'T2' | 'GST/HST' | etc.
        public readonly string $clientName,
        public readonly string $deadlineDate,   // ISO-8601 date
    ) {}

    public function isUrgent(): bool
    {
        return $this->daysUntilDue <= 3;
    }
}
