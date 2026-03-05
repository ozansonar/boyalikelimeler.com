<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'body',
        'rating',
        'is_approved',
        'approved_at',
        'approved_by',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'rating'      => 'integer',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isByUser(): bool
    {
        return $this->user_id !== null;
    }

    public function fullName(): string
    {
        if ($this->user_id && $this->relationLoaded('user') && $this->user) {
            return $this->user->name;
        }

        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function commenterEmail(): ?string
    {
        if ($this->user_id && $this->relationLoaded('user') && $this->user) {
            return $this->user->email;
        }

        return $this->email;
    }

    public function commenterInitials(): string
    {
        if ($this->user_id && $this->relationLoaded('user') && $this->user) {
            $parts = explode(' ', $this->user->name);

            return mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1))
                 . mb_strtoupper(mb_substr($parts[1] ?? '', 0, 1));
        }

        return mb_strtoupper(mb_substr($this->first_name ?? '', 0, 1))
             . mb_strtoupper(mb_substr($this->last_name ?? '', 0, 1));
    }

    public function contentTitle(): string
    {
        return $this->commentable?->title ?? '-';
    }

    public function contentType(): string
    {
        return match ($this->commentable_type) {
            LiteraryWork::class => 'icerik',
            Post::class         => 'blog',
            default             => 'diger',
        };
    }

    public function contentTypeLabel(): string
    {
        return match ($this->commentable_type) {
            LiteraryWork::class => 'İçerik',
            Post::class         => 'Blog',
            default             => 'Diğer',
        };
    }
}
