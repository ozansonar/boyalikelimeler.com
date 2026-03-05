<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoWriterSeeder extends Seeder
{
    public function run(): void
    {
        $yazarRole = Role::where('slug', RoleSlug::Yazar->value)->firstOrFail();
        $hashedPassword = Hash::make('Demo*12345.');

        $maleNames = [
            'Ahmet', 'Mehmet', 'Mustafa', 'Ali', 'Hüseyin', 'Hasan', 'İbrahim', 'İsmail',
            'Yusuf', 'Osman', 'Murat', 'Ömer', 'Halil', 'Süleyman', 'Abdullah', 'Mahmut',
            'Recep', 'Salih', 'Fatih', 'Kadir', 'Emre', 'Burak', 'Serkan', 'Cem', 'Deniz',
            'Onur', 'Tolga', 'Barış', 'Kerem', 'Arda', 'Kaan', 'Berk', 'Efe', 'Eren',
            'Furkan', 'Gökhan', 'Uğur', 'Volkan', 'Erdem', 'Sinan', 'Selim', 'Taner',
            'Alp', 'Doruk', 'Yiğit', 'Koray', 'Cenk', 'Taylan', 'Ozan', 'Tuğrul',
        ];

        $femaleNames = [
            'Fatma', 'Ayşe', 'Emine', 'Hatice', 'Zeynep', 'Elif', 'Merve', 'Büşra',
            'Zehra', 'Esra', 'Nur', 'Seda', 'Özge', 'Gizem', 'Derya', 'Pınar',
            'Sibel', 'Aslı', 'Ceren', 'İrem', 'Ebru', 'Dilek', 'Hülya', 'Sevgi',
            'Gamze', 'Burcu', 'Tuğba', 'Başak', 'Damla', 'Cansu', 'Ece', 'Defne',
            'Melis', 'Naz', 'Buse', 'Yaren', 'Selin', 'Nehir', 'Ada', 'Lale',
            'Gül', 'Nilüfer', 'Sinem', 'Dilan', 'Cemre', 'Berna', 'Ilgın', 'Azra',
            'Simge', 'Ülkü',
        ];

        $surnames = [
            'Yılmaz', 'Kaya', 'Demir', 'Çelik', 'Şahin', 'Yıldız', 'Yıldırım', 'Öztürk',
            'Aydın', 'Özdemir', 'Arslan', 'Doğan', 'Kılıç', 'Aslan', 'Çetin', 'Kara',
            'Koç', 'Kurt', 'Özkan', 'Şimşek', 'Polat', 'Korkmaz', 'Erdoğan', 'Güneş',
            'Aktaş', 'Bulut', 'Karaca', 'Aksoy', 'Acar', 'Balcı', 'Başaran', 'Bayrak',
            'Bozkurt', 'Coşkun', 'Çakır', 'Dağlı', 'Elmas', 'Ercan', 'Güler', 'Işık',
            'Kaplan', 'Keskin', 'Kutlu', 'Oral', 'Özen', 'Sağlam', 'Tekin', 'Tunç',
            'Uzun', 'Yalçın', 'Zengin', 'Toprak', 'Ay', 'Tan', 'Uysal', 'Güngör',
            'Peker', 'Sönmez', 'Taş', 'Candan', 'Çınar', 'Durmaz', 'Ekinci', 'Gümüş',
        ];

        $cities = [
            'İstanbul' => ['Kadıköy', 'Beşiktaş', 'Üsküdar', 'Bakırköy', 'Beyoğlu', 'Şişli', 'Fatih', 'Maltepe', 'Ataşehir', 'Sarıyer'],
            'Ankara' => ['Çankaya', 'Keçiören', 'Mamak', 'Yenimahalle', 'Etimesgut', 'Sincan', 'Altındağ', 'Pursaklar'],
            'İzmir' => ['Konak', 'Karşıyaka', 'Bornova', 'Buca', 'Bayraklı', 'Çiğli', 'Alsancak', 'Urla'],
            'Bursa' => ['Osmangazi', 'Nilüfer', 'Yıldırım', 'Mudanya', 'Gemlik'],
            'Antalya' => ['Muratpaşa', 'Konyaaltı', 'Kepez', 'Alanya', 'Manavgat'],
            'Eskişehir' => ['Odunpazarı', 'Tepebaşı'],
            'Trabzon' => ['Ortahisar', 'Akçaabat', 'Yomra'],
            'Konya' => ['Selçuklu', 'Meram', 'Karatay'],
            'Gaziantep' => ['Şahinbey', 'Şehitkâmil', 'Oğuzeli'],
            'Adana' => ['Seyhan', 'Çukurova', 'Yüreğir'],
            'Samsun' => ['Atakum', 'İlkadım', 'Canik'],
            'Kayseri' => ['Melikgazi', 'Kocasinan', 'Talas'],
            'Mersin' => ['Mezitli', 'Yenişehir', 'Akdeniz', 'Toroslar'],
            'Diyarbakır' => ['Bağlar', 'Kayapınar', 'Yenişehir', 'Sur'],
            'Muğla' => ['Bodrum', 'Fethiye', 'Marmaris', 'Dalaman'],
            'Denizli' => ['Merkezefendi', 'Pamukkale'],
            'Edirne' => ['Merkez', 'Keşan', 'Uzunköprü'],
            'Çanakkale' => ['Merkez', 'Biga', 'Gelibolu'],
            'Hatay' => ['Antakya', 'İskenderun', 'Defne'],
            'Mardin' => ['Artuklu', 'Kızıltepe', 'Midyat'],
        ];

        $bios = [
            'Kelimelerin büyülü dünyasında kaybolmayı seven bir ruh.',
            'Şiir ve düzyazı arasında mekik dokuyan bir kalem.',
            'Edebiyatın iyileştirici gücüne inanan bir yazar.',
            'Hayatı kelimelerle anlamlandırmaya çalışan biri.',
            'Yazmanın en güzel terapi olduğuna inanan bir edebiyat tutkunu.',
            'Düşlerini mürekkebe dönüştüren bir hikâye avcısı.',
            'Kafasındaki karakterleri kâğıda döken bir öykü yazarı.',
            'Geceleri yıldızları, gündüzleri kelimeleri sayan biri.',
            'Her kitapta yeni bir evren keşfeden meraklı bir okur-yazar.',
            'Şiirin en saf duygu aktarım aracı olduğunu savunan bir kalem.',
            'Bir fincan kahve, bir kalem ve sonsuz hayal gücü.',
            'Sokakların, insanların ve duyguların yazarı.',
            'Anadolu topraklarının hikâyelerini anlatan bir kalem erbabı.',
            'Felsefe ve edebiyatı harmanlayan bir düşünür-yazar.',
            'Doğanın sesini kelimelere çeviren bir çevirmen.',
            'Her satırda bir parça kalbini bırakan bir şair.',
            'Okumayı nefes almak kadar doğal bulan biri.',
            'Dünya edebiyatını Türkçeye sevdalı bir gözle okuyan.',
            'Masallardan gerçeğe uzanan bir köprü kurucusu.',
            'Yazarken zamanın durduğuna inanan bir romantik.',
            'Kalemin kılıçtan keskin olduğuna iman eden bir idealist.',
            'Her gün yeni bir hikâye keşfeden meraklı bir gezgin.',
            'Edebiyatın karanlıkta bir fener olduğuna inanan.',
            'Sessizliğin dilini çözmeye çalışan bir kelime ustası.',
            'Bir damla mürekkeple okyanuslar yaratmayı hayal eden.',
            'Türk edebiyatının geleceğine umutla bakan genç bir kalem.',
            'Klasik edebiyat sevdalısı, modern anlatım meraklısı.',
            'Duyguların en yalın halini arayan bir şiir emekçisi.',
            'Kitaplar arasında kaybolmayı seven bir edebiyat kurdu.',
            'Hikâyelerin dünyayı değiştirebileceğine inanan bir iyimser.',
        ];

        $abouts = [
            'Küçük yaşlardan itibaren edebiyata ilgi duydum. İlk şiirimi 12 yaşında yazdım ve o günden beri kalemimi bırakmadım. Üniversitede Türk Dili ve Edebiyatı okudum, ardından çeşitli edebiyat dergilerinde yazılarım yayımlandı. Şiir, deneme ve öykü türlerinde eserler veriyorum.',
            'Yazma serüvenim lise yıllarında başladı. Okul gazetesinde yazdığım yazılar ilgi görünce bu yolda devam etmeye karar verdim. Çeşitli yarışmalarda dereceler aldım. Şu an serbest yazar olarak çalışıyor, edebiyat atölyelerine katılıyorum.',
            'Profesyonel hayatımın yanında edebiyatı ikinci bir yaşam alanı olarak görüyorum. Özellikle deneme ve gezi yazısı türlerinde yazmayı seviyorum. Yılda en az 50 kitap okumayı kendime hedef koydum ve bu hedefi genellikle aşıyorum.',
            'Edebiyat benim için sadece bir hobi değil, bir yaşam biçimi. Her gün en az bir saat yazmaya ayırıyorum. Türk ve dünya edebiyatını takip ediyor, yeni yazarları keşfetmekten büyük keyif alıyorum.',
            'Gazetecilik geçmişim bana kelimelerin gücünü öğretti. Yıllarca haber yazdıktan sonra edebiyata yöneldim. Öykü ve roman türlerinde çalışıyorum. İlk romanım yakında yayımlanacak.',
            'Felsefe mezunuyum. Düşüncelerin yazıya dökülmesi beni her zaman büyülemiştir. Deneme ve eleştiri yazıyorum. Özellikle varoluşçuluk ve Doğu felsefesi ilgi alanlarım arasında.',
            'Çocuk edebiyatı ve masal yazarlığı yapıyorum. Çocukların hayal dünyasına hitap eden, eğitici ve eğlenceli metinler yazmak en büyük tutkum. İki çocuk kitabım yayımlandı.',
            'Müzik ve edebiyatı bir arada seven biriyim. Şarkı sözleri de yazıyorum. Şiirlerimde ritim ve melodi önemli bir yer tutar. Aynı zamanda akustik gitar çalıyorum.',
        ];

        $interests = [
            ['Şiir', 'Roman', 'Felsefe'],
            ['Öykü', 'Sinema', 'Tiyatro'],
            ['Deneme', 'Gezi', 'Fotoğrafçılık'],
            ['Edebiyat', 'Müzik', 'Resim'],
            ['Tarih', 'Kültür', 'Arkeoloji'],
            ['Bilim', 'Teknoloji', 'Fütürizm'],
            ['Doğa', 'Çevre', 'Sürdürülebilirlik'],
            ['Psikoloji', 'İnsan', 'Toplum'],
            ['Sanat', 'Tasarım', 'Mimari'],
            ['Mitoloji', 'Masal', 'Fantastik'],
            ['Gastronomi', 'Kültür', 'Seyahat'],
            ['Spor', 'Sağlık', 'Doğa Yürüyüşü'],
            ['Sinema', 'Dizi', 'Senaryo'],
            ['Klasik Müzik', 'Caz', 'Edebiyat'],
            ['Yoga', 'Meditasyon', 'Felsefe'],
        ];

        $websites = [
            'https://medium.com/@{username}',
            'https://{username}.wordpress.com',
            'https://www.blogger.com/{username}',
            'https://{username}.blogspot.com',
            '',
            '',
        ];

        $users = [];
        $usedEmails = [];
        $usedUsernames = [];
        $now = now()->format('Y-m-d H:i:s');

        for ($i = 0; $i < 200; $i++) {
            $isFemale = $i % 2 === 0;
            $gender = $isFemale ? 'female' : 'male';
            $firstName = $isFemale
                ? $femaleNames[array_rand($femaleNames)]
                : $maleNames[array_rand($maleNames)];
            $surname = $surnames[array_rand($surnames)];
            $fullName = $firstName . ' ' . $surname;

            // Unique username
            $baseUsername = Str::slug($firstName . '-' . $surname, '');
            $username = $baseUsername;
            $counter = 1;
            while (in_array($username, $usedUsernames, true)) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            $usedUsernames[] = $username;

            // Unique email
            $baseEmail = Str::slug($firstName . '.' . $surname, '.') . '@';
            $domains = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'yandex.com'];
            $email = $baseEmail . $domains[array_rand($domains)];
            $counter = 1;
            while (in_array($email, $usedEmails, true)) {
                $email = $baseEmail . $counter . '@' . $domains[array_rand($domains)];
                $counter++;
            }
            $usedEmails[] = $email;

            // City & district
            $cityNames = array_keys($cities);
            $city = $cityNames[array_rand($cityNames)];
            $district = $cities[$city][array_rand($cities[$city])];
            $location = $district . ', ' . $city;

            // Website
            $websiteTemplate = $websites[array_rand($websites)];
            $website = $websiteTemplate ? str_replace('{username}', $username, $websiteTemplate) : null;

            // Social media
            $instagram = rand(0, 100) > 20 ? $username : null;
            $twitter = rand(0, 100) > 30 ? $username : null;
            $youtube = rand(0, 100) > 70 ? '@' . $username : null;
            $tiktok = rand(0, 100) > 60 ? $username : null;
            $spotify = rand(0, 100) > 80 ? 'https://open.spotify.com/user/' . $username : null;

            // Birthdate (1970-2004)
            $year = rand(1970, 2004);
            $month = rand(1, 12);
            $day = rand(1, 28);
            $birthdate = sprintf('%04d-%02d-%02d', $year, $month, $day);

            // Registration date (last 30 days)
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $users[] = [
                'name'              => $fullName,
                'username'          => $username,
                'email'             => $email,
                'password'          => $hashedPassword,
                'role_id'           => $yazarRole->id,
                'bio'               => $bios[array_rand($bios)],
                'about'             => $abouts[array_rand($abouts)],
                'location'          => $location,
                'website'           => $website,
                'birthdate'         => $birthdate,
                'gender'            => $gender,
                'instagram'         => $instagram,
                'twitter'           => $twitter,
                'youtube'           => $youtube,
                'tiktok'            => $tiktok,
                'spotify'           => $spotify,
                'interests'         => json_encode($interests[array_rand($interests)]),
                'is_public'         => true,
                'show_email'        => (bool) rand(0, 1),
                'show_last_seen'    => true,
                'allow_messages'    => true,
                'email_verified_at' => $now,
                'created_at'        => $createdAt->format('Y-m-d H:i:s'),
                'updated_at'        => $createdAt->format('Y-m-d H:i:s'),
            ];
        }

        // Bulk insert in chunks of 50
        DB::transaction(function () use ($users): void {
            foreach (array_chunk($users, 50) as $chunk) {
                User::insert($chunk);
            }
        });

        $this->command->info('200 demo yazar başarıyla oluşturuldu.');
    }
}
