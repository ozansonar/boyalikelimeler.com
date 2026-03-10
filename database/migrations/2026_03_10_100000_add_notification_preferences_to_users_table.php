<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('notify_comment_approved')->default(true)->after('allow_messages');
            $table->boolean('notify_work_status')->default(true)->after('notify_comment_approved');
            $table->boolean('notify_new_comment')->default(true)->after('notify_work_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'notify_comment_approved',
                'notify_work_status',
                'notify_new_comment',
            ]);
        });
    }
};
