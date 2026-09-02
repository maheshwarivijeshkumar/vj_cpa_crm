<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use App\Events\Auth\UserRegistered;

/**
 * SendEmailVerificationOnRegister
 *
 * Dispatched synchronously — email verification is a hard gate for login,
 * so it MUST be sent before the user leaves the registration page.
 *
 * WelcomeMail is queued separately; this one is intentionally synchronous
 * so the user gets the link immediately, even without a running queue worker.
 *
 * Rule: Never implements ShouldQueue for account-access emails.
 */
final class SendEmailVerificationOnRegister
{
    public function handle(UserRegistered $event): void
    {
        if (! $event->requiresEmailVerification) {
            return;
        }

        if ($event->user->hasVerifiedEmail()) {
            return;
        }

        try {
            $event->user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            // Log failure but do NOT crash the registration response.
            // The VerifyEmail page provides a resend button as fallback.
            logger()->error('SendEmailVerificationOnRegister failed', [
                'user_id' => $event->user->id,
                'email'   => $event->user->email,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
