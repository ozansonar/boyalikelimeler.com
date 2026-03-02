<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Şiir',
                'slug'        => 'siir',
                'description' => 'Duyguların en saf hali, kelimelerin dansı. Şiirler ve şiir çözümlemeleri.',
                'sort_order'  => 0,
                'is_active'   => true,
            ],
            [
                'name'        => 'Öykü',
                'slug'        => 'oyku',
                'description' => 'Kısa ve uzun öyküler, hikâye anlatıcılığının incelikleri.',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'name'        => 'Deneme',
                'slug'        => 'deneme',
                'description' => 'Düşünce yazıları, denemeler ve felsefi bakış açıları.',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'name'        => 'Sanat',
                'slug'        => 'sanat',
                'description' => 'Resim, müzik, sinema ve tüm sanat dallarından incelemeler.',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
            [
                'name'        => 'Edebiyat Dünyası',
                'slug'        => 'edebiyat-dunyasi',
                'description' => 'Edebiyat haberleri, kitap incelemeleri ve yazarlarla söyleşiler.',
                'sort_order'  => 4,
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
