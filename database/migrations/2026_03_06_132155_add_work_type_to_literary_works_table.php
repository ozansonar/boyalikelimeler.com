<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('literary_works', function (Blueprint $table) {
            $table->string('work_type', 20)->default('written')->after('status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('literary_works', function (Blueprint $table) {
            $table->dropIndex(['work_type']);
            $table->dropColumn('work_type');
        });
    }
};
