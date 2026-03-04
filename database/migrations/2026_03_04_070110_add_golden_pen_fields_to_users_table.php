<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_golden_pen')->default(false)->after('allow_messages');
            $table->date('golden_pen_starts_at')->nullable()->after('is_golden_pen');
            $table->date('golden_pen_ends_at')->nullable()->after('golden_pen_starts_at');

            $table->index('is_golden_pen');
            $table->index(['golden_pen_starts_at', 'golden_pen_ends_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_golden_pen']);
            $table->dropIndex(['golden_pen_starts_at', 'golden_pen_ends_at']);
            $table->dropColumn(['is_golden_pen', 'golden_pen_starts_at', 'golden_pen_ends_at']);
        });
    }
};
