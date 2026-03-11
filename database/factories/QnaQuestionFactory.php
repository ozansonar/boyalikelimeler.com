<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\QnaStatus;
use App\Models\QnaCategory;
use App\Models\QnaQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<QnaQuestion>
 */
class QnaQuestionFactory extends Factory
{
    protected $model = QnaQuestion::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('tr_TR');

        $title = $faker->sentence(rand(5, 10));
        $title = rtrim($title, '.');
        $slug = Str::slug($title) . '-' . Str::random(5);
        $createdAt = now()->subDays(rand(1, 60))->subHours(rand(0, 23));

        return [
            'user_id'         => User::inRandomOrder()->value('id'),
            'qna_category_id' => QnaCategory::inRandomOrder()->value('id'),
            'title'           => $title,
            'slug'            => $slug,
            'body'            => '<p>' . $faker->paragraph(rand(3, 6)) . '</p>',
            'status'          => QnaStatus::Approved,
            'view_count'      => rand(5, 500),
            'like_count'      => rand(0, 50),
            'answer_count'    => 0,
            'ip_address'      => $faker->ipv4(),
            'created_at'      => $createdAt,
            'updated_at'      => $createdAt->copy()->addMinutes(rand(0, 60)),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'status' => QnaStatus::Pending,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => [
            'status' => QnaStatus::Rejected,
        ]);
    }
}
