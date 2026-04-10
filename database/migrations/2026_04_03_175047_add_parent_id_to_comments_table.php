<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('commentable_id')
                ->constrained('comments')
                ->nullOnDelete();

            $table->index('parent_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->default(5)->change();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
