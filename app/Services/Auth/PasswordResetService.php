<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Events\Auth\PasswordChanged;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * PasswordResetService — business logic for forgot-password and reset flows.
 *
 * Responsibilities:
 *  - Send a password reset link via Laravel's Password broker
 *  - Validate the reset token and apply the new password
 *  - Clear must_change_password flag after a successful reset
 *  - Fire PasswordReset (Laravel built-in) + PasswordChanged (our domain event)
 *  - Return the broker status string for the controller to translate
 */
final class PasswordResetService
{
    /**
     * Send a password reset link to the given email.
     *
     * @return string  Password broker status constant (Password::RESET_LINK_SENT or error)
     */
    public function sendResetLink(string $email): string
    {
        return Password::sendResetLink(compact('email'));
    }

    /**
     * Reset the password using the broker.
     *
     * The $callback receives the User and new password if you need custom logic.
     * Here we encapsulate all mutations inside this service.
     *
     * @param  array{email:string, password:string, password_confirmation:string, token:string} $data
     * @return string  Password broker status constant (Password::PASSWORD_RESET or error)
     */
    public function reset(array $data): string
    {
        $ipAddress = request()->ip() ?? '';

        $status = Password::reset(
            $data,
            function (User $user, string $password) use ($ipAddress): void {
                $user->forceFill([
                    'password'             => Hash::make($password),
                    'remember_token'       => Str::random(60),
                    'must_change_password' => false,
                ])->saveQuietly(); // saveQuietly — avoid triggering unrelated model observers

                // Laravel built-in event (revokes remember tokens, etc.)
                event(new PasswordReset($user));

                // Our domain event → LogPasswordChange listener (audit)
                PasswordChanged::dispatch($user, 'reset', $ipAddress);
            },
        );

        return $status;
    }
}
