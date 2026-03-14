<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ─── Anasayfa ───
            ['group' => 'homepage', 'key' => 'hero_title',       'value' => 'Boyalı Kelimeler'],
            ['group' => 'homepage', 'key' => 'hero_subtitle',    'value' => 'Sosyal Çöküntüye Sanatsal Direniş'],
            ['group' => 'homepage', 'key' => 'hero_tagline',     'value' => '— Bir Sanat Hareketi —'],
            ['group' => 'homepage', 'key' => 'hero_description', 'value' => 'Kelimelerin boyandığı, fırçaların konuştuğu, sanatın direniş olduğu bir platform. 2026\'nın en cesur edebiyat ve sanat hareketi burada başlıyor.'],

            // ─── Genel ───
            ['group' => 'general', 'key' => 'site_name',        'value' => 'Boyalı Kelimeler'],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Edebiyat ve sanat platformu'],
            ['group' => 'general', 'key' => 'site_url',         'value' => 'boyalikelimeler.com'],
            ['group' => 'general', 'key' => 'logo',             'value' => null],
            ['group' => 'general', 'key' => 'favicon',          'value' => null],
            ['group' => 'general', 'key' => 'timezone',         'value' => 'Europe/Istanbul'],
            ['group' => 'general', 'key' => 'language',         'value' => 'tr'],

            // ─── İletişim ───
            ['group' => 'contact', 'key' => 'email',            'value' => 'iletisim@boyalikelimeler.com'],
            ['group' => 'contact', 'key' => 'phone',            'value' => null],
            ['group' => 'contact', 'key' => 'address',          'value' => null],
            ['group' => 'contact', 'key' => 'map_embed',        'value' => null],

            // ─── Sosyal Medya ───
            ['group' => 'social', 'key' => 'facebook',          'value' => null],
            ['group' => 'social', 'key' => 'twitter',           'value' => null],
            ['group' => 'social', 'key' => 'instagram',         'value' => null],
            ['group' => 'social', 'key' => 'youtube',           'value' => null],
            ['group' => 'social', 'key' => 'tiktok',            'value' => null],
            ['group' => 'social', 'key' => 'linkedin',          'value' => null],
            ['group' => 'social', 'key' => 'whatsapp',          'value' => null],

            // ─── SEO ───
            ['group' => 'seo', 'key' => 'meta_title',           'value' => 'Boyalı Kelimeler — Edebiyat ve Sanat Platformu'],
            ['group' => 'seo', 'key' => 'meta_description',     'value' => 'Edebiyat, şiir, öykü, deneme ve sanat dünyasına açılan kapınız.'],
            ['group' => 'seo', 'key' => 'meta_keywords',        'value' => 'edebiyat, şiir, öykü, sanat, dergi'],
            ['group' => 'seo', 'key' => 'google_analytics',     'value' => null],
            ['group' => 'seo', 'key' => 'google_verification',  'value' => null],
            ['group' => 'seo', 'key' => 'robots_txt',           'value' => "User-agent: *\nAllow: /"],

            // ─── E-posta (SMTP) ───
            ['group' => 'smtp', 'key' => 'host',                'value' => null],
            ['group' => 'smtp', 'key' => 'port',                'value' => '587'],
            ['group' => 'smtp', 'key' => 'username',            'value' => null],
            ['group' => 'smtp', 'key' => 'password',            'value' => null],
            ['group' => 'smtp', 'key' => 'encryption',          'value' => 'tls'],
            ['group' => 'smtp', 'key' => 'from_name',           'value' => 'Boyalı Kelimeler'],
            ['group' => 'smtp', 'key' => 'from_email',          'value' => 'noreply@boyalikelimeler.com'],

            // ─── Haftanın Film Önerisi ───
            ['group' => 'homepage', 'key' => 'weekly_movies',       'value' => json_encode([
                ['title' => 'Paterson', 'year' => '2016', 'director' => 'Jim Jarmusch', 'link' => ''],
                ['title' => 'Dead Poets Society', 'year' => '1989', 'director' => '', 'link' => ''],
                ['title' => 'Midnight in Paris', 'year' => '2011', 'director' => '', 'link' => ''],
                ['title' => 'Bright Star', 'year' => '2009', 'director' => '', 'link' => ''],
                ['title' => 'Il Postino', 'year' => '1994', 'director' => '', 'link' => ''],
            ], JSON_UNESCAPED_UNICODE)],
            ['group' => 'homepage', 'key' => 'weekly_movies_count', 'value' => '5'],

            // ─── Bakım Modu ───
            ['group' => 'maintenance', 'key' => 'enabled',      'value' => '0'],
            ['group' => 'maintenance', 'key' => 'message',      'value' => 'Sistemimiz şu anda planlı bakım çalışması nedeniyle geçici olarak hizmet dışıdır. En kısa sürede geri döneceğiz.'],
            ['group' => 'maintenance', 'key' => 'allowed_ips',  'value' => null],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
