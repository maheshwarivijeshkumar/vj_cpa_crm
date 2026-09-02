<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * ContactFormMail — internal notification for marketing contact form submissions.
 */
final class ContactFormMail extends BaseMailable
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $email,
        public readonly string  $subject,
        public readonly string  $message,
        public readonly ?string $company = null,
    ) {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Contact] {$this->subject}",
            replyTo: [$this->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
            with: [
                'name'    => $this->name,
                'email'   => $this->email,
                'company' => $this->company,
                'subject' => $this->subject,
                'message' => $this->message,
                'appName' => $this->appName(),
            ],
        );
    }
}
