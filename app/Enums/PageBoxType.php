<?php

declare(strict_types=1);

namespace App\Enums;

enum PageBoxType: string
{
    case Image = 'image';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Görsel',
            self::Video => 'Video',
        };
    }
}
