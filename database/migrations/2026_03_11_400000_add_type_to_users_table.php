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
        Schema::table('users', function (Blueprint $table): void {
            $table->string('type', 20)->default('kullanici')->after('role_id')->index();
        });

        // Populate type from existing role slugs
        $roleMap = DB::table('roles')
            ->whereIn('slug', ['super_admin', 'admin'])
            ->pluck('slug', 'id');

        foreach ($roleMap as $roleId => $slug) {
            DB::table('users')
                ->where('role_id', $roleId)
                ->update(['type' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }
};
