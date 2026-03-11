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
        $permissions = [
            ['slug' => 'qna.view', 'name' => 'Söz Meydanı Sorularını Görüntüle', 'group' => 'qna', 'sort_order' => 120],
            ['slug' => 'qna.approve', 'name' => 'Söz Meydanı Onayla/Reddet', 'group' => 'qna', 'sort_order' => 121],
            ['slug' => 'qna.delete', 'name' => 'Söz Meydanı Sil', 'group' => 'qna', 'sort_order' => 122],
            ['slug' => 'qna-categories.view', 'name' => 'S.M. Kategorileri Görüntüle', 'group' => 'qna-categories', 'sort_order' => 123],
            ['slug' => 'qna-categories.create', 'name' => 'S.M. Kategorileri Oluştur', 'group' => 'qna-categories', 'sort_order' => 124],
            ['slug' => 'qna-categories.edit', 'name' => 'S.M. Kategorileri Düzenle', 'group' => 'qna-categories', 'sort_order' => 125],
            ['slug' => 'qna-categories.delete', 'name' => 'S.M. Kategorileri Sil', 'group' => 'qna-categories', 'sort_order' => 126],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                ['name' => $perm['name'], 'group' => $perm['group'], 'sort_order' => $perm['sort_order']],
            );
        }

        $adminRoles = Role::whereIn('slug', ['admin', 'super-admin'])->get();
        $permissionIds = Permission::whereIn('slug', array_column($permissions, 'slug'))->pluck('id');

        foreach ($adminRoles as $role) {
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    public function down(): void
    {
        $permSlugs = [
            'qna.view', 'qna.approve', 'qna.delete',
            'qna-categories.view', 'qna-categories.create', 'qna-categories.edit', 'qna-categories.delete',
        ];

        $permIds = Permission::whereIn('slug', $permSlugs)->pluck('id');

        DB::table('role_permission')->whereIn('permission_id', $permIds)->delete();
        Permission::whereIn('slug', $permSlugs)->delete();
    }
};
