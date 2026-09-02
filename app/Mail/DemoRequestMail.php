<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * DemoRequestMail — internal notification sent to support team
 * when a prospect requests a product demo.
 */
final class DemoRequestMail extends BaseMailable
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $email,
        public readonly string  $company,
        public readonly ?string $teamSize = null,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Demo Request] {$this->company}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demo-request',
            with: [
                'name'     => $this->name,
                'email'    => $this->email,
                'company'  => $this->company,
                'teamSize' => $this->teamSize ?? 'Not specified',
                'appName'  => $this->appName(),
            ],
        );
    }
}
