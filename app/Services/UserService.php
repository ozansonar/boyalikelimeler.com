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
    public function __construct(
        private readonly GoldenPenPeriodService $goldenPenPeriodService,
    ) {}
    /**
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return Cache::remember('admin.users.stats', 300, function (): array {
            $row = User::selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) as verified,
                SUM(CASE WHEN email_verified_at IS NULL THEN 1 ELSE 0 END) as unverified,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_month
            ", [now()->startOfMonth()])->first();

            return [
                'total'      => (int) $row->total,
                'verified'   => (int) $row->verified,
                'unverified' => (int) $row->unverified,
                'this_month' => (int) $row->this_month,
            ];
        });
    }

    /**
     * @return array<string, int>
     */
    public function getRoleCounts(): array
    {
        return Cache::remember('admin.users.role_counts', 300, function (): array {
            $rows = User::join('roles', 'users.role_id', '=', 'roles.id')
                ->selectRaw('roles.slug, COUNT(*) as cnt')
                ->whereNull('users.deleted_at')
                ->groupBy('roles.slug')
                ->pluck('cnt', 'slug');

            $counts = [];
            foreach (RoleSlug::cases() as $roleSlug) {
                $counts[$roleSlug->value] = (int) ($rows[$roleSlug->value] ?? 0);
            }

            return $counts;
        });
    }

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $query = User::with(['role', 'activeGoldenPenPeriod']);

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

            if (! empty($data['golden_pen_periods'])) {
                $this->goldenPenPeriodService->syncPeriods($user, $data['golden_pen_periods']);
            }

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

            if (isset($data['golden_pen_periods_sent'])) {
                $this->goldenPenPeriodService->syncPeriods($user, $data['golden_pen_periods'] ?? []);
            }

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
