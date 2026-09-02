<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use App\Events\Auth\UserRegistered;
use App\Mail\WelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class SendWelcomeEmail implements ShouldQueue
{
    public string $queue   = 'notifications';
    public int    $tries   = 3;
    public array  $backoff = [30, 120, 600];

    public function handle(UserRegistered $event): void
    {
        try {
            Mail::to($event->user->email, $event->user->full_name)
                ->send(new WelcomeMail($event->user));
        } catch (\Throwable $e) {
            // Never crash registration flow because of a mail failure
            logger()->error('SendWelcomeEmail failed', [
                'user_id' => $event->user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
