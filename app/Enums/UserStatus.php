<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active    = 'active';
    case Inactive  = 'inactive';
    case Suspended = 'suspended';
    case Invited   = 'invited';
    case Archived  = 'archived';

    public function label(): string
    {
        return match($this) {
            self::Active    => 'Active',
            self::Inactive  => 'Inactive',
            self::Suspended => 'Suspended',
            self::Invited   => 'Invited',
            self::Archived  => 'Archived',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Active    => 'badge-success',
            self::Invited   => 'badge-warning',
            self::Suspended => 'badge-danger',
            self::Inactive,
            self::Archived  => 'badge-gray',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn (self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label', 'value',
        );
    }
}
