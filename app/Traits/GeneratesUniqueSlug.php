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
        $model = $this->slugModel();

        $exists = $model::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if (! $exists) {
            return $slug;
        }

        $existing = $model::withTrashed()
            ->where('slug', 'LIKE', $slug . '-%')
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->pluck('slug');

        $max = 0;
        $prefix = $slug . '-';
        $prefixLen = strlen($prefix);

        foreach ($existing as $s) {
            $suffix = substr($s, $prefixLen);
            if (ctype_digit($suffix)) {
                $max = max($max, (int) $suffix);
            }
        }

        return $slug . '-' . ($max + 1);
    }
}
