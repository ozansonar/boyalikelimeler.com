<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UserService
{
    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return Cache::remember('admin.users.stats', 300, function (): array {
            return [
                'total'        => User::count(),
                'verified'     => User::whereNotNull('email_verified_at')->count(),
                'unverified'   => User::whereNull('email_verified_at')->count(),
                'this_month'   => User::where('created_at', '>=', now()->startOfMonth())->count(),
            ];
        });
    }

    /**
     * @return array<string, int>
     */
    public function getRoleCounts(): array
    {
        return Cache::remember('admin.users.role_counts', 300, function (): array {
            $counts = [];
            foreach (RoleSlug::cases() as $roleSlug) {
                $counts[$roleSlug->value] = User::whereHas('role', fn ($q) => $q->where('slug', $roleSlug->value))->count();
            }
            return $counts;
        });
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = User::with('role');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['role'])) {
            $query->whereHas('role', fn ($q) => $q->where('slug', $filters['role']));
        }

        if (! empty($filters['status'])) {
            match ($filters['status']) {
                'verified'   => $query->whereNotNull('email_verified_at'),
                'unverified' => $query->whereNull('email_verified_at'),
                default      => null,
            };
        }

        $sortField = $filters['sort'] ?? 'created_at';
        $sortDir = $filters['dir'] ?? 'desc';
        $allowedSorts = ['name', 'email', 'created_at'];

        if (! in_array($sortField, $allowedSorts, true)) {
            $sortField = 'created_at';
        }

        return $query->orderBy($sortField, $sortDir)->paginate($perPage)->withQueryString();
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::create([
                'name'              => $data['first_name'] . ' ' . $data['last_name'],
                'email'             => $data['email'],
                'password'          => $data['password'],
                'role_id'           => $data['role_id'],
                'email_verified_at' => ! empty($data['email_verified']) ? now() : null,
            ]);

            Cache::forget('admin.users.stats');
            Cache::forget('admin.users.role_counts');

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $updateData = [
                'name'              => $data['first_name'] . ' ' . $data['last_name'],
                'email'             => $data['email'],
                'role_id'           => $data['role_id'],
                'email_verified_at' => ! empty($data['email_verified']) ? ($user->email_verified_at ?? now()) : null,
            ];

            if (! empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            $user->update($updateData);

            Cache::forget('admin.users.stats');
            Cache::forget('admin.users.role_counts');

            return $user->fresh();
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->delete();

            Cache::forget('admin.users.stats');
            Cache::forget('admin.users.role_counts');
        });
    }
}
