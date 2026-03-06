<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LiteraryWorkStatus;
use App\Enums\LiteraryWorkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class LiteraryWork extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'literary_category_id',
        'user_id',
        'status',
        'work_type',
        'meta_title',
        'meta_description',
        'view_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id'              => 'integer',
            'literary_category_id' => 'integer',
            'status'               => LiteraryWorkStatus::class,
            'work_type'            => LiteraryWorkType::class,
            'view_count'           => 'integer',
            'published_at'         => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LiteraryCategory::class, 'literary_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approvedComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->where('is_approved', true)
            ->orderByDesc('created_at');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(LiteraryRevision::class)->orderByDesc('created_at');
    }

    public function latestRevision(): ?LiteraryRevision
    {
        return $this->revisions()->first();
    }

    public function isApproved(): bool
    {
        return $this->status === LiteraryWorkStatus::Approved;
    }

    public function isPending(): bool
    {
        return $this->status === LiteraryWorkStatus::Pending;
    }

    public function isRevisionRequested(): bool
    {
        return $this->status === LiteraryWorkStatus::RevisionRequested;
    }

    public function isUnpublished(): bool
    {
        return $this->status === LiteraryWorkStatus::Unpublished;
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }

    public function isFavoritedBy(?int $userId = null): bool
    {
        $userId ??= Auth::id();

        if (! $userId) {
            return false;
        }

        return $this->favorites()->where('user_id', $userId)->exists();
    }

    public function readingTime(): int
    {
        $wordCount = str_word_count(strip_tags((string) $this->body));
        return max(1, (int) ceil($wordCount / 200));
    }
}
