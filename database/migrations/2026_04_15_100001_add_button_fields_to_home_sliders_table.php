<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_sliders', function (Blueprint $table) {
            $table->string('button_text', 100)->nullable()->after('description');
            $table->string('button_link', 500)->nullable()->after('button_text');
            $table->string('button_target', 10)->default('_self')->after('button_link');
        });
    }

    public function down(): void
    {
        Schema::table('home_sliders', function (Blueprint $table) {
            $table->dropColumn(['button_text', 'button_link', 'button_target']);
        });
    }
};
