<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class SettingService
{
    private const CACHE_KEY = 'app.settings';
    private const CACHE_TTL = 3600; // 1 hour

    /** @var array<string, mixed> In-memory store to avoid duplicate DB cache hits per request */
    private static array $memo = [];

    /**
     * Get a single setting value.
     */
    public function get(string $key, ?string $default = null): ?string
    {
        $all = $this->all();

        return $all[$key] ?? $default;
    }

    /**
     * Get all settings as a flat key => value array.
     */
    public function all(): array
    {
        return $this->memoize(self::CACHE_KEY, fn (): array =>
            Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn (): array =>
                Setting::pluck('value', 'key')->toArray()
            )
        );
    }

    /**
     * Get all settings grouped by group name (single query, cached).
     *
     * @return array<string, array<string, string|null>>
     */
    public function getAllGrouped(): array
    {
        $cacheKey = self::CACHE_KEY . '.grouped';

        return $this->memoize($cacheKey, fn (): array =>
            Cache::remember($cacheKey, self::CACHE_TTL, fn (): array =>
                Setting::all(['group', 'key', 'value'])
                    ->groupBy('group')
                    ->map(fn ($items) => $items->pluck('value', 'key')->toArray())
                    ->toArray()
            )
        );
    }

    /**
     * Get all settings for a specific group (cached).
     *
     * @return array<string, string|null>
     */
    public function getGroup(string $group): array
    {
        $cacheKey = self::CACHE_KEY . ".group.{$group}";

        return $this->memoize($cacheKey, fn (): array =>
            Cache::remember(
                $cacheKey,
                self::CACHE_TTL,
                fn (): array => Setting::where('group', $group)
                    ->pluck('value', 'key')
                    ->toArray()
            )
        );
    }

    /**
     * Update a group of settings.
     *
     * @param array<string, string|null> $data
     */
    public function updateGroup(string $group, array $data): void
    {
        DB::transaction(function () use ($group, $data): void {
            foreach ($data as $key => $value) {
                Setting::updateOrCreate(
                    ['group' => $group, 'key' => $key],
                    ['value' => $value]
                );
            }
        });

        $this->clearCache();
    }

    /**
     * Set a single setting value.
     */
    public function set(string $group, string $key, ?string $value): void
    {
        Setting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );

        $this->clearCache();
    }

    /**
     * @return array<int, array{title: string, year: string, director: string, link: string}>
     */
    public function getWeeklyMovies(): array
    {
        return Cache::remember(self::CACHE_KEY . '.weekly_movies', self::CACHE_TTL, function (): array {
            $homepage = $this->getGroup('homepage');
            $json = $homepage['weekly_movies'] ?? '[]';
            $movies = json_decode($json, true) ?: [];
            $count = (int) ($homepage['weekly_movies_count'] ?? 5);

            return array_slice($movies, 0, $count);
        });
    }

    /**
     * Clear the settings cache.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '.grouped');
        Cache::forget(self::CACHE_KEY . '.weekly_movies');

        $groups = Setting::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget(self::CACHE_KEY . ".group.{$group}");
        }

        // YouTube cache - channel ID may have changed
        $channelId = Setting::where('group', 'homepage')
            ->where('key', 'youtube_channel_id')
            ->value('value');
        if (!empty($channelId)) {
            Cache::forget('youtube.channel_videos.' . $channelId);
        }

        self::$memo = [];
    }

    /**
     * Return from in-memory store if available, otherwise execute callback and store.
     */
    private function memoize(string $key, callable $callback): mixed
    {
        if (array_key_exists($key, self::$memo)) {
            return self::$memo[$key];
        }

        return self::$memo[$key] = $callback();
    }
}
