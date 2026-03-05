<?php

declare(strict_types=1);

namespace App\Enums;

enum MailLogStatus: string
{
    case Pending = 'pending';
    case Sent    = 'sent';
    case Failed  = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Beklemede',
            self::Sent    => 'Gönderildi',
            self::Failed  => 'Başarısız',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'orange',
            self::Sent    => 'green',
            self::Failed  => 'red',
        };
    }
}
