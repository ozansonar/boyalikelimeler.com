<?php

declare(strict_types=1);

namespace App\Enums;

enum LinkTarget: string
{
    case Self  = '_self';
    case Blank = '_blank';

    public function label(): string
    {
        return match ($this) {
            self::Self  => 'Aynı Sekme',
            self::Blank => 'Yeni Sekme',
        };
    }
}
