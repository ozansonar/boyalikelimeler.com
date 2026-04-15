<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pwa_installs', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 20)->default('unknown')->index();
            $table->string('user_agent', 500)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['created_at']);
            $table->index(['platform', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pwa_installs');
    }
};
