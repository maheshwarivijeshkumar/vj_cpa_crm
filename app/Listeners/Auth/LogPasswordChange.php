<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordChanged;
use App\Services\Audit\AuditService;
use Illuminate\Contracts\Queue\ShouldQueue;

final class LogPasswordChange implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(PasswordChanged $event): void
    {
        AuditService::logAuth(
            'password_changed',
            $event->user->email,
            true,
            [
                'changed_via' => $event->changedVia,
                'ip'          => $event->ipAddress,
            ],
        );
    }
}
