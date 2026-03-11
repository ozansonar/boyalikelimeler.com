<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\QnaStatus;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QnaAnswer>
 */
class QnaAnswerFactory extends Factory
{
    protected $model = QnaAnswer::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('tr_TR');
        $createdAt = now()->subDays(rand(1, 40))->subHours(rand(0, 23));

        return [
            'qna_question_id' => QnaQuestion::inRandomOrder()->value('id'),
            'user_id'         => User::inRandomOrder()->value('id'),
            'body'            => '<p>' . $faker->paragraph(rand(2, 5)) . '</p>',
            'status'          => QnaStatus::Approved,
            'like_count'      => rand(0, 30),
            'ip_address'      => $faker->ipv4(),
            'created_at'      => $createdAt,
            'updated_at'      => $createdAt->copy()->addMinutes(rand(0, 30)),
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
