<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_views', function (Blueprint $table): void {
            $table->id();
            $table->morphs('viewable');
            $table->date('view_date');
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->unique(['viewable_type', 'viewable_id', 'view_date'], 'daily_views_unique');
            $table->index('view_date');
            $table->index(['viewable_type', 'viewable_id', 'view_date'], 'daily_views_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_views');
    }
};
