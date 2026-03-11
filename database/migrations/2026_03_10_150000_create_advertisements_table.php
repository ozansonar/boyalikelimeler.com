<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('position', 30)->index();
            $table->string('image')->nullable();
            $table->string('link', 500)->nullable();
            $table->string('link_target', 10)->default('_blank');
            $table->boolean('is_active')->default(true)->index();
            $table->date('start_date')->nullable()->index();
            $table->date('end_date')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('click_count')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
