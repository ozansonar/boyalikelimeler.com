<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QnaStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QnaQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'qna_category_id',
        'title',
        'slug',
        'body',
        'status',
        'view_count',
        'like_count',
        'answer_count',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'user_id'          => 'integer',
            'qna_category_id'  => 'integer',
            'status'           => QnaStatus::class,
            'view_count'       => 'integer',
            'like_count'       => 'integer',
            'answer_count'     => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QnaCategory::class, 'qna_category_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QnaAnswer::class);
    }

    public function approvedAnswers(): HasMany
    {
        return $this->hasMany(QnaAnswer::class)->where('status', QnaStatus::Approved);
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

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('qna_category_id', $categoryId);
    }

    public function isApproved(): bool
    {
        return $this->status === QnaStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->status === QnaStatus::Pending;
    }

    public function hasApprovedAnswers(): bool
    {
        return $this->approvedAnswers()->exists();
    }
}
