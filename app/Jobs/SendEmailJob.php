<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * SendEmailJob — single entry-point for all queued email delivery.
 *
 * Every outbound email MUST go through this job, not Mail::queue() directly.
 * This ensures a single observable, retriable, logged path for all email.
 *
 * Pattern: carries recipient data + serialized Mailable (not a model).
 * Retries: 3 attempts with exponential backoff.
 * Failure: logs email address (class name only — no body content).
 */
final class SendEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int   $tries   = 3;
    public array $backoff = [30, 120, 600];  // 30s, 2min, 10min
    public int   $timeout = 60;

    public function __construct(
        private readonly string   $toEmail,
        private readonly string   $toName,
        private readonly Mailable $mailable,
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Prevent duplicate jobs from being dispatched for the same mailable+recipient.
     */
    public function uniqueId(): string
    {
        return md5($this->toEmail . $this->mailable::class . (string) now()->minute);
    }

    public function handle(): void
    {
        Mail::to($this->toEmail, $this->toName)->send($this->mailable);
    }

    public function failed(\Throwable $e): void
    {
        // Log class name only — never log email content (could contain PII)
        logger()->error('SendEmailJob failed', [
            'mailable' => $this->mailable::class,
            'to'       => $this->toEmail,
            'error'    => $e::class,
            'message'  => $e->getMessage(),
        ]);
    }
}
