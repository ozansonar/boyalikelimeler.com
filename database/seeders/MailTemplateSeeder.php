<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\MailTemplateService;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        app(MailTemplateService::class)->seedTemplates();
    }
}
