<?php

declare(strict_types=1);

use App\Models\QnaQuestion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        QnaQuestion::query()
            ->whereRaw('slug != LOWER(slug)')
            ->each(function (QnaQuestion $question): void {
                $newSlug = Str::lower($question->slug);

                if (QnaQuestion::where('slug', $newSlug)->where('id', '!=', $question->id)->exists()) {
                    $newSlug = $newSlug . '-' . strtolower(Str::random(3));
                }

                $question->update(['slug' => $newSlug]);
            });
    }

    public function down(): void
    {
        // Irreversible — slug case info is lost
    }
};
