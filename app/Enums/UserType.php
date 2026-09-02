<?php

namespace App\Enums;

enum UserType: string
{
    case PlatformAdmin = 'platform_admin';
    case FirmOwner     = 'firm_owner';
    case FirmUser      = 'firm_user';
    case Client        = 'client';

    public function label(): string
    {
        return match($this) {
            self::PlatformAdmin => 'Platform Admin',
            self::FirmOwner     => 'Firm Owner',
            self::FirmUser      => 'Staff',
            self::Client        => 'Client',
        };
    }

    public function isPlatformLevel(): bool
    {
        return $this === self::PlatformAdmin;
    }

    public function isFirmLevel(): bool
    {
        return in_array($this, [self::FirmOwner, self::FirmUser], true);
    }

    /** @return array<string,string>  value => label */
    public static function options(): array
    {
        return array_column(
            array_map(fn (self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label', 'value',
        );
    }
}
