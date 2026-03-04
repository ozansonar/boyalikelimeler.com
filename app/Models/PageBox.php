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
        'title',
        'description',
        'link',
        'link_target',
        'image',
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

    public function bootstrapColClass(): string
    {
        return sprintf('col-%d col-md-%d col-lg-%d', $this->col_mobile, $this->col_tablet, $this->col_desktop);
    }
}
