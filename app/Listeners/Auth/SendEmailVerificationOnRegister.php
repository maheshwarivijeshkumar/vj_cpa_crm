<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use App\Events\Auth\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendEmailVerificationOnRegister implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(UserRegistered $event): void
    {
        if (! $event->requiresEmailVerification) {
            return;
        }
        if ($event->user->hasVerifiedEmail()) {
            return;
        }
        $event->user->sendEmailVerificationNotification();
    }
}
