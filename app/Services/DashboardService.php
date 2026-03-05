<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\LiteraryWork;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class DashboardService
{
    public function getStats(): array
    {
        return Cache::remember('dashboard.stats', 300, function (): array {
            return DB::transaction(function (): array {
                $totalUsers = User::count();
                $totalPosts = Post::count();
                $totalWorks = LiteraryWork::count();
                $totalComments = Comment::count();
                $pendingWorks = LiteraryWork::where('status', 'pending')->count();
                $pendingComments = Comment::where('is_approved', false)->count();
                $unreadMessages = ContactMessage::where('is_read', false)->count();

                $newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
                $newUsersLastMonth = User::whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth(),
                ])->count();

                $userGrowth = $newUsersLastMonth > 0
                    ? round((($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100, 1)
                    : ($newUsersThisMonth > 0 ? 100.0 : 0.0);

                $newWorksThisMonth = LiteraryWork::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
                $newWorksLastMonth = LiteraryWork::whereBetween('created_at', [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth(),
                ])->count();

                $workGrowth = $newWorksLastMonth > 0
                    ? round((($newWorksThisMonth - $newWorksLastMonth) / $newWorksLastMonth) * 100, 1)
                    : ($newWorksThisMonth > 0 ? 100.0 : 0.0);

                return [
                    'total_users'       => $totalUsers,
                    'total_posts'       => $totalPosts,
                    'total_works'       => $totalWorks,
                    'total_comments'    => $totalComments,
                    'pending_works'     => $pendingWorks,
                    'pending_comments'  => $pendingComments,
                    'unread_messages'   => $unreadMessages,
                    'new_users_month'   => $newUsersThisMonth,
                    'user_growth'       => $userGrowth,
                    'new_works_month'   => $newWorksThisMonth,
                    'work_growth'       => $workGrowth,
                ];
            });
        });
    }

    public function getMonthlyUserRegistrations(): array
    {
        return Cache::remember('dashboard.monthly_users', 600, function (): array {
            $data = User::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count'),
            )
                ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            $labels = [];
            $values = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $key = $date->format('Y-m');
                $labels[] = $date->translatedFormat('M Y');
                $values[] = $data[$key] ?? 0;
            }

            return ['labels' => $labels, 'values' => $values];
        });
    }

    public function getMonthlyWorks(): array
    {
        return Cache::remember('dashboard.monthly_works', 600, function (): array {
            $data = LiteraryWork::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count'),
            )
                ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();

            $labels = [];
            $values = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $key = $date->format('Y-m');
                $labels[] = $date->translatedFormat('M Y');
                $values[] = $data[$key] ?? 0;
            }

            return ['labels' => $labels, 'values' => $values];
        });
    }

    public function getRoleDistribution(): array
    {
        return Cache::remember('dashboard.role_dist', 600, function (): array {
            return User::join('roles', 'users.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('COUNT(*) as count'))
                ->groupBy('roles.name')
                ->orderByDesc('count')
                ->pluck('count', 'name')
                ->toArray();
        });
    }

    public function getWorkStatusDistribution(): array
    {
        return Cache::remember('dashboard.work_status', 600, function (): array {
            return LiteraryWork::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });
    }

    public function getLatestWorks(int $limit = 5): Collection
    {
        return LiteraryWork::with(['author:id,name,username', 'category:id,name'])
            ->select('id', 'title', 'slug', 'status', 'user_id', 'literary_category_id', 'created_at')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getLatestComments(int $limit = 5): Collection
    {
        return Comment::with('commentable')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getLatestUsers(int $limit = 5): Collection
    {
        return User::with('role:id,name')
            ->select('id', 'name', 'email', 'username', 'role_id', 'created_at')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getTopAuthors(int $limit = 5): Collection
    {
        return User::withCount(['literaryWorks' => function ($query): void {
            $query->where('status', 'published');
        }])
            ->having('literary_works_count', '>', 0)
            ->orderByDesc('literary_works_count')
            ->limit($limit)
            ->get(['id', 'name', 'username']);
    }
}
