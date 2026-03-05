<?php

declare(strict_types=1);

namespace App\Enums;

enum ContactSubject: string
{
    case Genel     = 'genel';
    case IsBirligi = 'isbirligi';
    case Yarisma   = 'yarisma';
    case Teknik    = 'teknik';
    case Oneri     = 'oneri';
    case Diger     = 'diger';

    public function label(): string
    {
        return match ($this) {
            self::Genel     => 'Genel Bilgi',
            self::IsBirligi => 'İş Birliği Talebi',
            self::Yarisma   => 'Yarışma Hakkında',
            self::Teknik    => 'Teknik Destek',
            self::Oneri     => 'Öneri / Şikayet',
            self::Diger     => 'Diğer',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Genel     => 'blue',
            self::IsBirligi => 'green',
            self::Yarisma   => 'purple',
            self::Teknik    => 'orange',
            self::Oneri     => 'red',
            self::Diger     => 'teal',
        };
    }
}
