<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\LiteraryWorkStatus;
use App\Enums\RoleSlug;
use App\Models\LiteraryCategory;
use App\Models\LiteraryRevision;
use App\Models\LiteraryWork;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoLiteraryWorkSeeder extends Seeder
{
    private const int WORKS_PER_WRITER = 5;

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
            $this->command->error('Aktif kategori bulunamadı! Önce WriterSeeder çalıştırın.');
            return;
        }

        $adminUser = User::whereHas('role', fn ($q) => $q->whereIn('slug', [
            RoleSlug::Admin->value,
            RoleSlug::SuperAdmin->value,
        ]))->first();

        $faker = \Faker\Factory::create('tr_TR');

        $totalCreated = 0;

        DB::transaction(function () use ($writers, $categoryIds, $adminUser, $faker, &$totalCreated): void {
            foreach ($writers as $writer) {
                $works = LiteraryWork::factory()
                    ->count(self::WORKS_PER_WRITER)
                    ->sequence(fn ($sequence) => [
                        'literary_category_id' => $categoryIds[array_rand($categoryIds)],
                        'user_id'              => $writer->id,
                    ])
                    ->create();

                // Create revision records for revision_requested works
                foreach ($works as $work) {
                    if ($work->status === LiteraryWorkStatus::RevisionRequested && $adminUser) {
                        LiteraryRevision::create([
                            'literary_work_id' => $work->id,
                            'admin_id'         => $adminUser->id,
                            'reason'           => $faker->sentence(rand(10, 20)),
                            'is_resolved'      => false,
                        ]);
                    }
                }

                $totalCreated += self::WORKS_PER_WRITER;
            }
        });

        $this->command->info("{$totalCreated} demo edebi eser başarıyla oluşturuldu ({$writers->count()} yazar × " . self::WORKS_PER_WRITER . ' eser).');
    }
}
