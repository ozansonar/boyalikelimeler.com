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
        DB::transaction(function (): void {
            $maxOrder = Permission::max('sort_order') ?? 0;

            $permissions = [
                [
                    'name'        => 'Yazar Başvurularını Görüntüle',
                    'slug'        => 'writer-applications.view',
                    'group'       => 'Yönetim',
                    'description' => 'Yazar başvuru listesini ve detaylarını görüntüleme',
                    'sort_order'  => $maxOrder + 1,
                ],
                [
                    'name'        => 'Yazar Başvurularını Yönet',
                    'slug'        => 'writer-applications.manage',
                    'group'       => 'Yönetim',
                    'description' => 'Yazar başvurularını onaylama veya reddetme',
                    'sort_order'  => $maxOrder + 2,
                ],
            ];

            $permissionIds = [];
            foreach ($permissions as $perm) {
                $p = Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
                $permissionIds[] = $p->id;
            }

            // Assign to SuperAdmin and Admin roles
            $roles = Role::whereIn('slug', ['super_admin', 'admin'])->get();
            foreach ($roles as $role) {
                $role->permissions()->syncWithoutDetaching($permissionIds);
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $permissions = Permission::whereIn('slug', [
                'writer-applications.view',
                'writer-applications.manage',
            ])->get();

            foreach ($permissions as $permission) {
                $permission->roles()->detach();
                $permission->delete();
            }
        });
    }
};
