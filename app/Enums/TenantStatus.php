<?php

namespace App\Enums;

enum TenantStatus: string
{
    case Trial     = 'trial';
    case Active    = 'active';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Trial     => 'Trial',
            self::Active    => 'Active',
            self::Suspended => 'Suspended',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Active    => 'badge-success',
            self::Trial     => 'badge-warning',
            self::Suspended => 'badge-danger',
            self::Cancelled => 'badge-gray',
        };
    }

    public function isOperational(): bool
    {
        return in_array($this, [self::Trial, self::Active], true);
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn (self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label', 'value',
        );
    }
}
