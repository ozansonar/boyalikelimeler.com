<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DailyView extends Model
{
    protected $fillable = [
        'viewable_type',
        'viewable_id',
        'view_date',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'view_date'  => 'date',
            'view_count' => 'integer',
        ];
    }

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }
}
