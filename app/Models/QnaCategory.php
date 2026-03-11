<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QnaStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QnaCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color_class',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QnaQuestion::class);
    }

    public function approvedQuestions(): HasMany
    {
        return $this->hasMany(QnaQuestion::class)->where('status', QnaStatus::Approved);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function approvedQuestionCount(): int
    {
        return $this->approvedQuestions()->count();
    }

    public function approvedAnswerCount(): int
    {
        return QnaAnswer::whereHas('question', function (Builder $query): void {
            $query->where('qna_category_id', $this->id)->where('status', QnaStatus::Approved);
        })->where('status', QnaStatus::Approved)->count();
    }
}
