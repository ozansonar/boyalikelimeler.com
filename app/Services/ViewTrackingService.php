<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DailyView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ViewTrackingService
{
    /**
     * Record a unique view for the given model.
     *
     * - Logged-in user  → user_id based (1 view per day per content)
     * - Guest           → session + IP hash based (1 view per day per content)
     * - Also updates daily_views aggregate table
     *
     * Returns true if the view was counted, false if duplicate.
     */
    public function recordView(Model $viewable, bool $withDailyView = true): bool
    {
        $viewerKey = $this->resolveViewerKey();
        $cacheKey  = $this->buildCacheKey($viewable, $viewerKey);

        if (Cache::has($cacheKey)) {
            return false;
        }

        $viewable->increment('view_count');

        if ($withDailyView) {
            $today = now()->toDateString();

            DB::table('daily_views')
                ->updateOrInsert(
                    [
                        'viewable_type' => $viewable->getMorphClass(),
                        'viewable_id'   => $viewable->getKey(),
                        'view_date'     => $today,
                    ],
                    [
                        'view_count' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );

            DB::table('daily_views')
                ->where('viewable_type', $viewable->getMorphClass())
                ->where('viewable_id', $viewable->getKey())
                ->where('view_date', $today)
                ->increment('view_count', 1, ['updated_at' => now()]);
        }

        // Cache until end of day (max 24h)
        $secondsUntilMidnight = now()->endOfDay()->diffInSeconds(now());
        Cache::put($cacheKey, true, (int) $secondsUntilMidnight);

        return true;
    }

    private function resolveViewerKey(): string
    {
        $userId = auth()->id();

        if ($userId) {
            return "u{$userId}";
        }

        $sessionId = session()->getId();
        $ip        = request()->ip() ?? '0.0.0.0';

        return 'g' . md5($sessionId . $ip);
    }

    private function buildCacheKey(Model $viewable, string $viewerKey): string
    {
        $type = class_basename($viewable);
        $id   = $viewable->getKey();
        $date = now()->toDateString();

        return "view_track:{$type}:{$id}:{$viewerKey}:{$date}";
    }
}
