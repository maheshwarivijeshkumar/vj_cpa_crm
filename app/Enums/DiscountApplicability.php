<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Who the discount applies to.
 * Scope is always resolved by DiscountService — never by the frontend.
 */
enum DiscountApplicability: string
{
    case All      = 'all';      // All tenants
    case Specific = 'specific'; // Only specific tenant_ids (stored in discount_tenants pivot)
    case Plan     = 'plan';     // Only tenants on specific plan(s)

    public function label(): string
    {
        return match ($this) {
            self::All      => 'All Tenants',
            self::Specific => 'Specific Tenants',
            self::Plan     => 'By Plan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::All      => 'badge-teal',
            self::Specific => 'badge-info',
            self::Plan     => 'badge-warning',
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
