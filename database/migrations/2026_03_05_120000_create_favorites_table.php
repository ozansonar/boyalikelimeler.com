<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('favoriteable');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'favoriteable_id', 'favoriteable_type'], 'favorites_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
