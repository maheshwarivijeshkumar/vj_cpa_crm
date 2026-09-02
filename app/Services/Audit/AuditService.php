<?php

declare(strict_types=1);

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * AuditService — central audit logging service.
 *
 * Usage:
 *   AuditService::log('created', $client);
 *   AuditService::logChange('updated', $invoice, $old, $new);
 *   AuditService::logAuth('login', $email, true);
 *
 * Rules:
 *   - Logs are immutable — AuditLog model blocks updates/deletes
 *   - Call from Services only, never from Controllers
 *   - Never log passwords, secrets, or tokens in old/new values
 */
final class AuditService
{
    /** Sensitive fields redacted from before/after diffs */
    private const REDACT = [
        'password', 'password_confirmation', 'two_factor_secret',
        'two_factor_recovery_codes', 'remember_token', 'api_key',
        'api_secret', 'token', 'secret',
    ];

    /**
     * Log a generic domain event (create, delete, restore, export, etc.).
     *
     * @param  string      $eventType   e.g. 'created', 'deleted', 'export'
     * @param  Model|null  $resource    The Eloquent model being acted on
     * @param  array       $metadata    Extra context (module-specific data)
     * @param  string|null $description Human-readable override
     */
    public static function log(
        string  $eventType,
        ?Model  $resource    = null,
        array   $metadata    = [],
        ?string $description = null,
    ): void {
        try {
            $user = Auth::user();

            AuditLog::create([
                'tenant_id'      => $user?->tenant_id,
                'user_id'        => $user?->id,
                'user_name'      => $user ? "{$user->first_name} {$user->last_name}" : null,
                'event_type'     => $eventType,
                'module'         => $resource ? self::moduleFromModel($resource) : null,
                'resource_type'  => $resource ? get_class($resource) : null,
                'resource_id'    => $resource?->getKey() !== null ? (string) $resource->getKey() : null,
                'resource_label' => $resource ? self::labelFromModel($resource) : null,
                'description'    => $description ?? self::buildDescription($eventType, $resource),
                'ip_address'     => self::ipAddress(),
                'user_agent'     => self::userAgent(),
                'metadata'       => empty($metadata) ? null : $metadata,
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging crash the application
            logger()->error('AuditService::log failed', [
                'error'     => $e->getMessage(),
                'eventType' => $eventType,
            ]);
        }
    }

    /**
     * Log a field-level change with before/after values.
     * Only changed fields are stored — unchanged ones are stripped.
     *
     * @param  string  $eventType  e.g. 'updated', 'status_changed'
     * @param  Model   $resource   The changed model
     * @param  array   $oldValues  Attribute values before the change
     * @param  array   $newValues  Attribute values after the change
     */
    public static function logChange(
        string  $eventType,
        Model   $resource,
        array   $oldValues,
        array   $newValues,
        ?string $description = null,
    ): void {
        try {
            $user = Auth::user();

            // Diff: keep only changed fields
            $changedKeys = array_keys(array_diff_assoc($newValues, $oldValues));
            $filteredOld = array_intersect_key($oldValues, array_flip($changedKeys));
            $filteredNew = array_intersect_key($newValues, array_flip($changedKeys));

            AuditLog::create([
                'tenant_id'      => $user?->tenant_id,
                'user_id'        => $user?->id,
                'user_name'      => $user ? "{$user->first_name} {$user->last_name}" : null,
                'event_type'     => $eventType,
                'module'         => self::moduleFromModel($resource),
                'resource_type'  => get_class($resource),
                'resource_id'    => (string) $resource->getKey(),
                'resource_label' => self::labelFromModel($resource),
                'description'    => $description ?? self::buildDescription($eventType, $resource),
                'old_values'     => self::redact($filteredOld),
                'new_values'     => self::redact($filteredNew),
                'ip_address'     => self::ipAddress(),
                'user_agent'     => self::userAgent(),
            ]);
        } catch (\Throwable $e) {
            logger()->error('AuditService::logChange failed', [
                'error'     => $e->getMessage(),
                'eventType' => $eventType,
            ]);
        }
    }

    /**
     * Log a financial event (posting, reversals, voids — immutable records).
     */
    public static function logFinancial(
        string  $eventType,
        Model   $resource,
        array   $metadata    = [],
        ?string $description = null,
    ): void {
        self::log(
            $eventType,
            $resource,
            array_merge(['financial' => true], $metadata),
            $description,
        );
    }

    /**
     * Log an authentication event (login, logout, register, password_changed, etc.).
     *
     * @param  string  $eventType   e.g. 'login', 'logout', 'register', 'login_failed'
     * @param  string  $email       The email address involved
     * @param  bool    $successful  Whether the action succeeded
     * @param  array   $extra       Additional context (IP, device, etc.)
     */
    public static function logAuth(
        string $eventType,
        string $email,
        bool   $successful,
        array  $extra = [],
    ): void {
        try {
            $user = Auth::user();

            AuditLog::create([
                'tenant_id'      => $user?->tenant_id,
                'user_id'        => $user?->id,
                'user_name'      => $email,
                'event_type'     => $eventType,
                'module'         => 'auth',
                'resource_type'  => 'auth',
                'resource_id'    => null,
                'resource_label' => $email,
                'description'    => ucfirst(str_replace('_', ' ', $eventType))
                    . ' — ' . ($successful ? 'success' : 'failed'),
                'ip_address'     => self::ipAddress(),
                'user_agent'     => self::userAgent(),
                'metadata'       => array_merge(['successful' => $successful], $extra),
            ]);
        } catch (\Throwable $e) {
            logger()->error('AuditService::logAuth failed', ['error' => $e->getMessage()]);
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private static function buildDescription(string $eventType, ?Model $resource): string
    {
        if ($resource === null) {
            return ucfirst(str_replace('_', ' ', $eventType));
        }

        $action = ucfirst(str_replace('_', ' ', $eventType));
        $type   = class_basename($resource);
        $label  = self::labelFromModel($resource);

        return "{$action}: {$type} — {$label}";
    }

    private static function labelFromModel(Model $resource): string
    {
        foreach (['name', 'title', 'full_name', 'email', 'code', 'number', 'reference'] as $attr) {
            $value = $resource->getAttribute($attr);
            if (! empty($value)) {
                return (string) $value;
            }
        }

        return '#' . $resource->getKey();
    }

    private static function moduleFromModel(Model $resource): string
    {
        $map = [
            'Client'       => 'clients',
            'Lead'         => 'crm',
            'Contact'      => 'contacts',
            'Entity'       => 'entities',
            'Engagement'   => 'engagements',
            'Filing'       => 'filings',
            'TaxReturn'    => 'taxation',
            'Deadline'     => 'deadlines',
            'Task'         => 'tasks',
            'TimeEntry'    => 'time',
            'Document'     => 'documents',
            'Invoice'      => 'invoicing',
            'Payment'      => 'payments',
            'JournalEntry' => 'accounting',
            'User'         => 'users',
            'Role'         => 'roles',
            'Office'       => 'offices',
            'Tenant'       => 'platform',
            'BlogPost'     => 'blog',
            'NotificationLog' => 'notifications',
        ];

        return $map[class_basename($resource)] ?? strtolower(class_basename($resource));
    }

    private static function redact(array $values): array
    {
        foreach (self::REDACT as $field) {
            if (array_key_exists($field, $values)) {
                $values[$field] = '[REDACTED]';
            }
        }

        return $values;
    }

    private static function ipAddress(): ?string
    {
        return request()?->ip();
    }

    private static function userAgent(): ?string
    {
        return request()?->userAgent();
    }
}
