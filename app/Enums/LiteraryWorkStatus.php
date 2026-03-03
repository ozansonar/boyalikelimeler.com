<?php

declare(strict_types=1);

namespace App\Enums;

enum LiteraryWorkStatus: string
{
    case Pending           = 'pending';
    case Approved          = 'approved';
    case Rejected          = 'rejected';
    case RevisionRequested = 'revision_requested';
    case Unpublished       = 'unpublished';

    public function label(): string
    {
        return match ($this) {
            self::Pending           => 'Beklemede',
            self::Approved          => 'Onaylandı',
            self::Rejected          => 'Reddedildi',
            self::RevisionRequested => 'Revize Bekleniyor',
            self::Unpublished       => 'Yayından Kaldırıldı',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending           => 'pending',
            self::Approved          => 'active',
            self::Rejected          => 'inactive',
            self::RevisionRequested => 'info',
            self::Unpublished       => 'inactive',
        };
    }
}
