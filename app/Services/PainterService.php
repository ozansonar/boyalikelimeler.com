<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class PainterService
{
    /**
     * @return array{painter_count: int, total_works: int, total_views: int}
     */
    public function getStats(): array
    {
        return Cache::remember('front.painters.stats', 300, function (): array {
            $row = User::query()
                ->whereNotNull('users.email_verified_at')
                ->whereNull('users.deleted_at')
                ->whereExists(function ($q): void {
                    $q->select(DB::raw(1))
                      ->from('literary_works')
                      ->whereColumn('literary_works.user_id', 'users.id')
                      ->whereNull('literary_works.deleted_at')
                      ->where('literary_works.status', 'approved')
                      ->where('literary_works.work_type', LiteraryWorkType::Visual->value);
                })
                ->selectRaw('COUNT(*) as painter_count')
                ->selectRaw("(SELECT COUNT(*) FROM literary_works lw WHERE lw.deleted_at IS NULL AND lw.status = 'approved' AND lw.work_type = ?) as total_works", [LiteraryWorkType::Visual->value])
                ->selectRaw("(SELECT COALESCE(SUM(lw2.view_count), 0) FROM literary_works lw2 WHERE lw2.deleted_at IS NULL AND lw2.status = 'approved' AND lw2.work_type = ?) as total_views", [LiteraryWorkType::Visual->value])
                ->first();

            return [
                'painter_count' => (int) $row->painter_count,
                'total_works'   => (int) $row->total_works,
                'total_views'   => (int) $row->total_views,
            ];
        });
    }

    public function getFeaturedPainter(?string $userId): ?User
    {
        if (empty($userId)) {
            return null;
        }

        return Cache::remember("front.painters.featured.{$userId}", 300, function () use ($userId): ?User {
            return User::query()
                ->where('id', (int) $userId)
                ->whereNotNull('email_verified_at')
                ->with(['activeGoldenPenPeriod'])
                ->withCount(['literaryWorks as approved_visual_works_count' => function ($q): void {
                    $q->where('status', 'approved')
                      ->where('work_type', LiteraryWorkType::Visual->value);
                }])
                ->withSum(['literaryWorks as total_visual_views' => function ($q): void {
                    $q->where('status', 'approved')
                      ->where('work_type', LiteraryWorkType::Visual->value);
                }], 'view_count')
                ->first();
        });
    }

    /**
     * @param  string|null  $idsJson  JSON-encoded array of user IDs
     * @return Collection<int, User>
     */
    public function getFeaturedPainters(?string $idsJson): Collection
    {
        if (empty($idsJson)) {
            return new Collection();
        }

        /** @var array<int> $ids */
        $ids = json_decode($idsJson, true);

        if (! is_array($ids) || $ids === []) {
            return new Collection();
        }

        $ids = array_map('intval', array_filter($ids));

        if ($ids === []) {
            return new Collection();
        }

        $cacheKey = 'front.painters.featured_multi.' . md5(implode(',', $ids));

        return Cache::remember($cacheKey, 300, function () use ($ids): Collection {
            return User::query()
                ->whereIn('id', $ids)
                ->whereNotNull('email_verified_at')
                ->with(['activeGoldenPenPeriod'])
                ->withCount(['literaryWorks as approved_visual_works_count' => function ($q): void {
                    $q->where('status', 'approved')
                      ->where('work_type', LiteraryWorkType::Visual->value);
                }])
                ->withSum(['literaryWorks as total_visual_views' => function ($q): void {
                    $q->where('status', 'approved')
                      ->where('work_type', LiteraryWorkType::Visual->value);
                }], 'view_count')
                ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
                ->get();
        });
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = User::query()
            ->select('users.*')
            ->whereNotNull('users.email_verified_at')
            ->whereExists(function ($q): void {
                $q->select(DB::raw(1))
                  ->from('literary_works')
                  ->whereColumn('literary_works.user_id', 'users.id')
                  ->whereNull('literary_works.deleted_at')
                  ->where('literary_works.status', 'approved')
                  ->where('literary_works.work_type', LiteraryWorkType::Visual->value);
            })
            ->with(['activeGoldenPenPeriod'])
            ->withCount(['literaryWorks as approved_visual_works_count' => function ($q): void {
                $q->where('status', 'approved')
                  ->where('work_type', LiteraryWorkType::Visual->value);
            }])
            ->withSum(['literaryWorks as total_visual_views' => function ($q): void {
                $q->where('status', 'approved')
                  ->where('work_type', LiteraryWorkType::Visual->value);
            }], 'view_count');

        // Search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir = $filters['dir'] ?? 'desc';

        match ($sortField) {
            'name'   => $query->orderBy('users.name', $sortDir),
            'works'  => $query->orderBy('approved_visual_works_count', $sortDir),
            'views'  => $query->orderBy('total_visual_views', $sortDir),
            default  => $query->orderBy('users.created_at', $sortDir),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
