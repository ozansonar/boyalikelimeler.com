<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PwaInstallPlatform;
use App\Models\PwaInstall;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class PwaInstallService
{
    private const CACHE_KEY_STATS = 'pwa_installs.stats';
    private const CACHE_KEY_MONTHLY = 'pwa_installs.monthly';
    private const CACHE_KEY_PLATFORMS = 'pwa_installs.platforms';
    private const CACHE_TTL = 300;

    public function record(array $data): PwaInstall
    {
        return DB::transaction(function () use ($data): PwaInstall {
            $install = PwaInstall::create([
                'platform'   => $data['platform'] ?? PwaInstallPlatform::Unknown->value,
                'user_agent' => isset($data['user_agent']) ? mb_substr((string) $data['user_agent'], 0, 500) : null,
                'referrer'   => isset($data['referrer']) ? mb_substr((string) $data['referrer'], 0, 500) : null,
                'ip_hash'    => $data['ip_hash'] ?? null,
            ]);

            $this->clearCache();

            return $install;
        });
    }

    /**
     * @return array{total: int, last_7_days: int, last_30_days: int, today: int}
     */
    public function getStats(): array
    {
        return Cache::remember(self::CACHE_KEY_STATS, self::CACHE_TTL, function (): array {
            $row = PwaInstall::selectRaw(
                'COUNT(*) as total, '
                . 'SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as last_7_days, '
                . 'SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as last_30_days, '
                . 'SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as today',
                [
                    Carbon::now()->subDays(7),
                    Carbon::now()->subDays(30),
                    Carbon::now()->startOfDay(),
                ]
            )->first();

            return [
                'total'        => (int) ($row->total ?? 0),
                'last_7_days'  => (int) ($row->last_7_days ?? 0),
                'last_30_days' => (int) ($row->last_30_days ?? 0),
                'today'        => (int) ($row->today ?? 0),
            ];
        });
    }

    /**
     * @return array<string, int>
     */
    public function getPlatformDistribution(): array
    {
        return Cache::remember(self::CACHE_KEY_PLATFORMS, self::CACHE_TTL, function (): array {
            $rows = PwaInstall::select('platform', DB::raw('COUNT(*) as total'))
                ->groupBy('platform')
                ->pluck('total', 'platform')
                ->toArray();

            // Ensure all enum values are present with 0 default
            $result = [];
            foreach (PwaInstallPlatform::cases() as $platform) {
                $result[$platform->value] = (int) ($rows[$platform->value] ?? 0);
            }

            return $result;
        });
    }

    /**
     * @return array{labels: array<int, string>, values: array<int, int>}
     */
    public function getMonthlyTrend(int $months = 12): array
    {
        $cacheKey = self::CACHE_KEY_MONTHLY . '.' . $months;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($months): array {
            $data = PwaInstall::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total'),
            )
                ->where('created_at', '>=', Carbon::now()->subMonths($months - 1)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $labels = [];
            $values = [];
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $key = $date->format('Y-m');
                $labels[] = $date->translatedFormat('M Y');
                $values[] = (int) ($data[$key] ?? 0);
            }

            return ['labels' => $labels, 'values' => $values];
        });
    }

    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_STATS);
        Cache::forget(self::CACHE_KEY_PLATFORMS);
        // Clear all monthly trend cache variants
        Cache::forget(self::CACHE_KEY_MONTHLY . '.12');
        Cache::forget(self::CACHE_KEY_MONTHLY . '.6');
    }
}
