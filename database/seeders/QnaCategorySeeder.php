<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\QnaCategory;
use Illuminate\Database\Seeder;

class QnaCategorySeeder extends Seeder
{
    public function run(): void
    {
        QnaCategory::factory()->count(10)->create();
    }
}
