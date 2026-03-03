<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MenuSeeder::class,
            SettingSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
            WriterSeeder::class,
            ContactMessageSeeder::class,
            PageSeeder::class,
        ]);
    }
}
