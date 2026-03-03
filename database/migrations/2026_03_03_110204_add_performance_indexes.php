<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // literary_works: front-end listing (status=approved + published_at sort)
        Schema::table('literary_works', function (Blueprint $table): void {
            $table->index(['status', 'published_at'], 'lw_status_published_idx');
            $table->index(['literary_category_id', 'status'], 'lw_category_status_idx');
        });

        // categories: active listing sorted
        Schema::table('categories', function (Blueprint $table): void {
            $table->index(['is_active', 'sort_order'], 'cat_active_sort_idx');
        });

        // literary_categories: active listing sorted
        Schema::table('literary_categories', function (Blueprint $table): void {
            $table->index(['is_active', 'sort_order'], 'lcat_active_sort_idx');
        });

        // pages: active listing sorted
        Schema::table('pages', function (Blueprint $table): void {
            $table->index(['is_active', 'sort_order'], 'pg_active_sort_idx');
        });

        // contact_messages: inbox/archive queries
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->index('is_starred', 'cm_starred_idx');
            $table->index('is_archived', 'cm_archived_idx');
            $table->index(['is_archived', 'is_read', 'created_at'], 'cm_inbox_idx');
        });

        // mail_logs: status + date filtering
        Schema::table('mail_logs', function (Blueprint $table): void {
            $table->index(['status', 'created_at'], 'ml_status_created_idx');
        });

        // literary_revisions: pending revisions query
        Schema::table('literary_revisions', function (Blueprint $table): void {
            $table->index(['literary_work_id', 'is_resolved'], 'lr_work_resolved_idx');
        });
    }

    public function down(): void
    {
        Schema::table('literary_works', function (Blueprint $table): void {
            $table->dropIndex('lw_status_published_idx');
            $table->dropIndex('lw_category_status_idx');
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex('cat_active_sort_idx');
        });

        Schema::table('literary_categories', function (Blueprint $table): void {
            $table->dropIndex('lcat_active_sort_idx');
        });

        Schema::table('pages', function (Blueprint $table): void {
            $table->dropIndex('pg_active_sort_idx');
        });

        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropIndex('cm_starred_idx');
            $table->dropIndex('cm_archived_idx');
            $table->dropIndex('cm_inbox_idx');
        });

        Schema::table('mail_logs', function (Blueprint $table): void {
            $table->dropIndex('ml_status_created_idx');
        });

        Schema::table('literary_revisions', function (Blueprint $table): void {
            $table->dropIndex('lr_work_resolved_idx');
        });
    }
};
