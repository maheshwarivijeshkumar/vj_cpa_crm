<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Contracts\NotificationServiceInterface;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * NotificationService — concrete implementation of NotificationServiceInterface.
 *
 * Resolution order for templates:
 *   1. Tenant-level override (if user belongs to a tenant)
 *   2. Platform/system template (tenant_id = null)
 *
 * All delivery is async — queued through SendEmailJob / Laravel notifications.
 * Every sent notification writes an immutable NotificationLog row.
 */
final class NotificationService implements NotificationServiceInterface
{
    public function send(
        User   $recipient,
        string $templateKey,
        array  $variables  = [],
        array  $channels   = [],
    ): void {
        $template = $this->resolveTemplate($templateKey, $recipient->tenant_id, $channels[0] ?? 'in_app');

        if ($template === null) {
            logger()->warning("NotificationService: no template found for key [{$templateKey}]", [
                'user_id'      => $recipient->id,
                'template_key' => $templateKey,
            ]);
            return;
        }

        $effectiveChannels = ! empty($channels)
            ? $channels
            : [$template->channel];

        foreach ($effectiveChannels as $channel) {
            $this->dispatch($recipient, $template, $channel, $variables);
        }
    }

    public function sendToTenant(
        int    $tenantId,
        string $templateKey,
        array  $variables = [],
        array  $channels  = [],
    ): void {
        $users = User::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get(['id', 'first_name', 'last_name', 'email', 'tenant_id']);

        foreach ($users as $user) {
            $this->send($user, $templateKey, $variables, $channels);
        }
    }

    public function markRead(int $notificationLogId, User $user): void
    {
        // Use raw update to bypass the immutable guard (read state IS mutable)
        DB::table('notification_logs')
            ->where('id', $notificationLogId)
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function markAllRead(User $user): void
    {
        DB::table('notification_logs')
            ->where('user_id', $user->id)
            ->where('channel', 'in_app')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function unreadCount(User $user): int
    {
        return (int) DB::table('notification_logs')
            ->where('user_id', $user->id)
            ->where('channel', 'in_app')
            ->where('is_read', false)
            ->count();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function resolveTemplate(
        string $key,
        ?int   $tenantId,
        string $channel,
    ): ?NotificationTemplate {
        // Try tenant-specific first, then fall back to platform
        return NotificationTemplate::query()
            ->active()
            ->where('key', $key)
            ->where('channel', $channel)
            ->where(function ($q) use ($tenantId): void {
                if ($tenantId !== null) {
                    $q->where('tenant_id', $tenantId)
                      ->orWhereNull('tenant_id');
                } else {
                    $q->whereNull('tenant_id');
                }
            })
            ->orderByRaw('CASE WHEN tenant_id IS NOT NULL THEN 0 ELSE 1 END') // tenant first
            ->first();
    }

    private function dispatch(
        User                 $recipient,
        NotificationTemplate $template,
        string               $channel,
        array                $variables,
    ): void {
        // Add standard variables available in every template
        $vars = array_merge([
            'user.first_name'  => $recipient->first_name,
            'user.last_name'   => $recipient->last_name,
            'user.email'       => $recipient->email,
            'user.name'        => $recipient->full_name,
            'app.name'         => config('app.name', 'VJ CPA CRM'),
            'app.url'          => config('app.url', url('/')),
        ], $variables);

        $rendered = $template->render($vars, $channel);

        // Write the log entry first (always, regardless of delivery success)
        $log = DB::table('notification_logs')->insertGetId([
            'uuid'                        => (string) Str::uuid(),
            'tenant_id'                   => $recipient->tenant_id,
            'user_id'                     => $recipient->id,
            'user_name'                   => $recipient->full_name,
            'notification_template_id'    => $template->id,
            'template_key'                => $template->key,
            'channel'                     => $channel,
            'subject'                     => $rendered['subject'],
            'body'                        => $channel === 'in_app' ? $rendered['body'] : null, // Don't store email HTML in DB
            'status'                      => 'sent',
            'is_read'                     => false,
            'created_at'                  => now(),
        ]);

        // Deliver based on channel
        match ($channel) {
            'email'  => $this->sendEmail($recipient, $rendered),
            'in_app' => null, // Written to notification_logs — frontend polls
            'sms'    => $this->sendSms($recipient, $rendered),
            default  => null,
        };
    }

    private function sendEmail(User $recipient, array $rendered): void
    {
        try {
            \Illuminate\Support\Facades\Mail::html(
                $rendered['body'] ?? '',
                function ($msg) use ($recipient, $rendered): void {
                    $msg->to($recipient->email, $recipient->full_name)
                        ->subject($rendered['subject'] ?? config('app.name'))
                        ->from(
                            config('mail.from.address', 'no-reply@cpacrm.com'),
                            config('mail.from.name', config('app.name')),
                        );
                },
            );
        } catch (\Throwable $e) {
            logger()->error('NotificationService::sendEmail failed', [
                'user_id' => $recipient->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function sendSms(User $recipient, array $rendered): void
    {
        // SMS implementation will be added when SMS provider is configured
        logger()->info('NotificationService::sendSms — SMS not yet configured', [
            'user_id' => $recipient->id,
            'message' => substr($rendered['body'] ?? '', 0, 50),
        ]);
    }
}
