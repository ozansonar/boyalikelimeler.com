<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Enums\LiteraryWorkType;
use App\Enums\PostStatus;
use App\Models\Favorite;
use App\Models\LiteraryWork;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class ProfileService
{
    public function __construct(
        private readonly UploadService $uploadService,
    ) {}
    /**
     * @return array{posts: LengthAwarePaginator, works: LengthAwarePaginator, stats: array, favoriteWorks: Collection, favoritePosts: Collection}
     */
    public function getProfileData(User $user, int $perPage = 5): array
    {
        $works = $user->literaryWorks()
            ->with('category')
            ->where('status', LiteraryWorkStatus::Approved)
            ->orderByDesc('published_at')
            ->paginate($perPage, ['*'], 'eser_sayfa')
            ->withQueryString()
            ->fragment('eserler');

        $posts = $user->posts()
            ->with('category')
            ->where('status', PostStatus::Published)
            ->orderByDesc('published_at')
            ->paginate($perPage, ['*'], 'yazi_sayfa')
            ->withQueryString()
            ->fragment('yazilar');

        $stats = $this->getWriterStats($user);

        $favoriteWorks = $this->getUserFavoriteWorks($user);
        $favoritePosts = $this->getUserFavoritePosts($user);

        return compact('posts', 'works', 'stats', 'favoriteWorks', 'favoritePosts');
    }

    /**
     * @return array{total_posts: int, published_posts: int, total_views: int, total_works: int, approved_works: int, total_work_views: int, work_type_counts: array<string, int>}
     */
    public function getWriterStats(User $user): array
    {
        $postStats = $user->posts()->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as published,
            COALESCE(SUM(view_count), 0) as views
        ", [PostStatus::Published->value])->first();

        $workStats = $user->literaryWorks()->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved,
            COALESCE(SUM(view_count), 0) as views
        ", [LiteraryWorkStatus::Approved->value])->first();

        $workTypeCounts = $user->literaryWorks()
            ->where('status', LiteraryWorkStatus::Approved)
            ->selectRaw('work_type, COUNT(*) as cnt')
            ->groupBy('work_type')
            ->pluck('cnt', 'work_type')
            ->toArray();

        return [
            'total_posts'       => (int) $postStats->total,
            'published_posts'   => (int) $postStats->published,
            'total_views'       => (int) $postStats->views,
            'total_works'       => (int) $workStats->total,
            'approved_works'    => (int) $workStats->approved,
            'total_work_views'  => (int) $workStats->views,
            'work_type_counts'  => $workTypeCounts,
        ];
    }

    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $user->update($data);
            return $user->fresh();
        });
    }

    public function updatePassword(User $user, string $oldPassword, string $newPassword): bool
    {
        if (! Hash::check($oldPassword, $user->password)) {
            return false;
        }

        $user->update(['password' => $newPassword]);

        return true;
    }

    public function uploadAvatar(User $user, UploadedFile $file): string
    {
        $slug = \Illuminate\Support\Str::slug($user->name);
        $path = $this->uploadService->replaceImage(
            $file,
            'avatars',
            $user->avatar,
            $slug,
            ['width' => 400, 'height' => 400, 'crop' => true],
        );
        $user->update(['avatar' => $path]);

        return $path;
    }

    public function removeAvatar(User $user): void
    {
        if ($user->avatar) {
            $this->uploadService->deleteImage($user->avatar);
            $user->update(['avatar' => null]);
        }
    }

    public function uploadCover(User $user, UploadedFile $file): string
    {
        $slug = \Illuminate\Support\Str::slug($user->name);
        $path = $this->uploadService->replaceImage(
            $file,
            'covers',
            $user->cover_image,
            $slug,
            ['width' => 1920, 'height' => 400, 'crop' => true],
        );
        $user->update(['cover_image' => $path]);

        return $path;
    }

    public function getUserFavoriteWorks(User $user, int $limit = 6): Collection
    {
        $favoriteIds = Favorite::where('user_id', $user->id)
            ->where('favoriteable_type', LiteraryWork::class)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->pluck('favoriteable_id');

        if ($favoriteIds->isEmpty()) {
            return new Collection();
        }

        return LiteraryWork::whereIn('id', $favoriteIds)
            ->whereHas('author', fn ($q) => $q->whereNotNull('username'))
            ->where('status', LiteraryWorkStatus::Approved)
            ->with(['category', 'author'])
            ->get();
    }

    public function getUserFavoritePosts(User $user, int $limit = 6): Collection
    {
        $favoriteIds = Favorite::where('user_id', $user->id)
            ->where('favoriteable_type', Post::class)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->pluck('favoriteable_id');

        if ($favoriteIds->isEmpty()) {
            return new Collection();
        }

        return Post::whereIn('id', $favoriteIds)
            ->where('status', PostStatus::Published)
            ->with(['category', 'author'])
            ->get();
    }
}
