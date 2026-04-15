<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PwaInstallPlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PwaInstall extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'platform',
        'user_agent',
        'referrer',
        'ip_hash',
    ];

    protected function casts(): array
    {
        return [
            'platform' => PwaInstallPlatform::class,
        ];
    }
}
