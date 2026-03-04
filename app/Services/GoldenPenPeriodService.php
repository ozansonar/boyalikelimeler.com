<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoldenPenPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class GoldenPenPeriodService
{
    /**
     * @return Collection<int, GoldenPenPeriod>
     */
    public function getPeriodsForUser(User $user): Collection
    {
        return $user->goldenPenPeriods()
            ->orderByDesc('ends_at')
            ->get();
    }

    public function addPeriod(User $user, string $startsAt, string $endsAt, ?string $note = null): GoldenPenPeriod
    {
        return DB::transaction(fn (): GoldenPenPeriod => $user->goldenPenPeriods()->create([
            'starts_at' => $startsAt,
            'ends_at'   => $endsAt,
            'note'      => $note,
        ]));
    }

    public function removePeriod(GoldenPenPeriod $period): void
    {
        DB::transaction(fn () => $period->delete());
    }

    /**
     * Sync periods from admin form data.
     * Replaces all existing periods with the given list.
     *
     * @param array<int, array{starts_at: string, ends_at: string, note?: string|null}> $periods
     */
    public function syncPeriods(User $user, array $periods): void
    {
        DB::transaction(function () use ($user, $periods): void {
            // Collect old period months before deletion (for cache invalidation)
            $oldMonths = $this->getAffectedMonths($user->goldenPenPeriods()->pluck('starts_at', 'ends_at'));

            // Soft-delete all existing periods
            $user->goldenPenPeriods()->delete();

            // Re-create from given data
            foreach ($periods as $period) {
                if (empty($period['starts_at']) || empty($period['ends_at'])) {
                    continue;
                }

                $user->goldenPenPeriods()->create([
                    'starts_at' => $period['starts_at'],
                    'ends_at'   => $period['ends_at'],
                    'note'      => $period['note'] ?? null,
                ]);
            }

            // Collect new period months for cache invalidation
            $newMonths = $this->getAffectedMonths($user->goldenPenPeriods()->pluck('starts_at', 'ends_at'));

            $this->clearFrontendCache($user, $oldMonths->merge($newMonths)->unique()->all());
        });
    }

    /**
     * Get all year-month keys affected by given date ranges.
     *
     * @param \Illuminate\Support\Collection<string, string> $dateRanges (ends_at => starts_at)
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function getAffectedMonths(\Illuminate\Support\Collection $dateRanges): \Illuminate\Support\Collection
    {
        $months = collect();

        foreach ($dateRanges as $endsAt => $startsAt) {
            $start = \Illuminate\Support\Carbon::parse($startsAt)->startOfMonth();
            $end = \Illuminate\Support\Carbon::parse($endsAt)->startOfMonth();

            while ($start->lte($end)) {
                $months->push($start->format('Y-m'));
                $start->addMonth();
            }
        }

        return $months->unique()->values();
    }

    private function clearFrontendCache(User $user, array $affectedMonths): void
    {
        Cache::forget('front.authors.stats');
        Cache::forget('front.authors.golden_pen_months');
        Cache::forget("front.authors.featured.{$user->id}");

        foreach ($affectedMonths as $yearMonth) {
            Cache::forget("front.authors.golden_pen.{$yearMonth}");
        }
    }
}
