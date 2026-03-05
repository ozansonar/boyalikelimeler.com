<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LiteraryWorkStatus;
use App\Enums\PostStatus;
use App\Models\LiteraryWork;
use App\Models\Post;
use App\Models\User;

final class SearchService
{
    /**
     * @return array{works: \Illuminate\Support\Collection, posts: \Illuminate\Support\Collection, authors: \Illuminate\Support\Collection, total: int}
     */
    public function search(string $query, int $limit = 6): array
    {
        $works = $this->searchWorks($query, $limit);
        $posts = $this->searchPosts($query, $limit);
        $authors = $this->searchAuthors($query, $limit);

        return [
            'works'   => $works,
            'posts'   => $posts,
            'authors' => $authors,
            'total'   => $works->count() + $posts->count() + $authors->count(),
        ];
    }

    /**
     * @return \Illuminate\Support\Collection<int, LiteraryWork>
     */
    public function searchWorks(string $query, int $limit = 6): \Illuminate\Support\Collection
    {
        return LiteraryWork::with(['category', 'author'])
            ->whereHas('author', fn ($q) => $q->whereNotNull('username'))
            ->where('status', LiteraryWorkStatus::Approved)
            ->whereNotNull('published_at')
            ->where(function ($q) use ($query): void {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection<int, Post>
     */
    public function searchPosts(string $query, int $limit = 6): \Illuminate\Support\Collection
    {
        return Post::with(['category', 'author'])
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($q) use ($query): void {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection<int, User>
     */
    public function searchAuthors(string $query, int $limit = 6): \Illuminate\Support\Collection
    {
        return User::where('is_public', true)
            ->where(function ($q) use ($query): void {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->orWhere('bio', 'like', "%{$query}%");
            })
            ->withCount(['literaryWorks' => function ($q): void {
                $q->where('status', LiteraryWorkStatus::Approved);
            }])
            ->orderByDesc('literary_works_count')
            ->limit($limit)
            ->get();
    }
}
