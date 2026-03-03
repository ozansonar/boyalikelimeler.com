<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Enums\PostStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class ProfileService
{
    /**
     * @return array{posts: Collection, works: Collection, stats: array}
     */
    public function getProfileData(User $user, int $postLimit = 6, int $workLimit = 10): array
    {
        $posts = $user->posts()
            ->with('category')
            ->where('status', PostStatus::Published)
            ->orderByDesc('published_at')
            ->limit($postLimit)
            ->get();

        $works = $user->literaryWorks()
            ->with('category')
            ->where('status', LiteraryWorkStatus::Approved)
            ->orderByDesc('published_at')
            ->limit($workLimit)
            ->get();

        $stats = $this->getWriterStats($user);

        return compact('posts', 'works', 'stats');
    }

    /**
     * @return array{total_posts: int, published_posts: int, total_views: int, total_works: int, approved_works: int, total_work_views: int}
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

        return [
            'total_posts'      => (int) $postStats->total,
            'published_posts'  => (int) $postStats->published,
            'total_views'      => (int) $postStats->views,
            'total_works'      => (int) $workStats->total,
            'approved_works'   => (int) $workStats->approved,
            'total_work_views' => (int) $workStats->views,
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
        $this->deleteOldFile($user->avatar);

        $path = $this->storeFile($file, 'avatars');
        $user->update(['avatar' => $path]);

        return $path;
    }

    public function uploadCover(User $user, UploadedFile $file): string
    {
        $this->deleteOldFile($user->cover_image);

        $path = $this->storeFile($file, 'covers');
        $user->update(['cover_image' => $path]);

        return $path;
    }

    private function storeFile(UploadedFile $file, string $folder): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('uploads/' . $folder);

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $file->move($directory, $filename);

        return $folder . '/' . $filename;
    }

    private function deleteOldFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $fullPath = public_path('uploads/' . $path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
