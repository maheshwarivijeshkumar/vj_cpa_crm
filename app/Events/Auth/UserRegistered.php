<?php

declare(strict_types=1);

namespace App\Events\Auth;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a new user account is created (registration or invite acceptance).
 * Listeners: SendWelcomeEmail, SendEmailVerificationOnRegister.
 */
final class UserRegistered
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly bool $requiresEmailVerification = true,
    ) {}
}
