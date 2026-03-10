<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LiteraryWorkStatus;
use App\Enums\LiteraryWorkType;
use App\Models\LiteraryCategory;
use App\Models\LiteraryWork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LiteraryWork>
 */
class LiteraryWorkFactory extends Factory
{
    protected $model = LiteraryWork::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('tr_TR');

        $title = $this->generateTitle($faker);
        $slug = Str::slug($title) . '-' . Str::random(5);
        $paragraphCount = rand(5, 10);
        $body = $this->generateBody($faker, $paragraphCount);
        $excerpt = Str::limit(strip_tags($body), 150);
        $status = $this->randomStatus();
        $createdAt = now()->subDays(rand(1, 120))->subHours(rand(0, 23));

        return [
            'title'                => $title,
            'slug'                 => $slug,
            'excerpt'              => $excerpt,
            'body'                 => $body,
            'cover_image'          => null,
            'literary_category_id' => LiteraryCategory::inRandomOrder()->value('id'),
            'user_id'              => User::inRandomOrder()->value('id'),
            'status'               => $status,
            'work_type'            => LiteraryWorkType::Written,
            'meta_title'           => Str::limit($title, 190),
            'meta_description'     => Str::limit($excerpt, 290),
            'view_count'           => rand(0, 1500),
            'published_at'         => $status === LiteraryWorkStatus::Approved
                ? $createdAt->copy()->addHours(rand(1, 48))
                : null,
            'created_at'           => $createdAt,
            'updated_at'           => $createdAt->copy()->addMinutes(rand(0, 120)),
        ];
    }

    /**
     * 20-40 karakter arası başlık üret.
     */
    private function generateTitle(\Faker\Generator $faker): string
    {
        $maxAttempts = 20;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $wordCount = rand(3, 7);
            $title = $faker->sentence($wordCount, true);
            $title = rtrim($title, '.');
            $length = mb_strlen($title);

            if ($length >= 20 && $length <= 40) {
                return $title;
            }
        }

        // Fallback: trim or pad to fit
        $title = $faker->sentence(4, true);
        $title = rtrim($title, '.');

        if (mb_strlen($title) > 40) {
            $title = mb_substr($title, 0, 40);
            $title = rtrim($title);
        }

        if (mb_strlen($title) < 20) {
            $title .= ' ' . $faker->word();
        }

        return $title;
    }

    /**
     * 5-10 paragraf HTML body üret.
     */
    private function generateBody(\Faker\Generator $faker, int $paragraphCount): string
    {
        $paragraphs = [];

        for ($i = 0; $i < $paragraphCount; $i++) {
            $sentenceCount = rand(3, 8);
            $text = $faker->paragraph($sentenceCount, true);
            $paragraphs[] = '<p>' . $text . '</p>';
        }

        return implode("\n", $paragraphs);
    }

    /**
     * Status dağılımı: %70 approved, %15 pending, %10 revision_requested, %5 rejected.
     */
    private function randomStatus(): LiteraryWorkStatus
    {
        $rand = rand(1, 100);

        return match (true) {
            $rand <= 70  => LiteraryWorkStatus::Approved,
            $rand <= 85  => LiteraryWorkStatus::Pending,
            $rand <= 95  => LiteraryWorkStatus::RevisionRequested,
            default      => LiteraryWorkStatus::Rejected,
        };
    }

    /* ── State methods ── */

    public function approved(): static
    {
        return $this->state(fn () => [
            'status'       => LiteraryWorkStatus::Approved,
            'published_at' => now()->subDays(rand(1, 90)),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status'       => LiteraryWorkStatus::Pending,
            'published_at' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'       => LiteraryWorkStatus::Rejected,
            'published_at' => null,
        ]);
    }

    public function revisionRequested(): static
    {
        return $this->state(fn () => [
            'status'       => LiteraryWorkStatus::RevisionRequested,
            'published_at' => null,
        ]);
    }

    public function written(): static
    {
        return $this->state(fn () => [
            'work_type' => LiteraryWorkType::Written,
        ]);
    }

    public function visual(): static
    {
        return $this->state(fn () => [
            'work_type' => LiteraryWorkType::Visual,
        ]);
    }
}
