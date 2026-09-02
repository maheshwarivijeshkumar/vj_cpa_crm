<?php

namespace App\Enums;

enum TenantPlan: string
{
    case Trial        = 'trial';
    case Starter      = 'starter';
    case Professional = 'professional';
    case Enterprise   = 'enterprise';

    public function label(): string
    {
        return match($this) {
            self::Trial        => 'Trial',
            self::Starter      => 'Starter',
            self::Professional => 'Professional',
            self::Enterprise   => 'Enterprise',
        };
    }

    public function monthlyPrice(): int
    {
        return match($this) {
            self::Trial        => 0,
            self::Starter      => 49,
            self::Professional => 99,
            self::Enterprise   => 199,
        };
    }

    public function maxClients(): ?int
    {
        return match($this) {
            self::Trial        => 10,
            self::Starter      => 50,
            self::Professional => 250,
            self::Enterprise   => null, // unlimited
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
