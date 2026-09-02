<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $firmName  = $notifiable->tenant?->name ?? config('app.name');
        $trialDays = config('cpa.trial_days', 14);
        $loginUrl  = url('/login');

        return (new MailMessage())
            ->subject("Welcome to " . config('app.name') . " — Your account is ready")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Welcome to **" . config('app.name') . "**! Your firm **{$firmName}** has been set up and your account is ready.")
            ->line("You're on a **{$trialDays}-day free trial** with full access to all features. No credit card required.")
            ->action('Log in to your account', $loginUrl)
            ->line("Here's a quick overview of what you can do:")
            ->line("• **Manage clients** — Add all your clients and organise them by type")
            ->line("• **Track filings** — Never miss a deadline with our filing engine")
            ->line("• **Automate workflows** — Set up recurring tasks and reminders")
            ->line("• **Send documents** — Request and share documents securely")
            ->line("If you have any questions, reply to this email or visit our help centre.")
            ->salutation("— The " . config('app.name') . " Team");
    }
}
