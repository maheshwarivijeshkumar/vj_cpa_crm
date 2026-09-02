<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Mail\PasswordResetMail;

final class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $token) {}

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        // Delegate to the branded PasswordResetMail mailable via MailMessage
        return (new \Illuminate\Notifications\Messages\MailMessage())
            ->view('emails.password-reset', [
                'user'      => $notifiable,
                'resetUrl'  => url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->email], false)),
                'appName'   => config('app.name', 'VJ CPA CRM'),
                'expiresIn' => config('auth.passwords.users.expire', 60),
            ]);
    }
}
