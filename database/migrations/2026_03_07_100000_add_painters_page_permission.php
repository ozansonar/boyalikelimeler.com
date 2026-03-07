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
        $permission = Permission::firstOrCreate(
            ['slug' => 'painters-page.manage'],
            [
                'name'        => 'Ressamlar Sayfası Yönetimi',
                'group'       => 'painters-page',
                'sort_order'  => 111,
                'description' => null,
            ]
        );

        // Assign to super-admin role
        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin && $permission) {
            DB::table('role_permission')->insertOrIgnore([
                'role_id'       => $superAdmin->id,
                'permission_id' => $permission->id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        $permission = Permission::where('slug', 'painters-page.manage')->first();

        if ($permission) {
            DB::table('role_permission')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        }
    }
};
