<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class PollService
{
    private const CACHE_TTL = 300;

    /**
     * Get all polls with options count and votes count (admin list).
     */
    public function getAll(): Collection
    {
        return Poll::withCount(['options', 'votes'])
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Find a poll by ID with options.
     */
    public function find(int $id): Poll
    {
        return Poll::with(['options' => fn ($q) => $q->orderBy('display_order')])
            ->findOrFail($id);
    }

    /**
     * Create a new poll with options.
     *
     * @param array<string, mixed> $data
     * @param array<int, string> $options
     */
    public function store(array $data, array $options): Poll
    {
        return DB::transaction(function () use ($data, $options): Poll {
            $poll = Poll::create($data);

            foreach ($options as $index => $optionText) {
                $optionText = trim($optionText);
                if ($optionText !== '') {
                    $poll->options()->create([
                        'option_text'   => $optionText,
                        'display_order' => $index,
                    ]);
                }
            }

            if ($poll->is_active) {
                $this->deactivateOtherPolls($poll->id);
            }

            $this->clearCache();

            return $poll;
        });
    }

    /**
     * Update a poll with options.
     *
     * @param array<string, mixed> $data
     * @param array<int, string> $options
     */
    public function update(Poll $poll, array $data, array $options): Poll
    {
        return DB::transaction(function () use ($poll, $data, $options): Poll {
            $poll->update($data);

            $poll->options()->forceDelete();

            foreach ($options as $index => $optionText) {
                $optionText = trim($optionText);
                if ($optionText !== '') {
                    $poll->options()->create([
                        'option_text'   => $optionText,
                        'display_order' => $index,
                    ]);
                }
            }

            if ($poll->is_active) {
                $this->deactivateOtherPolls($poll->id);
            }

            $this->clearCache();

            return $poll;
        });
    }

    /**
     * Delete a poll.
     */
    public function destroy(Poll $poll): void
    {
        $poll->delete();
        $this->clearCache();
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(Poll $poll): void
    {
        DB::transaction(function () use ($poll): void {
            $newStatus = ! $poll->is_active;
            $poll->update(['is_active' => $newStatus]);

            if ($newStatus) {
                $this->deactivateOtherPolls($poll->id);
            }

            $this->clearCache();
        });
    }

    /**
     * Get the currently active poll for frontend display.
     */
    public function getActivePoll(): ?Poll
    {
        return Cache::remember('poll.active', self::CACHE_TTL, function (): ?Poll {
            $poll = Poll::where('is_active', true)
                ->with(['options' => fn ($q) => $q->orderBy('display_order')])
                ->first();

            if ($poll && ! $poll->isCurrentlyActive()) {
                return null;
            }

            return $poll;
        });
    }

    /**
     * Check if an IP has already voted on a poll.
     */
    public function hasVoted(int $pollId, string $ipAddress): bool
    {
        return PollVote::where('poll_id', $pollId)
            ->where('ip_address', $ipAddress)
            ->exists();
    }

    /**
     * Get the vote of an IP for a specific poll.
     */
    public function getVoteByIp(int $pollId, string $ipAddress): ?PollVote
    {
        return PollVote::where('poll_id', $pollId)
            ->where('ip_address', $ipAddress)
            ->first();
    }

    /**
     * Cast a vote.
     */
    public function vote(int $pollId, int $optionId, string $ipAddress, ?string $userAgent = null): PollVote
    {
        return DB::transaction(function () use ($pollId, $optionId, $ipAddress, $userAgent): PollVote {
            $existingVote = PollVote::where('poll_id', $pollId)
                ->where('ip_address', $ipAddress)
                ->first();

            if ($existingVote) {
                throw new \RuntimeException('Bu ankette zaten oy kullandınız.');
            }

            $option = PollOption::where('id', $optionId)
                ->where('poll_id', $pollId)
                ->firstOrFail();

            $vote = PollVote::create([
                'poll_id'        => $pollId,
                'poll_option_id' => $option->id,
                'ip_address'     => $ipAddress,
                'user_agent'     => $userAgent ? mb_substr($userAgent, 0, 500) : null,
            ]);

            $this->clearResultsCache($pollId);

            return $vote;
        });
    }

    /**
     * Get poll results with percentages.
     *
     * @return array<int, array{option_id: int, option_text: string, vote_count: int, percentage: float}>
     */
    public function getResults(int $pollId): array
    {
        $cacheKey = "poll.results.{$pollId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($pollId): array {
            $poll = Poll::with(['options' => fn ($q) => $q->orderBy('display_order')])
                ->findOrFail($pollId);

            $totalVotes = PollVote::where('poll_id', $pollId)->count();

            $voteCounts = PollVote::where('poll_id', $pollId)
                ->select('poll_option_id', DB::raw('COUNT(*) as vote_count'))
                ->groupBy('poll_option_id')
                ->pluck('vote_count', 'poll_option_id')
                ->toArray();

            $results = [];
            foreach ($poll->options as $option) {
                $count = $voteCounts[$option->id] ?? 0;
                $results[] = [
                    'option_id'   => $option->id,
                    'option_text' => $option->option_text,
                    'vote_count'  => $count,
                    'percentage'  => $totalVotes > 0 ? round(($count / $totalVotes) * 100, 1) : 0,
                ];
            }

            return $results;
        });
    }

    /**
     * Get total votes for a poll.
     */
    public function getTotalVotes(int $pollId): int
    {
        return PollVote::where('poll_id', $pollId)->count();
    }

    /**
     * Get admin stats for polls.
     *
     * @return array{total: int, active: int, inactive: int, total_votes: int}
     */
    public function getAdminStats(): array
    {
        return [
            'total'       => Poll::count(),
            'active'      => Poll::where('is_active', true)->count(),
            'inactive'    => Poll::where('is_active', false)->count(),
            'total_votes' => PollVote::count(),
        ];
    }

    /**
     * Deactivate all polls except the given one.
     */
    private function deactivateOtherPolls(int $exceptId): void
    {
        Poll::where('id', '!=', $exceptId)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    /**
     * Clear all poll-related caches.
     */
    private function clearCache(): void
    {
        Cache::forget('poll.active');
    }

    /**
     * Clear results cache for a specific poll.
     */
    private function clearResultsCache(int $pollId): void
    {
        Cache::forget("poll.results.{$pollId}");
        Cache::forget('poll.active');
    }
}
