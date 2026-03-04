<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageBox extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'page_id',
        'type',
        'title',
        'description',
        'link',
        'link_target',
        'image',
        'video_url',
        'col_desktop',
        'col_tablet',
        'col_mobile',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'col_desktop' => 'integer',
            'col_tablet'  => 'integer',
            'col_mobile'  => 'integer',
            'sort_order'  => 'integer',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function youtubeId(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->video_url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public function bootstrapColClass(): string
    {
        return sprintf('col-%d col-md-%d col-lg-%d', $this->col_mobile, $this->col_tablet, $this->col_desktop);
    }
}
