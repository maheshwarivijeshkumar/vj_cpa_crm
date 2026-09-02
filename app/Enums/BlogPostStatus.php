<?php

namespace App\Enums;

enum BlogPostStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Review => 'In Review',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Published => 'badge-success',
            self::Review => 'badge-warning',
            self::Draft => 'badge-teal',
            self::Archived => 'badge-gray',
        };
    }

    public function isVisible(): bool
    {
        return $this === self::Published;
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
