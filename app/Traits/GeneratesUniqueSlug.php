<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    /**
     * Return the model class that owns the slug column.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    abstract protected function slugModel(): string;

    protected function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;
        $model = $this->slugModel();

        while ($model::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
