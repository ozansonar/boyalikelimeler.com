<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qna_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('qna_question_id')->constrained('qna_questions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->unsignedInteger('like_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('qna_question_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qna_answers');
    }
};
