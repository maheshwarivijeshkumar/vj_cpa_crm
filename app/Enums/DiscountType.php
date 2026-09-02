<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Discount value type: fixed amount or percentage off.
 */
enum DiscountType: string
{
    case Fixed      = 'fixed';
    case Percentage = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::Fixed      => 'Fixed Amount',
            self::Percentage => 'Percentage',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Fixed      => 'badge-teal',
            self::Percentage => 'badge-info',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::Fixed      => '$',
            self::Percentage => '%',
        };
    }

    /** @return array<string,string> value => label */
    public static function options(): array
    {
        return array_column(
            array_map(fn (self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label', 'value',
        );
    }
}
