<?php

namespace App\Enums;

enum SettingType: string
{
    case String  = 'string';
    case Boolean = 'boolean';
    case Integer = 'integer';
    case Float   = 'float';
    case Json    = 'json';

    public function cast(mixed $value): mixed
    {
        return match($this) {
            self::Boolean => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            self::Integer => (int) $value,
            self::Float   => (float) $value,
            self::Json    => json_decode((string) $value, true),
            default       => $value,
        };
    }

    public function serialize(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return (string) json_encode($value);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        return (string) $value;
    }
}
