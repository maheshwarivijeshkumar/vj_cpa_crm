<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Lifecycle state of a single referral link click / signup.
 */
enum ReferralStatus: string
{
    case Pending   = 'pending';   // Referral link clicked, signup not complete
    case Signed    = 'signed';    // Referred tenant signed up (unverified)
    case Verified  = 'verified';  // Email verified by referred tenant
    case Rewarded  = 'rewarded';  // Referrer received points/credit
    case Expired   = 'expired';   // Signup not completed within expiry window
    case Revoked   = 'revoked';   // Fraud / policy violation

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Pending',
            self::Signed   => 'Signed Up',
            self::Verified => 'Verified',
            self::Rewarded => 'Rewarded',
            self::Expired  => 'Expired',
            self::Revoked  => 'Revoked',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending  => 'badge-warning',
            self::Signed   => 'badge-teal',
            self::Verified => 'badge-info',
            self::Rewarded => 'badge-success',
            self::Expired  => 'badge-gray',
            self::Revoked  => 'badge-danger',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Rewarded, self::Expired, self::Revoked], true);
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn (self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label', 'value',
        );
    }
}
