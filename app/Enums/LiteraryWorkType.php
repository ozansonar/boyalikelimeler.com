<?php

declare(strict_types=1);

namespace App\Enums;

enum LiteraryWorkType: string
{
    case Written = 'written';
    case Visual  = 'visual';

    public function label(): string
    {
        return match ($this) {
            self::Written => 'Yazılı Eser',
            self::Visual  => 'Görsel Eser',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Written => 'bi-journal-text',
            self::Visual  => 'bi-palette',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Written => 'active',
            self::Visual  => 'info',
        };
    }
}
