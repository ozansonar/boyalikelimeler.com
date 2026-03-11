<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\PollService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PollController extends Controller
{
    public function __construct(
        private readonly PollService $pollService,
    ) {}

    public function vote(Request $request): JsonResponse
    {
        $request->validate([
            'poll_id'   => ['required', 'integer', 'exists:polls,id'],
            'option_id' => ['required', 'integer', 'exists:poll_options,id'],
        ]);

        $pollId = (int) $request->input('poll_id');
        $optionId = (int) $request->input('option_id');
        $ipAddress = $request->ip();

        if ($this->pollService->hasVoted($pollId, $ipAddress)) {
            $results = $this->pollService->getResults($pollId);
            $totalVotes = $this->pollService->getTotalVotes($pollId);

            return response()->json([
                'success'     => false,
                'message'     => 'Bu ankette zaten oy kullandınız.',
                'already_voted' => true,
                'results'     => $results,
                'total_votes' => $totalVotes,
            ], 200);
        }

        $activePoll = $this->pollService->getActivePoll();
        if (! $activePoll || $activePoll->id !== $pollId) {
            return response()->json([
                'success' => false,
                'message' => 'Bu anket artık aktif değil.',
            ], 422);
        }

        $this->pollService->vote($pollId, $optionId, $ipAddress, $request->userAgent());

        $results = $this->pollService->getResults($pollId);
        $totalVotes = $this->pollService->getTotalVotes($pollId);

        return response()->json([
            'success'     => true,
            'message'     => 'Oyunuz başarıyla kaydedildi.',
            'results'     => $results,
            'total_votes' => $totalVotes,
        ]);
    }

    public function results(Request $request, int $pollId): JsonResponse
    {
        $results = $this->pollService->getResults($pollId);
        $totalVotes = $this->pollService->getTotalVotes($pollId);
        $hasVoted = $this->pollService->hasVoted($pollId, $request->ip());

        return response()->json([
            'results'     => $results,
            'total_votes' => $totalVotes,
            'has_voted'   => $hasVoted,
        ]);
    }
}
