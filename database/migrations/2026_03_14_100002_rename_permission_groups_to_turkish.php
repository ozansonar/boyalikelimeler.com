<?php

declare(strict_types=1);

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $groupMap = [
        'advertisements'      => 'Reklamlar',
        'author-statistics'   => 'Yazar İstatistikleri',
        'authors-page'        => 'Yazarlar Sayfası',
        'categories'          => 'Kategoriler',
        'comments'            => 'Yorumlar',
        'contacts'            => 'İletişim Mesajları',
        'daily-questions'     => 'Günün Sorusu',
        'dashboard'           => 'Panel Anasayfa',
        'home-sliders'        => 'Anasayfa Slider',
        'literary-categories' => 'Edebiyat Kategorileri',
        'literary-works'      => 'Edebiyat Eserleri',
        'mail-logs'           => 'Mail Logları',
        'mail-templates'      => 'Mail Şablonları',
        'menus'               => 'Menüler',
        'pages'               => 'Sayfalar',
        'painters-page'       => 'Ressamlar Sayfası',
        'polls'               => 'Anketler',
        'posts'               => 'Blog Yazıları',
        'qna'                 => 'Söz Meydanı',
        'qna-categories'      => 'Söz Meydanı Kategorileri',
        'roles'               => 'Roller',
        'settings'            => 'Ayarlar',
        'users'               => 'Kullanıcılar',
    ];

    public function up(): void
    {
        foreach ($this->groupMap as $old => $new) {
            Permission::withTrashed()
                ->where('group', $old)
                ->update(['group' => $new]);
        }
    }

    public function down(): void
    {
        $reversed = array_flip($this->groupMap);

        foreach ($reversed as $current => $original) {
            Permission::withTrashed()
                ->where('group', $current)
                ->update(['group' => $original]);
        }
    }
};
