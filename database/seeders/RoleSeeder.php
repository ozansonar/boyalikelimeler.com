<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Süper Admin', 'slug' => 'super_admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Yazar', 'slug' => 'yazar'],
            ['name' => 'Kullanıcı', 'slug' => 'kullanici'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
