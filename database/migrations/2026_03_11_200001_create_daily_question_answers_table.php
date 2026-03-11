<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_question_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('daily_question_id')->constrained('daily_questions')->cascadeOnDelete();
            $table->text('answer_text');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->index();
            $table->string('cookie_token', 64)->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['daily_question_id', 'ip_address']);
            $table->index(['daily_question_id', 'cookie_token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_question_answers');
    }
};
