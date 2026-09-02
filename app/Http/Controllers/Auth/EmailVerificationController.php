<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * EmailVerificationController — HTTP layer only.
 *
 * Responsibilities:
 *   - Render the verify-email notice page
 *   - Delegate verification logic to EmailVerificationService
 *   - Delegate resend logic to EmailVerificationService
 *   - Decide the redirect destination
 *
 * Contains zero business logic.
 */
final class EmailVerificationController extends Controller
{
    public function __construct(
        private readonly EmailVerificationService $verificationService,
    ) {}

    /** Show the "please verify your email" notice page. */
    public function notice(): Response
    {
        return Inertia::render('Auth/VerifyEmail', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle the signed email verification link.
     * EmailVerificationRequest handles signed URL + throttle guard.
     * EmailVerificationService marks the email as verified + fires events.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $this->verificationService->verify($request->user());

        return redirect()->intended(route('dashboard') . '?verified=1');
    }

    /**
     * Resend the verification email.
     * EmailVerificationService guards against resending to already-verified users.
     */
    public function send(Request $request): RedirectResponse
    {
        $resent = $this->verificationService->resend($request->user());

        if (! $resent) {
            // Already verified — redirect straight to dashboard
            return redirect()->intended(route('dashboard'));
        }

        return back()->with('status', 'verification-link-sent');
    }
}
