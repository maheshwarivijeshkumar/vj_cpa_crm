<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Audit\AuditService;
use Illuminate\Auth\Events\Verified;

/**
 * EmailVerificationService — all business logic for email verification.
 *
 * Responsibilities:
 *  - Check if a user already has a verified email (prevent duplicate work)
 *  - Mark email as verified and fire the Verified event
 *  - Resend the verification notification (rate-limited at route level)
 *  - Write audit log on successful verification
 *
 * The controller only resolves the redirect destination.
 */
final class EmailVerificationService
{
    /**
     * Verify the user's email address.
     *
     * Returns true if this call did the verification,
     * false if email was already verified (already-verified redirect).
     */
    public function verify(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false; // Already verified — controller redirects to dashboard
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));

            AuditService::logAuth('email_verified', $user->email, true);
        }

        return true;
    }

    /**
     * Resend the email verification notification.
     *
     * Returns false if the user is already verified (no need to resend).
     */
    public function resend(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false; // Already verified — controller skips the resend
        }

        $user->sendEmailVerificationNotification();

        return true;
    }
}
