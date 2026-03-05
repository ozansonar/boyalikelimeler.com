<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\RoleSlug;
use Illuminate\Support\Facades\Cache;

trait HasPermission
{
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permissionSlug, $this->getCachedPermissions(), true);
    }

    public function hasAnyPermission(string ...$permissionSlugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $cached = $this->getCachedPermissions();

        foreach ($permissionSlugs as $slug) {
            if (in_array($slug, $cached, true)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(string ...$permissionSlugs): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $cached = $this->getCachedPermissions();

        foreach ($permissionSlugs as $slug) {
            if (!in_array($slug, $cached, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    public function getCachedPermissions(): array
    {
        return Cache::remember(
            "user.{$this->id}.permissions",
            300,
            fn (): array => $this->role?->permissions()->pluck('slug')->toArray() ?? []
        );
    }

    public function clearPermissionCache(): void
    {
        Cache::forget("user.{$this->id}.permissions");
    }
}
