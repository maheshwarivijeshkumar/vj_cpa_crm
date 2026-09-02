<?php

declare(strict_types=1);

namespace App\Events\Auth;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a user's password is changed (reset, forced change, or user-initiated).
 * Listener: LogPasswordChange (audit + revoke all sessions).
 */
final class PasswordChanged
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly string $changedVia, // 'reset' | 'user_request' | 'admin_force' | 'first_login'
        public readonly string $ipAddress,
    ) {}
}
