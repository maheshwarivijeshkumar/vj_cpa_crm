<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * What triggers a discount to be created / sent to a tenant.
 * Used by the DiscountService to automatically generate and email codes.
 */
enum DiscountTrigger: string
{
    case Manual       = 'manual';        // Platform admin creates manually
    case Welcome      = 'welcome';       // New tenant welcome discount
    case Winback      = 'winback';       // Subscription lapsed 30+ days — win-back campaign
    case Referral     = 'referral';      // Referred a new tenant successfully
    case Anniversary  = 'anniversary';   // Subscription anniversary (1yr, 2yr, etc.)
    case LoyaltyTier  = 'loyalty_tier';  // Reached a referral point milestone

    public function label(): string
    {
        return match ($this) {
            self::Manual      => 'Manual',
            self::Welcome     => 'Welcome Offer',
            self::Winback     => 'Win-Back Campaign',
            self::Referral    => 'Referral Reward',
            self::Anniversary => 'Anniversary Reward',
            self::LoyaltyTier => 'Loyalty Tier Reward',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Manual      => 'badge-gray',
            self::Welcome     => 'badge-success',
            self::Winback     => 'badge-warning',
            self::Referral    => 'badge-info',
            self::Anniversary => 'badge-teal',
            self::LoyaltyTier => 'badge-teal-mid',
        };
    }

    /** Whether this trigger type should auto-email the code to the tenant */
    public function shouldAutoEmail(): bool
    {
        return in_array($this, [self::Welcome, self::Winback, self::Referral, self::Anniversary, self::LoyaltyTier], true);
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn (self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label', 'value',
        );
    }
}
