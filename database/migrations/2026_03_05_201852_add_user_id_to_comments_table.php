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
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('commentable_id')
                  ->constrained()
                  ->nullOnDelete();

            $table->string('first_name', 100)->nullable()->change();
            $table->string('last_name', 100)->nullable()->change();
            $table->string('email', 255)->nullable()->change();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');

            $table->string('first_name', 100)->nullable(false)->change();
            $table->string('last_name', 100)->nullable(false)->change();
            $table->string('email', 255)->nullable(false)->change();
        });
    }
};
