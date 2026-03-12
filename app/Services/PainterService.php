<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class PainterService
{
    private const array TURKISH_MONTHS = [
        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
        5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
        9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık',
    ];

    /**
     * @return array{painter_count: int, golden_brush_count: int, total_works: int, total_views: int}
     */
    public function getStats(): array
    {
        return Cache::remember('front.painters.stats', 300, function (): array {
            $today = now()->toDateString();

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
                ->selectRaw("(SELECT COUNT(DISTINCT gbp.user_id) FROM golden_brush_periods gbp WHERE gbp.deleted_at IS NULL AND gbp.starts_at <= ? AND gbp.ends_at >= ?) as golden_brush_count", [$today, $today])
                ->selectRaw("(SELECT COUNT(*) FROM literary_works lw WHERE lw.deleted_at IS NULL AND lw.status = 'approved' AND lw.work_type = ?) as total_works", [LiteraryWorkType::Visual->value])
                ->selectRaw("(SELECT COALESCE(SUM(lw2.view_count), 0) FROM literary_works lw2 WHERE lw2.deleted_at IS NULL AND lw2.status = 'approved' AND lw2.work_type = ?) as total_views", [LiteraryWorkType::Visual->value])
                ->first();

            return [
                'painter_count'      => (int) $row->painter_count,
                'golden_brush_count' => (int) $row->golden_brush_count,
                'total_works'        => (int) $row->total_works,
                'total_views'        => (int) $row->total_views,
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
                ->with(['activeGoldenBrushPeriod'])
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
                ->with(['activeGoldenBrushPeriod'])
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

    /**
     * @return array<int, array{key: string, label: string}>
     */
    public function getGoldenBrushMonths(): array
    {
        return Cache::remember('front.painters.golden_brush_months', 300, function (): array {
            $startMonth = Carbon::create(2026, 1, 1)->startOfDay();
            $currentMonth = now()->startOfMonth();
            $months = [];

            while ($startMonth->lte($currentMonth)) {
                $key = $startMonth->format('Y-m');
                $label = $startMonth->year . ' ' . self::TURKISH_MONTHS[(int) $startMonth->month] . ' Altın Fırçaları';
                $months[] = [
                    'key'   => $key,
                    'label' => $label,
                ];
                $startMonth->addMonth();
            }

            return $months;
        });
    }

    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, array{key: string, label: string}>
     */
    public function getGoldenBrushMonthsPaginated(int $perPage = 12): \Illuminate\Pagination\LengthAwarePaginator
    {
        $allMonths = array_reverse($this->getGoldenBrushMonths());

        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $offset = ($page - 1) * $perPage;
        $items = array_slice($allMonths, $offset, $perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            count($allMonths),
            $perPage,
            $page,
            ['path' => route('painters.golden-brush-index')],
        );
    }

    /**
     * @return array{label: string, painters: Collection<int, User>}|null
     */
    public function getGoldenBrushPaintersByMonth(string $yearMonth): ?array
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            return null;
        }

        return Cache::remember("front.painters.golden_brush.{$yearMonth}", 300, function () use ($yearMonth): ?array {
            [$yearStr, $monthStr] = explode('-', $yearMonth);
            $year = (int) $yearStr;
            $month = (int) $monthStr;

            if ($month < 1 || $month > 12) {
                return null;
            }

            $date = Carbon::create($year, $month, 1)->startOfDay();

            if ($date->gt(now()->startOfMonth()) || $date->lt(Carbon::create(2026, 1, 1)->startOfDay())) {
                return null;
            }

            $firstDay = $date->copy()->startOfMonth()->toDateString();
            $lastDay = $date->copy()->endOfMonth()->toDateString();

            $painters = User::query()
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
                ->whereExists(function ($q) use ($firstDay, $lastDay): void {
                    $q->select(DB::raw(1))
                      ->from('golden_brush_periods')
                      ->whereColumn('golden_brush_periods.user_id', 'users.id')
                      ->whereNull('golden_brush_periods.deleted_at')
                      ->where('golden_brush_periods.starts_at', '<=', $lastDay)
                      ->where('golden_brush_periods.ends_at', '>=', $firstDay);
                })
                ->with(['activeGoldenBrushPeriod'])
                ->withCount(['literaryWorks as approved_visual_works_count' => function ($q): void {
                    $q->where('status', 'approved')
                      ->where('work_type', LiteraryWorkType::Visual->value);
                }])
                ->withSum(['literaryWorks as total_visual_views' => function ($q): void {
                    $q->where('status', 'approved')
                      ->where('work_type', LiteraryWorkType::Visual->value);
                }], 'view_count')
                ->orderBy('users.name')
                ->get();

            $label = $date->year . ' ' . self::TURKISH_MONTHS[(int) $date->month] . ' Altın Fırçaları';

            return [
                'label'    => $label,
                'painters' => $painters,
            ];
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
            ->with(['activeGoldenBrushPeriod'])
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

        // Golden brush filter
        if (! empty($filters['golden_brush'])) {
            $today = now()->toDateString();
            $query->whereExists(function ($q) use ($today): void {
                $q->select(DB::raw(1))
                  ->from('golden_brush_periods')
                  ->whereColumn('golden_brush_periods.user_id', 'users.id')
                  ->whereNull('golden_brush_periods.deleted_at')
                  ->where('golden_brush_periods.starts_at', '<=', $today)
                  ->where('golden_brush_periods.ends_at', '>=', $today);
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
