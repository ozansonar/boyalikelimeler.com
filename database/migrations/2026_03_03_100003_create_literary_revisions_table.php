<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('literary_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('literary_work_id')->constrained('literary_works')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('reason');
            $table->text('author_note')->nullable();
            $table->boolean('is_resolved')->default(false)->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('literary_work_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('literary_revisions');
    }
};
