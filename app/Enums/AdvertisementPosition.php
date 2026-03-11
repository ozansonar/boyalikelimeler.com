<?php

declare(strict_types=1);

namespace App\Enums;

enum AdvertisementPosition: string
{
    case Sidebar = 'sidebar';
    case Tall = 'tall';

    public function label(): string
    {
        return match ($this) {
            self::Sidebar => 'Sidebar (Yan Panel)',
            self::Tall => 'Alt Bölüm (Uzun)',
        };
    }
}
