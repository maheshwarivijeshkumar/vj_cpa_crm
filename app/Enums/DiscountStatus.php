<?php

declare(strict_types=1);

namespace App\Enums;

enum DiscountStatus: string
{
    case Active   = 'active';
    case Inactive = 'inactive';
    case Expired  = 'expired';
    case Depleted = 'depleted'; // Usage limit reached

    public function label(): string
    {
        return match ($this) {
            self::Active   => 'Active',
            self::Inactive => 'Inactive',
            self::Expired  => 'Expired',
            self::Depleted => 'Depleted',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Active   => 'badge-success',
            self::Inactive => 'badge-gray',
            self::Expired  => 'badge-warning',
            self::Depleted => 'badge-danger',
        };
    }

    public function isUsable(): bool
    {
        return $this === self::Active;
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn (self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label', 'value',
        );
    }
}
