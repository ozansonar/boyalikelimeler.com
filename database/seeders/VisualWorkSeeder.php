<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\LiteraryCategory;
use App\Models\LiteraryWork;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisualWorkSeeder extends Seeder
{
    public function run(): void
    {
        $yazarRole = Role::where('slug', RoleSlug::Yazar->value)->firstOrFail();

        $writers = User::where('role_id', $yazarRole->id)->get();

        if ($writers->isEmpty()) {
            $this->command->error('Yazar bulunamadı! Önce DemoWriterSeeder çalıştırın.');
            return;
        }

        $categoryIds = LiteraryCategory::where('is_active', true)->pluck('id')->toArray();

        if (empty($categoryIds)) {
            $this->command->error('Aktif kategori bulunamadı!');
            return;
        }

        $totalCreated = 0;

        DB::transaction(function () use ($writers, $categoryIds, &$totalCreated): void {
            $worksToCreate = 10;
            $writerCount = $writers->count();

            LiteraryWork::factory()
                ->count($worksToCreate)
                ->visual()
                ->approved()
                ->sequence(fn ($sequence) => [
                    'literary_category_id' => $categoryIds[array_rand($categoryIds)],
                    'user_id'              => $writers[$sequence->index % $writerCount]->id,
                ])
                ->create();

            $totalCreated = $worksToCreate;
        });

        $this->command->info("{$totalCreated} görsel eser başarıyla oluşturuldu.");
    }
}
