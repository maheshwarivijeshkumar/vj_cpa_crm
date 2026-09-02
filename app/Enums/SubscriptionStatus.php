<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active    = 'active';
    case Trial     = 'trial';
    case PastDue   = 'past_due';  // Payment failed, grace period
    case Lapsed    = 'lapsed';    // Expired and not renewed
    case Cancelled = 'cancelled'; // Cancelled by tenant or platform admin

    public function label(): string
    {
        return match ($this) {
            self::Active    => 'Active',
            self::Trial     => 'Trial',
            self::PastDue   => 'Past Due',
            self::Lapsed    => 'Lapsed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Active    => 'badge-success',
            self::Trial     => 'badge-warning',
            self::PastDue   => 'badge-danger',
            self::Lapsed    => 'badge-gray',
            self::Cancelled => 'badge-gray',
        };
    }

    public function isAccessAllowed(): bool
    {
        return in_array($this, [self::Active, self::Trial, self::PastDue], true);
    }

    /** Whether the tenant should receive a win-back discount email */
    public function shouldTriggerWinback(): bool
    {
        return $this === self::Lapsed;
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn (self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label', 'value',
        );
    }
}
