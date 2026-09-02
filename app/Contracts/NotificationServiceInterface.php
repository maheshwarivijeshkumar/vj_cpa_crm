<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

/**
 * NotificationServiceInterface
 *
 * Central contract for all in-app + email + SMS notifications.
 * Implementations resolve the correct template, render variables,
 * dispatch through the right channel, and write to notification_logs.
 */
interface NotificationServiceInterface
{
    /**
     * Send a notification to a specific user using a template key.
     *
     * @param User                 $recipient   The receiving user
     * @param string               $templateKey  e.g. 'deadline.reminder', 'invoice.created'
     * @param array<string,mixed>  $variables    Template variable replacements
     * @param array<string>        $channels     e.g. ['in_app','email'] — empty = use template defaults
     */
    public function send(
        User   $recipient,
        string $templateKey,
        array  $variables = [],
        array  $channels  = [],
    ): void;

    /**
     * Send a notification to all active users within a tenant.
     *
     * @param int                  $tenantId
     * @param string               $templateKey
     * @param array<string,mixed>  $variables
     * @param array<string>        $channels
     */
    public function sendToTenant(
        int    $tenantId,
        string $templateKey,
        array  $variables = [],
        array  $channels  = [],
    ): void;

    /**
     * Mark a single in-app notification log entry as read.
     */
    public function markRead(int $notificationLogId, User $user): void;

    /**
     * Mark all in-app notifications for a user as read.
     */
    public function markAllRead(User $user): void;

    /**
     * Return unread notification count for a user.
     */
    public function unreadCount(User $user): int;
}
