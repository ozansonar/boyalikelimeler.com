<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Favorite;
use App\Models\LiteraryWork;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

final class FavoriteService
{
    private const array TYPE_MAP = [
        'post'          => Post::class,
        'literary_work' => LiteraryWork::class,
    ];

    public function toggle(User $user, string $type, int $id): array
    {
        $model = $this->resolveModel($type, $id);

        if (! $model) {
            return ['success' => false, 'message' => 'İçerik bulunamadı.'];
        }

        $existing = Favorite::where('user_id', $user->id)
            ->where('favoriteable_type', $model::class)
            ->where('favoriteable_id', $model->id)
            ->first();

        if ($existing) {
            $existing->forceDelete();

            return [
                'success'    => true,
                'is_favorited' => false,
                'count'      => $model->favorites()->count(),
                'message'    => 'Favorilerden kaldırıldı.',
            ];
        }

        Favorite::create([
            'user_id'           => $user->id,
            'favoriteable_type' => $model::class,
            'favoriteable_id'   => $model->id,
        ]);

        return [
            'success'    => true,
            'is_favorited' => true,
            'count'      => $model->favorites()->count(),
            'message'    => 'Favorilere eklendi.',
        ];
    }

    public function isFavorited(User $user, Model $model): bool
    {
        return Favorite::where('user_id', $user->id)
            ->where('favoriteable_type', $model::class)
            ->where('favoriteable_id', $model->id)
            ->exists();
    }

    public function getCount(Model $model): int
    {
        return $model->favorites()->count();
    }

    public function getUserFavorites(User $user, int $perPage = 12): LengthAwarePaginator
    {
        return Favorite::where('user_id', $user->id)
            ->with('favoriteable')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getUserFavoritesByType(User $user, string $type, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $modelClass = self::TYPE_MAP[$type] ?? null;

        if (! $modelClass) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return Favorite::where('user_id', $user->id)
            ->where('favoriteable_type', $modelClass)
            ->with('favoriteable')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->pluck('favoriteable')
            ->filter();
    }

    private function resolveModel(string $type, int $id): ?Model
    {
        $modelClass = self::TYPE_MAP[$type] ?? null;

        if (! $modelClass) {
            return null;
        }

        return $modelClass::find($id);
    }
}
