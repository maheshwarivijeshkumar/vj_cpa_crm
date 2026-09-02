<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Mail\Mailable;

/**
 * MailProviderInterface
 *
 * Abstraction over Laravel's Mail facade.
 * Allows swapping to a different mail transport (Postmark, Resend, SES) by
 * binding a different implementation in CpaServiceProvider, without touching
 * any calling code.
 *
 * All queued mails MUST use SendEmailJob rather than calling Mail::queue()
 * directly — that way all email dispatch flows through one observable path.
 */
interface MailProviderInterface
{
    /**
     * Send a mailable immediately (synchronous — for critical system mails only).
     * For all user-facing mails, use queue() instead.
     */
    public function send(string $to, string $name, Mailable $mailable): void;

    /**
     * Queue a mailable for async delivery.
     *
     * @param string    $to       Recipient email
     * @param string    $name     Recipient display name
     * @param Mailable  $mailable The mailable instance
     * @param string    $queue    Queue name (default: 'notifications')
     */
    public function queue(
        string   $to,
        string   $name,
        Mailable $mailable,
        string   $queue = 'notifications',
    ): void;

    /**
     * Send a mailable to multiple recipients (BCC pattern for bulk).
     *
     * @param array<array{email:string,name:string}> $recipients
     */
    public function sendBulk(array $recipients, Mailable $mailable): void;
}
