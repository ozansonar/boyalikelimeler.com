<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('golden_pen_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('note', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index(['starts_at', 'ends_at']);
        });

        // Migrate existing data from users table
        $users = DB::table('users')
            ->where('is_golden_pen', true)
            ->whereNotNull('golden_pen_starts_at')
            ->whereNotNull('golden_pen_ends_at')
            ->get(['id', 'golden_pen_starts_at', 'golden_pen_ends_at']);

        foreach ($users as $user) {
            DB::table('golden_pen_periods')->insert([
                'user_id'    => $user->id,
                'starts_at'  => $user->golden_pen_starts_at,
                'ends_at'    => $user->golden_pen_ends_at,
                'note'       => 'Mevcut veriden aktarıldı.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Remove old columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_golden_pen']);
            $table->dropIndex(['golden_pen_starts_at', 'golden_pen_ends_at']);
            $table->dropColumn(['is_golden_pen', 'golden_pen_starts_at', 'golden_pen_ends_at']);
        });
    }

    public function down(): void
    {
        // Restore old columns
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_golden_pen')->default(false)->after('allow_messages');
            $table->date('golden_pen_starts_at')->nullable()->after('is_golden_pen');
            $table->date('golden_pen_ends_at')->nullable()->after('golden_pen_starts_at');
            $table->index('is_golden_pen');
            $table->index(['golden_pen_starts_at', 'golden_pen_ends_at']);
        });

        // Migrate data back (latest period per user)
        $periods = DB::table('golden_pen_periods')
            ->whereNull('deleted_at')
            ->orderBy('ends_at', 'desc')
            ->get();

        $migrated = [];
        foreach ($periods as $period) {
            if (in_array($period->user_id, $migrated, true)) {
                continue;
            }
            DB::table('users')->where('id', $period->user_id)->update([
                'is_golden_pen'        => true,
                'golden_pen_starts_at' => $period->starts_at,
                'golden_pen_ends_at'   => $period->ends_at,
            ]);
            $migrated[] = $period->user_id;
        }

        Schema::dropIfExists('golden_pen_periods');
    }
};
