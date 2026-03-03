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
        return [
            'total_posts'      => $user->posts()->count(),
            'published_posts'  => $user->posts()->where('status', PostStatus::Published)->count(),
            'total_views'      => (int) $user->posts()->sum('view_count'),
            'total_works'      => $user->literaryWorks()->count(),
            'approved_works'   => $user->literaryWorks()->where('status', LiteraryWorkStatus::Approved)->count(),
            'total_work_views' => (int) $user->literaryWorks()->sum('view_count'),
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
