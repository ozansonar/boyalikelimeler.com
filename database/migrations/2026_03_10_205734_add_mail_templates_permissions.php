<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        $permissions = [
            [
                'name'       => 'Mail Şablonlarını Görüntüle',
                'slug'       => 'mail-templates.view',
                'group'      => 'mail-templates',
                'sort_order' => 135,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Mail Şablonu Düzenle',
                'slug'       => 'mail-templates.edit',
                'group'      => 'mail-templates',
                'sort_order' => 136,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')
                ->where('slug', $permission['slug'])
                ->exists();

            if (! $exists) {
                DB::table('permissions')->insert($permission);
            }
        }

        // Admin rolüne yeni izinleri ata
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            $newPermissionIds = DB::table('permissions')
                ->whereIn('slug', ['mail-templates.view', 'mail-templates.edit'])
                ->pluck('id');

            foreach ($newPermissionIds as $permissionId) {
                $exists = DB::table('role_permission')
                    ->where('role_id', $adminRole->id)
                    ->where('permission_id', $permissionId)
                    ->exists();

                if (! $exists) {
                    DB::table('role_permission')->insert([
                        'role_id'       => $adminRole->id,
                        'permission_id' => $permissionId,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('slug', ['mail-templates.view', 'mail-templates.edit'])
            ->pluck('id');

        DB::table('role_permission')
            ->whereIn('permission_id', $permissionIds)
            ->delete();

        DB::table('permissions')
            ->whereIn('slug', ['mail-templates.view', 'mail-templates.edit'])
            ->delete();
    }
};
