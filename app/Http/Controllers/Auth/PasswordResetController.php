<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\PasswordResetService;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * PasswordResetController — HTTP layer only.
 *
 * Responsibilities:
 *   - Render forgot-password and reset-password pages
 *   - Pass data to PasswordResetService
 *   - Redirect or flash based on broker status string
 *
 * Contains zero business logic.
 * Password broker interaction, token handling, and event dispatch are in PasswordResetService.
 */
final class PasswordResetController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService,
    ) {}

    // ── Forgot password ───────────────────────────────────────────────────────

    /** Show the forgot-password page. */
    public function requestForm(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Send the password reset link.
     * ForgotPasswordRequest validates the email field.
     * PasswordResetService sends the link via the broker.
     */
    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = $this->passwordResetService->sendResetLink(
            $request->validated('email'),
        );

        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // ── Reset password ────────────────────────────────────────────────────────

    /** Show the password-reset form pre-filled with token + email. */
    public function resetForm(string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => request()->string('email', '')->toString(),
        ]);
    }

    /**
     * Apply the password reset.
     * ResetPasswordRequest validates token, email, and password.
     * PasswordResetService applies the reset + fires events.
     */
    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $status = $this->passwordResetService->reset($request->validated());

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
