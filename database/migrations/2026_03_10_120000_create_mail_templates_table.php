<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('mailable_class', 200);
            $table->string('subject', 300);
            $table->string('default_subject', 300);
            $table->longText('body');
            $table->longText('default_body');
            $table->string('description', 300);
            $table->json('variables');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('key');
            $table->index('mailable_class');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};
