<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mail_logs', function (Blueprint $table): void {
            $table->string('original_to_email')->nullable()->after('to_name');
            $table->boolean('is_debug_redirect')->default(false)->after('mailable_class');
        });
    }

    public function down(): void
    {
        Schema::table('mail_logs', function (Blueprint $table): void {
            $table->dropColumn(['original_to_email', 'is_debug_redirect']);
        });
    }
};
