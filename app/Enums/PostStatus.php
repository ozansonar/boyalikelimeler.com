<?php

declare(strict_types=1);

namespace App\Enums;

enum PostStatus: string
{
    case Draft     = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';
    case Archived  = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Taslak',
            self::Published => 'Yayında',
            self::Scheduled => 'Zamanlanmış',
            self::Archived  => 'Arşiv',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft     => 'pending',
            self::Published => 'active',
            self::Scheduled => 'info',
            self::Archived  => 'inactive',
        };
    }
}
