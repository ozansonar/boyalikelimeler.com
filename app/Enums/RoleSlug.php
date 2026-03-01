<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleSlug: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Yazar = 'yazar';
    case Kullanici = 'kullanici';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Süper Admin',
            self::Admin      => 'Admin',
            self::Yazar      => 'Yazar',
            self::Kullanici  => 'Kullanıcı',
        };
    }
}
