<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * BaseMailable — all CPA CRM mailables extend this.
 *
 * Enforces:
 *  - Consistent from address from config
 *  - Reply-to from cpa.support_email
 *  - Queued on 'notifications' queue by default
 *  - Template key convention: emails.{key}
 *
 * Future: Phase 9 will replace the blade stub templates with
 * database-driven NotificationTemplate records via TemplateResolverService.
 * At that point override render() here — all subclasses gain it for free.
 */
abstract class BaseMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->from(
            config('mail.from.address', 'no-reply@cpacrm.com'),
            config('mail.from.name',    config('app.name', 'VJ CPA CRM')),
        );

        $this->replyTo(
            config('cpa.support_email', 'support@cpacrm.com'),
            config('app.name', 'VJ CPA CRM') . ' Support',
        );

        $this->onQueue('notifications');
    }

    /**
     * Shared envelope metadata — subclasses call parent::envelope()
     * then modify subject as needed.
     */
    protected function appName(): string
    {
        return config('app.name', 'VJ CPA CRM');
    }

    protected function appUrl(): string
    {
        return rtrim(config('app.url', url('/')), '/');
    }
}
