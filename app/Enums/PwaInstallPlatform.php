<?php

declare(strict_types=1);

namespace App\Enums;

enum PwaInstallPlatform: string
{
    case Android = 'android';
    case Ios = 'ios';
    case Desktop = 'desktop';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Android => 'Android',
            self::Ios     => 'iOS',
            self::Desktop => 'Masaüstü',
            self::Unknown => 'Bilinmeyen',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Android => 'green',
            self::Ios     => 'blue',
            self::Desktop => 'purple',
            self::Unknown => 'orange',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Android => 'fa-brands fa-android',
            self::Ios     => 'fa-brands fa-apple',
            self::Desktop => 'fa-solid fa-desktop',
            self::Unknown => 'fa-solid fa-circle-question',
        };
    }
}
