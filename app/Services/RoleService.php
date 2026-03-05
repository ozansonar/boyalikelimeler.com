<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class RoleService
{
    use GeneratesUniqueSlug;

    protected function slugModel(): string
    {
        return Role::class;
    }

    public function allRoles(): Collection
    {
        return Role::withCount('users')
            ->with('permissions')
            ->orderBy('name')
            ->get();
    }

    public function getStats(): array
    {
        return Cache::remember('roles.admin_stats', 300, function (): array {
            $roles = Role::withCount('users')->get();
            $totalPermissions = Permission::count();
            $totalUsers = User::count();
            $permissionGroups = Permission::distinct('group')->count('group');

            return [
                'total_roles'       => $roles->count(),
                'total_permissions' => $totalPermissions,
                'total_users'       => $totalUsers,
                'permission_groups' => $permissionGroups,
            ];
        });
    }

    public function getRoleDistribution(): array
    {
        return Cache::remember('roles.distribution', 300, function (): array {
            $roles = Role::withCount('users')->orderByDesc('users_count')->get();
            $totalUsers = $roles->sum('users_count');

            return $roles->map(fn (Role $role): array => [
                'id'         => $role->id,
                'name'       => $role->name,
                'slug'       => $role->slug,
                'count'      => $role->users_count,
                'percentage' => $totalUsers > 0 ? round(($role->users_count / $totalUsers) * 100, 1) : 0,
            ])->toArray();
        });
    }

    public function getPermissionsByGroup(): Collection
    {
        return Permission::orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
    }

    public function findWithRelations(int $id): Role
    {
        return Role::withCount('users')
            ->with('permissions')
            ->findOrFail($id);
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data): Role {
            $data['slug'] = $this->generateUniqueSlug($data['name']);

            $role = Role::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);

            if (! empty($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }

            $this->clearCache();

            return $role->load('permissions');
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data): Role {
            $updateData = ['name' => $data['name']];

            if ($role->name !== $data['name']) {
                $updateData['slug'] = $this->generateUniqueSlug($data['name'], $role->id);
            }

            $role->update($updateData);

            if (array_key_exists('permissions', $data)) {
                $role->permissions()->sync($data['permissions'] ?? []);
            }

            $this->clearCache();

            return $role->fresh()->load('permissions');
        });
    }

    public function updatePermissions(Role $role, array $permissionIds): Role
    {
        return DB::transaction(function () use ($role, $permissionIds): Role {
            $role->permissions()->sync($permissionIds);

            $this->clearCache();

            return $role->fresh()->load('permissions');
        });
    }

    public function delete(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $role->permissions()->detach();
            $role->delete();
            $this->clearCache();
        });
    }

    public function assignRoleToUser(User $user, Role $role): void
    {
        DB::transaction(function () use ($user, $role): void {
            $user->update(['role_id' => $role->id]);
            Cache::forget("user_permissions_{$user->id}");
            $this->clearCache();
        });
    }

    public function getUsersForAssign(): \Illuminate\Database\Eloquent\Collection
    {
        return User::with('role')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role_id']);
    }

    private function clearCache(): void
    {
        Cache::forget('roles.admin_stats');
        Cache::forget('roles.distribution');
    }
}
