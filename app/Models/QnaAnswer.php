<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QnaStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QnaAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'qna_question_id',
        'user_id',
        'body',
        'status',
        'like_count',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'status'     => QnaStatus::class,
            'like_count' => 'integer',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QnaQuestion::class, 'qna_question_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(QnaLike::class, 'likeable');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', QnaStatus::Approved);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', QnaStatus::Pending);
    }

    public function isApproved(): bool
    {
        return $this->status === QnaStatus::Approved;
    }
}
