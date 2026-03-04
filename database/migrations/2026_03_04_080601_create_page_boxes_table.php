<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('link', 500)->nullable();
            $table->enum('link_target', ['_self', '_blank'])->default('_blank');
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('col_desktop')->default(4);
            $table->unsignedTinyInteger('col_tablet')->default(6);
            $table->unsignedTinyInteger('col_mobile')->default(12);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('page_id');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_boxes');
    }
};
