<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table): void {
            $table->index(['commentable_type', 'commentable_id'], 'comments_commentable_index');
            $table->index(['is_approved', 'created_at'], 'comments_approved_created_index');
        });

        Schema::table('qna_likes', function (Blueprint $table): void {
            $table->index(['likeable_type', 'likeable_id'], 'qna_likes_likeable_index');
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->index('user_id', 'posts_user_id_index');
        });

        Schema::table('mail_logs', function (Blueprint $table): void {
            $table->index('user_id', 'mail_logs_user_id_index');
        });

        if (Schema::hasColumn('advertisements', 'is_active')) {
            Schema::table('advertisements', function (Blueprint $table): void {
                $table->index(['is_active', 'start_date', 'end_date'], 'advertisements_active_dates_index');
            });
        }
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropIndex('comments_commentable_index');
            $table->dropIndex('comments_approved_created_index');
        });

        Schema::table('qna_likes', function (Blueprint $table): void {
            $table->dropIndex('qna_likes_likeable_index');
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->dropIndex('posts_user_id_index');
        });

        Schema::table('mail_logs', function (Blueprint $table): void {
            $table->dropIndex('mail_logs_user_id_index');
        });

        if (Schema::hasColumn('advertisements', 'is_active')) {
            Schema::table('advertisements', function (Blueprint $table): void {
                $table->dropIndex('advertisements_active_dates_index');
            });
        }
    }
};
