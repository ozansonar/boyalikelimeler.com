<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LiteraryWorkStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function readingTime(): int
    {
        $wordCount = str_word_count(strip_tags((string) $this->body));
        return max(1, (int) ceil($wordCount / 200));
    }
}
