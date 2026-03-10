<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'Dashboard Görüntüle', 'slug' => 'dashboard.view', 'group' => 'dashboard', 'sort_order' => 1],

            // Kullanıcılar
            ['name' => 'Kullanıcıları Görüntüle', 'slug' => 'users.view', 'group' => 'users', 'sort_order' => 10],
            ['name' => 'Kullanıcı Oluştur', 'slug' => 'users.create', 'group' => 'users', 'sort_order' => 11],
            ['name' => 'Kullanıcı Düzenle', 'slug' => 'users.edit', 'group' => 'users', 'sort_order' => 12],
            ['name' => 'Kullanıcı Sil', 'slug' => 'users.delete', 'group' => 'users', 'sort_order' => 13],

            // Yazılar (Blog)
            ['name' => 'Yazıları Görüntüle', 'slug' => 'posts.view', 'group' => 'posts', 'sort_order' => 20],
            ['name' => 'Yazı Oluştur', 'slug' => 'posts.create', 'group' => 'posts', 'sort_order' => 21],
            ['name' => 'Yazı Düzenle', 'slug' => 'posts.edit', 'group' => 'posts', 'sort_order' => 22],
            ['name' => 'Yazı Sil', 'slug' => 'posts.delete', 'group' => 'posts', 'sort_order' => 23],

            // Kategoriler
            ['name' => 'Kategorileri Görüntüle', 'slug' => 'categories.view', 'group' => 'categories', 'sort_order' => 30],
            ['name' => 'Kategori Oluştur', 'slug' => 'categories.create', 'group' => 'categories', 'sort_order' => 31],
            ['name' => 'Kategori Düzenle', 'slug' => 'categories.edit', 'group' => 'categories', 'sort_order' => 32],
            ['name' => 'Kategori Sil', 'slug' => 'categories.delete', 'group' => 'categories', 'sort_order' => 33],

            // Sayfalar
            ['name' => 'Sayfaları Görüntüle', 'slug' => 'pages.view', 'group' => 'pages', 'sort_order' => 40],
            ['name' => 'Sayfa Oluştur', 'slug' => 'pages.create', 'group' => 'pages', 'sort_order' => 41],
            ['name' => 'Sayfa Düzenle', 'slug' => 'pages.edit', 'group' => 'pages', 'sort_order' => 42],
            ['name' => 'Sayfa Sil', 'slug' => 'pages.delete', 'group' => 'pages', 'sort_order' => 43],

            // Edebiyat Eserleri
            ['name' => 'Eserleri Görüntüle', 'slug' => 'literary-works.view', 'group' => 'literary-works', 'sort_order' => 50],
            ['name' => 'Eser Düzenle', 'slug' => 'literary-works.edit', 'group' => 'literary-works', 'sort_order' => 51],
            ['name' => 'Eser Onayla/Reddet', 'slug' => 'literary-works.moderate', 'group' => 'literary-works', 'sort_order' => 52],
            ['name' => 'Eser Sil', 'slug' => 'literary-works.delete', 'group' => 'literary-works', 'sort_order' => 53],

            // Edebiyat Kategorileri
            ['name' => 'Edebi Kategorileri Görüntüle', 'slug' => 'literary-categories.view', 'group' => 'literary-categories', 'sort_order' => 60],
            ['name' => 'Edebi Kategori Oluştur', 'slug' => 'literary-categories.create', 'group' => 'literary-categories', 'sort_order' => 61],
            ['name' => 'Edebi Kategori Düzenle', 'slug' => 'literary-categories.edit', 'group' => 'literary-categories', 'sort_order' => 62],
            ['name' => 'Edebi Kategori Sil', 'slug' => 'literary-categories.delete', 'group' => 'literary-categories', 'sort_order' => 63],

            // Yorumlar
            ['name' => 'Yorumları Görüntüle', 'slug' => 'comments.view', 'group' => 'comments', 'sort_order' => 70],
            ['name' => 'Yorum Düzenle', 'slug' => 'comments.edit', 'group' => 'comments', 'sort_order' => 71],
            ['name' => 'Yorum Onayla/Reddet', 'slug' => 'comments.moderate', 'group' => 'comments', 'sort_order' => 72],
            ['name' => 'Yorum Sil', 'slug' => 'comments.delete', 'group' => 'comments', 'sort_order' => 73],

            // Mesajlar (İletişim)
            ['name' => 'Mesajları Görüntüle', 'slug' => 'contacts.view', 'group' => 'contacts', 'sort_order' => 80],
            ['name' => 'Mesaj Yanıtla', 'slug' => 'contacts.reply', 'group' => 'contacts', 'sort_order' => 81],
            ['name' => 'Mesaj Sil', 'slug' => 'contacts.delete', 'group' => 'contacts', 'sort_order' => 82],

            // Menüler
            ['name' => 'Menüleri Görüntüle', 'slug' => 'menus.view', 'group' => 'menus', 'sort_order' => 90],
            ['name' => 'Menü Oluştur', 'slug' => 'menus.create', 'group' => 'menus', 'sort_order' => 91],
            ['name' => 'Menü Düzenle', 'slug' => 'menus.edit', 'group' => 'menus', 'sort_order' => 92],
            ['name' => 'Menü Sil', 'slug' => 'menus.delete', 'group' => 'menus', 'sort_order' => 93],

            // Slider
            ['name' => 'Slider Görüntüle', 'slug' => 'home-sliders.view', 'group' => 'home-sliders', 'sort_order' => 100],
            ['name' => 'Slider Oluştur', 'slug' => 'home-sliders.create', 'group' => 'home-sliders', 'sort_order' => 101],
            ['name' => 'Slider Düzenle', 'slug' => 'home-sliders.edit', 'group' => 'home-sliders', 'sort_order' => 102],
            ['name' => 'Slider Sil', 'slug' => 'home-sliders.delete', 'group' => 'home-sliders', 'sort_order' => 103],

            // Yazarlar Sayfası
            ['name' => 'Yazarlar Sayfası Yönetimi', 'slug' => 'authors-page.manage', 'group' => 'authors-page', 'sort_order' => 110],

            // Ressamlar Sayfası
            ['name' => 'Ressamlar Sayfası Yönetimi', 'slug' => 'painters-page.manage', 'group' => 'painters-page', 'sort_order' => 111],

            // Roller & İzinler
            ['name' => 'Rolleri Görüntüle', 'slug' => 'roles.view', 'group' => 'roles', 'sort_order' => 115],
            ['name' => 'Rol Oluştur', 'slug' => 'roles.create', 'group' => 'roles', 'sort_order' => 116],
            ['name' => 'Rol Düzenle', 'slug' => 'roles.edit', 'group' => 'roles', 'sort_order' => 117],
            ['name' => 'Rol Sil', 'slug' => 'roles.delete', 'group' => 'roles', 'sort_order' => 118],
            ['name' => 'Rol Ata', 'slug' => 'roles.assign', 'group' => 'roles', 'sort_order' => 119],

            // Ayarlar
            ['name' => 'Ayarları Görüntüle', 'slug' => 'settings.view', 'group' => 'settings', 'sort_order' => 120],
            ['name' => 'Ayarları Düzenle', 'slug' => 'settings.edit', 'group' => 'settings', 'sort_order' => 121],

            // Mail Logları
            ['name' => 'Mail Loglarını Görüntüle', 'slug' => 'mail-logs.view', 'group' => 'mail-logs', 'sort_order' => 130],
            ['name' => 'Mail Logu Sil', 'slug' => 'mail-logs.delete', 'group' => 'mail-logs', 'sort_order' => 131],

            // Mail Şablonları
            ['name' => 'Mail Şablonlarını Görüntüle', 'slug' => 'mail-templates.view', 'group' => 'mail-templates', 'sort_order' => 135],
            ['name' => 'Mail Şablonu Düzenle', 'slug' => 'mail-templates.edit', 'group' => 'mail-templates', 'sort_order' => 136],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission,
            );
        }

        // Super Admin — tüm izinler otomatik (trait'te bypass)
        // Admin rolüne varsayılan izinleri ata
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole) {
            $allPermissionIds = Permission::pluck('id')->toArray();
            $adminRole->permissions()->sync($allPermissionIds);
        }
    }
}
