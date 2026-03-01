<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Süper Admin',
                'email'     => 'superadmin@test.com',
                'password'  => 'Demo*12345.',
                'role_slug' => RoleSlug::SuperAdmin,
            ],
            [
                'name'      => 'Admin',
                'email'     => 'admin@test.com',
                'password'  => 'Demo*12345.',
                'role_slug' => RoleSlug::Admin,
            ],
            [
                'name'      => 'Yazar',
                'email'     => 'yazar@test.com',
                'password'  => 'Demo*12345.',
                'role_slug' => RoleSlug::Yazar,
            ],
            [
                'name'      => 'Kullanıcı',
                'email'     => 'kullanici@test.com',
                'password'  => 'Demo*12345.',
                'role_slug' => RoleSlug::Kullanici,
            ],
        ];

        foreach ($users as $userData) {
            $role = Role::where('slug', $userData['role_slug']->value)->first();

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => $userData['password'],
                    'role_id'  => $role->id,
                ],
            );
        }
    }
}
