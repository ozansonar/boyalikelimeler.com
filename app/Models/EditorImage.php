<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EditorImage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'path',
        'original_name',
        'file_size',
        'width',
        'height',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'width'     => 'integer',
        'height'    => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): ?string
    {
        return upload_url($this->path);
    }

    public function getThumbUrlAttribute(): ?string
    {
        return upload_url($this->path, 'thumb');
    }
}
