<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * WelcomeMail — sent to new users after registration.
 *
 * Template stub: resources/views/emails/welcome.blade.php
 * Phase 9: replaced by NotificationTemplate 'auth.welcome' via TemplateResolverService.
 */
final class WelcomeMail extends BaseMailable
{
    public function __construct(public readonly User $user)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to {$this->appName()} — Your account is ready",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'user'      => $this->user,
                'loginUrl'  => $this->appUrl() . '/login',
                'appName'   => $this->appName(),
                'trialDays' => config('cpa.trial_days', 14),
                'firmName'  => $this->user->tenant?->name ?? $this->appName(),
            ],
        );
    }
}
