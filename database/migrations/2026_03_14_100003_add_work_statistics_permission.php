<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::create([
            'name'       => 'Eser İstatistiklerini Görüntüle',
            'slug'       => 'work-statistics.view',
            'group'      => 'Eser İstatistikleri',
            'sort_order' => 56,
        ]);

        $adminRoles = Role::whereIn('slug', ['super-admin', 'admin'])->get();

        foreach ($adminRoles as $role) {
            $role->permissions()->attach($permission->id);
        }
    }

    public function down(): void
    {
        $permission = Permission::where('slug', 'work-statistics.view')->first();

        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }
    }
};
