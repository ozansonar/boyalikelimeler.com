<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\QnaAnswer;
use App\Models\QnaLike;
use App\Models\QnaQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

final class QnaLikeService
{
    /**
     * @return array{liked: bool, count: int}
     */
    public function toggle(User $user, Model $likeable): array
    {
        return DB::transaction(function () use ($user, $likeable): array {
            $existing = QnaLike::where('user_id', $user->id)
                ->where('likeable_id', $likeable->id)
                ->where('likeable_type', $likeable->getMorphClass())
                ->first();

            if ($existing) {
                $existing->delete();
                $this->updateLikeCount($likeable);

                return [
                    'liked' => false,
                    'count' => $likeable->fresh()->like_count,
                ];
            }

            QnaLike::create([
                'user_id'       => $user->id,
                'likeable_id'   => $likeable->id,
                'likeable_type' => $likeable->getMorphClass(),
            ]);

            $this->updateLikeCount($likeable);

            return [
                'liked' => true,
                'count' => $likeable->fresh()->like_count,
            ];
        });
    }

    public function hasLiked(User $user, Model $likeable): bool
    {
        return QnaLike::where('user_id', $user->id)
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', $likeable->getMorphClass())
            ->exists();
    }

    private function updateLikeCount(Model $likeable): void
    {
        $count = QnaLike::where('likeable_id', $likeable->id)
            ->where('likeable_type', $likeable->getMorphClass())
            ->count();

        $likeable->updateQuietly(['like_count' => $count]);
    }
}
