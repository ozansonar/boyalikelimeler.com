<?php

declare(strict_types=1);

namespace App\Enums;

enum QnaStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Onay Bekliyor',
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

    public function icon(): string
    {
        return match ($this) {
            self::Pending  => 'fa-solid fa-hourglass-half',
            self::Approved => 'fa-solid fa-check-circle',
            self::Rejected => 'fa-solid fa-times-circle',
        };
    }
}
