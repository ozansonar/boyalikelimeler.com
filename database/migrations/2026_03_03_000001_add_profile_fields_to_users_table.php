<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username', 30)->nullable()->unique()->after('name');
            $table->string('bio', 300)->nullable()->after('email');
            $table->text('about')->nullable()->after('bio');
            $table->string('avatar')->nullable()->after('about');
            $table->string('cover_image')->nullable()->after('avatar');
            $table->string('location', 100)->nullable()->after('cover_image');
            $table->string('website', 255)->nullable()->after('location');
            $table->date('birthdate')->nullable()->after('website');
            $table->string('gender', 10)->nullable()->after('birthdate');
            $table->string('instagram', 50)->nullable()->after('gender');
            $table->string('twitter', 50)->nullable()->after('instagram');
            $table->string('youtube', 50)->nullable()->after('twitter');
            $table->string('tiktok', 50)->nullable()->after('youtube');
            $table->string('spotify', 100)->nullable()->after('tiktok');
            $table->json('interests')->nullable()->after('spotify');
            $table->boolean('is_public')->default(true)->after('interests');
            $table->boolean('show_email')->default(false)->after('is_public');
            $table->boolean('show_last_seen')->default(true)->after('show_email');
            $table->boolean('allow_messages')->default(true)->after('show_last_seen');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'username',
                'bio',
                'about',
                'avatar',
                'cover_image',
                'location',
                'website',
                'birthdate',
                'gender',
                'instagram',
                'twitter',
                'youtube',
                'tiktok',
                'spotify',
                'interests',
                'is_public',
                'show_email',
                'show_last_seen',
                'allow_messages',
            ]);
        });
    }
};
