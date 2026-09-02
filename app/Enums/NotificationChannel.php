<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case InApp = 'in_app';
    case Email = 'email';
    case Sms = 'sms';
    case WhatsApp = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::InApp => 'In-App',
            self::Email => 'Email',
            self::Sms => 'SMS',
            self::WhatsApp => 'WhatsApp',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn(self $e) => ['value' => $e->value, 'label' => $e->label()], self::cases()),
            'label',
            'value',
        );
    }
}
