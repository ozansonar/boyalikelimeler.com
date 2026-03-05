<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\LinkTarget;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Header Navbar ───
        $header = Menu::create([
            'name'        => 'Header Menü',
            'location'    => 'header',
            'description' => 'Üst navigasyon çubuğundaki ana menü',
            'is_active'   => true,
        ]);

        $headerItems = [
            ['title' => 'Ana Sayfa',    'url' => '/',    'icon' => 'fa-solid fa-house',      'sort_order' => 0],
            ['title' => 'Edebiyat',     'url' => '#',    'icon' => 'fa-solid fa-book-open',   'sort_order' => 1],
            ['title' => 'Sanat',        'url' => '#',    'icon' => 'fa-solid fa-palette',     'sort_order' => 2],
            ['title' => 'Söz Meydanı',  'url' => '#',    'icon' => 'fa-solid fa-comments',    'sort_order' => 3],
            ['title' => 'Dergimiz',     'url' => '#',    'icon' => 'fa-solid fa-newspaper',   'sort_order' => 4],
            ['title' => 'İletişim',     'url' => '#',    'icon' => 'fa-solid fa-paper-plane', 'sort_order' => 5],
        ];

        foreach ($headerItems as $item) {
            MenuItem::create(array_merge($item, [
                'menu_id'   => $header->id,
                'target'    => LinkTarget::Self->value,
                'is_active' => true,
            ]));
        }

        // ─── Footer: Keşfet ───
        $footerDiscover = Menu::create([
            'name'        => 'Footer — Keşfet',
            'location'    => 'footer_discover',
            'description' => 'Footer Keşfet bölümündeki linkler',
            'is_active'   => true,
        ]);

        $discoverItems = [
            ['title' => 'Yazılar',      'url' => '#', 'sort_order' => 0],
            ['title' => 'Resimler',     'url' => '#', 'sort_order' => 1],
            ['title' => 'Sanat Okulu',  'url' => '#', 'sort_order' => 2],
            ['title' => 'Söz Meydanı',  'url' => '#', 'sort_order' => 3],
        ];

        foreach ($discoverItems as $item) {
            MenuItem::create(array_merge($item, [
                'menu_id'   => $footerDiscover->id,
                'target'    => LinkTarget::Self->value,
                'is_active' => true,
            ]));
        }

        // ─── Footer: Yarışmalar ───
        $footerCompetitions = Menu::create([
            'name'        => 'Footer — Yarışmalar',
            'location'    => 'footer_competitions',
            'description' => 'Footer Yarışmalar bölümündeki linkler',
            'is_active'   => true,
        ]);

        $competitionItems = [
            ['title' => 'Altın Kalem', 'url' => '#', 'sort_order' => 0],
            ['title' => 'Altın Fırça', 'url' => '#', 'sort_order' => 1],
            ['title' => 'Dergimiz',    'url' => '#', 'sort_order' => 2],
            ['title' => 'Astroloji',   'url' => '#', 'sort_order' => 3],
        ];

        foreach ($competitionItems as $item) {
            MenuItem::create(array_merge($item, [
                'menu_id'   => $footerCompetitions->id,
                'target'    => LinkTarget::Self->value,
                'is_active' => true,
            ]));
        }

        // ─── Footer: Kurumsal ───
        $footerCorporate = Menu::create([
            'name'        => 'Footer — Kurumsal',
            'location'    => 'footer_corporate',
            'description' => 'Footer Kurumsal bölümündeki linkler',
            'is_active'   => true,
        ]);

        $corporateItems = [
            ['title' => 'Hakkımızda',    'url' => '/hakkimizda', 'sort_order' => 0],
            ['title' => 'Yönetim Ekibi', 'url' => '#',           'sort_order' => 1],
            ['title' => 'İletişim',       'url' => '#',           'sort_order' => 2],
            ['title' => 'Gizlilik',       'url' => '#',           'sort_order' => 3],
        ];

        foreach ($corporateItems as $item) {
            MenuItem::create(array_merge($item, [
                'menu_id'   => $footerCorporate->id,
                'target'    => LinkTarget::Self->value,
                'is_active' => true,
            ]));
        }
    }
}
