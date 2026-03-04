<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleSlug;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class AuthorService
{
    /**
     * @return array{author_count: int, golden_pen_count: int, total_works: int, total_views: int}
     */
    public function getStats(): array
    {
        return Cache::remember('front.authors.stats', 300, function (): array {
            $today = now()->toDateString();

            $row = User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.slug', RoleSlug::Yazar->value)
                ->whereNotNull('users.email_verified_at')
                ->whereNull('users.deleted_at')
                ->selectRaw('COUNT(*) as author_count')
                ->selectRaw("(SELECT COUNT(DISTINCT gpp.user_id) FROM golden_pen_periods gpp WHERE gpp.deleted_at IS NULL AND gpp.starts_at <= ? AND gpp.ends_at >= ?) as golden_pen_count", [$today, $today])
                ->selectRaw("(SELECT COUNT(*) FROM literary_works lw WHERE lw.deleted_at IS NULL AND lw.status = 'approved') as total_works")
                ->selectRaw("(SELECT COALESCE(SUM(lw2.view_count), 0) FROM literary_works lw2 WHERE lw2.deleted_at IS NULL AND lw2.status = 'approved') as total_views")
                ->first();

            return [
                'author_count'    => (int) $row->author_count,
                'golden_pen_count' => (int) $row->golden_pen_count,
                'total_works'     => (int) $row->total_works,
                'total_views'     => (int) $row->total_views,
            ];
        });
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $today = now()->toDateString();

        $query = User::query()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.slug', RoleSlug::Yazar->value)
            ->whereNotNull('users.email_verified_at')
            ->with(['activeGoldenPenPeriod'])
            ->withCount(['literaryWorks as approved_works_count' => function ($q): void {
                $q->where('status', 'approved');
            }])
            ->withSum(['literaryWorks as total_views' => function ($q): void {
                $q->where('status', 'approved');
            }], 'view_count')
            ->select('users.*');

        // Search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.username', 'like', "%{$search}%");
            });
        }

        // Golden pen filter
        if (! empty($filters['golden_pen'])) {
            $query->whereExists(function ($q) use ($today): void {
                $q->select(DB::raw(1))
                  ->from('golden_pen_periods')
                  ->whereColumn('golden_pen_periods.user_id', 'users.id')
                  ->whereNull('golden_pen_periods.deleted_at')
                  ->where('golden_pen_periods.starts_at', '<=', $today)
                  ->where('golden_pen_periods.ends_at', '>=', $today);
            });
        }

        // Sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir = $filters['dir'] ?? 'desc';

        match ($sortField) {
            'name'   => $query->orderBy('users.name', $sortDir),
            'works'  => $query->orderBy('approved_works_count', $sortDir),
            'views'  => $query->orderBy('total_views', $sortDir),
            default  => $query->orderBy('users.created_at', $sortDir),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
