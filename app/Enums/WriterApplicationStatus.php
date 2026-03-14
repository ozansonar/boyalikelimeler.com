<?php

declare(strict_types=1);

namespace App\Enums;

enum WriterApplicationStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Beklemede',
            self::Approved => 'Onaylandı',
            self::Rejected => 'Reddedildi',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending  => 'pending',
            self::Approved => 'active',
            self::Rejected => 'inactive',
        };
    }
}
