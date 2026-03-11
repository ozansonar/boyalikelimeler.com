<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\QnaCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<QnaCategory>
 */
class QnaCategoryFactory extends Factory
{
    protected $model = QnaCategory::class;

    private static int $sortCounter = 0;

    /**
     * @var array<int, array{name: string, icon: string, color: string, description: string}>
     */
    private static array $categories = [
        ['name' => 'Edebiyat', 'icon' => 'fa-solid fa-book-open', 'color' => 'qna-cat-card__icon-wrap--edebiyat', 'description' => 'Roman, şiir, hikâye ve edebiyat dünyasına dair sorular ve cevaplar.'],
        ['name' => 'Felsefe', 'icon' => 'fa-solid fa-brain', 'color' => 'qna-cat-card__icon-wrap--felsefe', 'description' => 'Düşünce tarihi, filozoflar ve felsefi akımlar hakkında tartışmalar.'],
        ['name' => 'Tarih', 'icon' => 'fa-solid fa-landmark', 'color' => 'qna-cat-card__icon-wrap--tarih', 'description' => 'Geçmişten günümüze tarihî olaylar, medeniyetler ve kişilikler.'],
        ['name' => 'Bilim', 'icon' => 'fa-solid fa-flask', 'color' => 'qna-cat-card__icon-wrap--bilim', 'description' => 'Fizik, kimya, biyoloji ve doğa bilimlerine dair merak edilenler.'],
        ['name' => 'Sanat', 'icon' => 'fa-solid fa-palette', 'color' => 'qna-cat-card__icon-wrap--sanat', 'description' => 'Resim, heykel, müzik, sinema ve tüm sanat dallarına ilişkin sorular.'],
        ['name' => 'Psikoloji', 'icon' => 'fa-solid fa-heart-pulse', 'color' => 'qna-cat-card__icon-wrap--psikoloji', 'description' => 'İnsan davranışları, zihin sağlığı ve psikolojik süreçler üzerine.'],
        ['name' => 'Teknoloji', 'icon' => 'fa-solid fa-microchip', 'color' => 'qna-cat-card__icon-wrap--teknoloji', 'description' => 'Yazılım, yapay zekâ, donanım ve dijital dünya hakkında sorular.'],
        ['name' => 'Dil Bilgisi', 'icon' => 'fa-solid fa-spell-check', 'color' => 'qna-cat-card__icon-wrap--dilbilgisi', 'description' => 'Türkçe dil bilgisi, yazım kuralları ve dil kullanımına dair sorular.'],
        ['name' => 'Mitoloji', 'icon' => 'fa-solid fa-dragon', 'color' => 'qna-cat-card__icon-wrap--mitoloji', 'description' => 'Yunan, Türk, Mısır ve dünya mitolojilerinden efsaneler ve hikâyeler.'],
        ['name' => 'Günlük Yaşam', 'icon' => 'fa-solid fa-comments', 'color' => 'qna-cat-card__icon-wrap--default', 'description' => 'Gündelik hayata dair pratik bilgiler, tavsiyeler ve sohbetler.'],
    ];

    public function definition(): array
    {
        $index = self::$sortCounter % count(self::$categories);
        $cat = self::$categories[$index];
        $sortOrder = self::$sortCounter;
        self::$sortCounter++;

        return [
            'name'        => $cat['name'],
            'slug'        => Str::slug($cat['name']),
            'description' => $cat['description'],
            'icon'        => $cat['icon'],
            'color_class' => $cat['color'],
            'sort_order'  => $sortOrder,
            'is_active'   => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
