<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoldenPenPeriod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'starts_at',
        'ends_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at'   => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        $today = now()->toDateString();

        return $this->starts_at?->toDateString() <= $today
            && $this->ends_at?->toDateString() >= $today;
    }
}
