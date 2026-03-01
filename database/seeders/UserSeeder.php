<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Süper Admin',
                'email' => 'superadmin@test.com',
                'password' => 'Demo*12345.',
                'role_slug' => 'super_admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'password' => 'Demo*12345.',
                'role_slug' => 'admin',
            ],
            [
                'name' => 'Yazar',
                'email' => 'yazar@test.com',
                'password' => 'Demo*12345.',
                'role_slug' => 'yazar',
            ],
            [
                'name' => 'Kullanıcı',
                'email' => 'kullanici@test.com',
                'password' => 'Demo*12345.',
                'role_slug' => 'kullanici',
            ],
        ];

        foreach ($users as $userData) {
            $role = Role::where('slug', $userData['role_slug'])->first();

            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'role_id' => $role->id,
            ]);
        }
    }
}
