<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Restore if soft-deleted
        Permission::withTrashed()
            ->where('slug', 'author-statistics.view')
            ->whereNotNull('deleted_at')
            ->restore();

        Permission::firstOrCreate(
            ['slug' => 'author-statistics.view'],
            ['name' => 'Yazar İstatistiklerini Görüntüle', 'group' => 'author-statistics', 'sort_order' => 55],
        );

        $adminRoles = Role::whereIn('slug', ['admin', 'super-admin'])->get();
        $permissionIds = Permission::where('slug', 'author-statistics.view')->pluck('id');

        foreach ($adminRoles as $role) {
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    public function down(): void
    {
        $permIds = Permission::where('slug', 'author-statistics.view')->pluck('id');

        DB::table('role_permission')->whereIn('permission_id', $permIds)->delete();
        Permission::where('slug', 'author-statistics.view')->delete();
    }
};
