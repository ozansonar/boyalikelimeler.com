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
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get all settings for a specific group.
     *
     * @return array<string, string|null>
     */
    public function getGroup(string $group): array
    {
        return Setting::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
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
     * Clear the settings cache.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
