<?php

declare(strict_types=1);

use App\Services\MailTemplateService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(MailTemplateService::class)->seedTemplates();
    }

    public function down(): void
    {
        \App\Models\MailTemplate::whereIn('key', [
            'writer_application_received',
            'writer_application_submitted',
            'writer_application_approved',
            'writer_application_rejected',
        ])->delete();
    }
};
