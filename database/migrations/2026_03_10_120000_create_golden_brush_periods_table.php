<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('golden_brush_periods', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('note', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('golden_brush_periods');
    }
};
