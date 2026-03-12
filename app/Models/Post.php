<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'category_id',
        'user_id',
        'status',
        'meta_title',
        'meta_description',
        'is_featured',
        'allow_comments',
        'sort_order',
        'view_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status'         => PostStatus::class,
            'is_featured'    => 'boolean',
            'allow_comments' => 'boolean',
            'sort_order'     => 'integer',
            'view_count'     => 'integer',
            'published_at'   => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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

    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published;
    }

    public function dailyViews(): MorphMany
    {
        return $this->morphMany(DailyView::class, 'viewable');
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

    public function url(): string
    {
        return route('blog.show', [$this->category?->slug ?? 'genel', $this->slug]);
    }
}
