<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoldenPenPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
        });
    }
}
