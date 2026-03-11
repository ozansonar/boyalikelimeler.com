<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AdvertisementPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'position',
        'image',
        'link',
        'link_target',
        'is_active',
        'start_date',
        'end_date',
        'sort_order',
        'click_count',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'position'    => AdvertisementPosition::class,
            'is_active'   => 'boolean',
            'start_date'  => 'date',
            'end_date'    => 'date',
            'sort_order'  => 'integer',
            'click_count' => 'integer',
            'view_count'  => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopePosition($query, AdvertisementPosition $position)
    {
        return $query->where('position', $position);
    }

    public function scopeCurrentlyValid($query)
    {
        $today = now()->toDateString();

        return $query->where(function ($q) use ($today) {
            $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
        });
    }
}
