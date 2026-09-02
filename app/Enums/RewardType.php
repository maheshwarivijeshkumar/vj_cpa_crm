<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The form in which a referral reward is given to the referrer tenant.
 */
enum RewardType: string
{
    case Points  = 'points';  // Redeemable points balance
    case Credit  = 'credit';  // Direct billing credit applied to next invoice

    public function label(): string
    {
        return match ($this) {
            self::Points => 'Points',
            self::Credit => 'Billing Credit',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Points => 'badge-teal',
            self::Credit => 'badge-success',
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
