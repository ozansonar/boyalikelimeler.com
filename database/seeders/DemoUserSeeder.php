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

class DemoUserSeeder extends Seeder
{
    private const int USER_COUNT = 500;

    public function run(): void
    {
        $kullaniciRole = Role::where('slug', RoleSlug::Kullanici->value)->firstOrFail();
        $hashedPassword = Hash::make('Demo*12345.');

        $maleNames = [
            'Ahmet', 'Mehmet', 'Mustafa', 'Ali', 'Hüseyin', 'Hasan', 'İbrahim', 'İsmail',
            'Yusuf', 'Osman', 'Murat', 'Ömer', 'Halil', 'Süleyman', 'Abdullah', 'Mahmut',
            'Recep', 'Salih', 'Fatih', 'Kadir', 'Emre', 'Burak', 'Serkan', 'Cem', 'Deniz',
            'Onur', 'Tolga', 'Barış', 'Kerem', 'Arda', 'Kaan', 'Berk', 'Efe', 'Eren',
            'Furkan', 'Gökhan', 'Uğur', 'Volkan', 'Erdem', 'Sinan', 'Selim', 'Taner',
            'Alp', 'Doruk', 'Yiğit', 'Koray', 'Cenk', 'Taylan', 'Ozan', 'Tuğrul',
            'Engin', 'Cihan', 'Batuhan', 'Umut', 'Tarık', 'Levent', 'Serdar', 'Adem',
            'Caner', 'Berkay', 'Alperen', 'Doğukan', 'Emir', 'Kutay', 'Polat', 'Rüzgar',
            'Sarp', 'Atakan', 'Baran', 'Çağlar', 'Derin', 'Göktürk', 'Harun', 'İlker',
        ];

        $femaleNames = [
            'Fatma', 'Ayşe', 'Emine', 'Hatice', 'Zeynep', 'Elif', 'Merve', 'Büşra',
            'Zehra', 'Esra', 'Nur', 'Seda', 'Özge', 'Gizem', 'Derya', 'Pınar',
            'Sibel', 'Aslı', 'Ceren', 'İrem', 'Ebru', 'Dilek', 'Hülya', 'Sevgi',
            'Gamze', 'Burcu', 'Tuğba', 'Başak', 'Damla', 'Cansu', 'Ece', 'Defne',
            'Melis', 'Naz', 'Buse', 'Yaren', 'Selin', 'Nehir', 'Ada', 'Lale',
            'Gül', 'Nilüfer', 'Sinem', 'Dilan', 'Cemre', 'Berna', 'Ilgın', 'Azra',
            'Simge', 'Ülkü', 'Melisa', 'Deniz', 'Yağmur', 'Aylin', 'Beril', 'Ceyda',
            'Duygu', 'Eylül', 'Fulya', 'Gökçe', 'Hazal', 'İpek', 'Kumsal', 'Miray',
            'Nisan', 'Özlem', 'Pelin', 'Rana', 'Serra', 'Tuba', 'Vildan', 'Zülal',
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
            'Aras', 'Bayram', 'Cengiz', 'Dinç', 'Eroğlu', 'Fırat', 'Gök', 'Hancı',
            'İnan', 'Kahraman', 'Lale', 'Mert', 'Nalçacı', 'Okur', 'Parlak', 'Reis',
        ];

        $cities = [
            'İstanbul' => ['Kadıköy', 'Beşiktaş', 'Üsküdar', 'Bakırköy', 'Beyoğlu', 'Şişli', 'Fatih', 'Maltepe', 'Ataşehir', 'Sarıyer', 'Pendik', 'Kartal', 'Tuzla', 'Beykoz', 'Çekmeköy'],
            'Ankara' => ['Çankaya', 'Keçiören', 'Mamak', 'Yenimahalle', 'Etimesgut', 'Sincan', 'Altındağ', 'Pursaklar', 'Gölbaşı'],
            'İzmir' => ['Konak', 'Karşıyaka', 'Bornova', 'Buca', 'Bayraklı', 'Çiğli', 'Urla', 'Çeşme', 'Menemen'],
            'Bursa' => ['Osmangazi', 'Nilüfer', 'Yıldırım', 'Mudanya', 'Gemlik', 'İnegöl'],
            'Antalya' => ['Muratpaşa', 'Konyaaltı', 'Kepez', 'Alanya', 'Manavgat', 'Kaş'],
            'Eskişehir' => ['Odunpazarı', 'Tepebaşı'],
            'Trabzon' => ['Ortahisar', 'Akçaabat', 'Yomra', 'Of'],
            'Konya' => ['Selçuklu', 'Meram', 'Karatay'],
            'Gaziantep' => ['Şahinbey', 'Şehitkâmil', 'Oğuzeli'],
            'Adana' => ['Seyhan', 'Çukurova', 'Yüreğir', 'Sarıçam'],
            'Samsun' => ['Atakum', 'İlkadım', 'Canik'],
            'Kayseri' => ['Melikgazi', 'Kocasinan', 'Talas'],
            'Mersin' => ['Mezitli', 'Yenişehir', 'Akdeniz', 'Toroslar'],
            'Diyarbakır' => ['Bağlar', 'Kayapınar', 'Yenişehir', 'Sur'],
            'Muğla' => ['Bodrum', 'Fethiye', 'Marmaris', 'Dalaman', 'Datça'],
            'Denizli' => ['Merkezefendi', 'Pamukkale'],
            'Edirne' => ['Merkez', 'Keşan', 'Uzunköprü'],
            'Çanakkale' => ['Merkez', 'Biga', 'Gelibolu'],
            'Hatay' => ['Antakya', 'İskenderun', 'Defne'],
            'Mardin' => ['Artuklu', 'Kızıltepe', 'Midyat'],
            'Sakarya' => ['Serdivan', 'Adapazarı', 'Erenler'],
            'Balıkesir' => ['Altıeylül', 'Karesi', 'Bandırma', 'Edremit'],
            'Malatya' => ['Battalgazi', 'Yeşilyurt'],
            'Erzurum' => ['Yakutiye', 'Palandöken', 'Aziziye'],
            'Van' => ['İpekyolu', 'Tuşba', 'Edremit'],
        ];

        $bios = [
            'Edebiyat ve sanat meraklısı bir okur.',
            'Kitap kurdu, müzik dinleyicisi, doğa aşığı.',
            'Hayatı keşfetmeyi seven meraklı bir ruh.',
            'Kahve tutkunu, film izleyicisi, kültür gezgini.',
            'Şiir ve öykü okumayı seven bir edebiyat dostu.',
            'Seyahat etmeyi ve yeni yerler keşfetmeyi seven biri.',
            'Sanatın her dalıyla ilgilenen kültürlü bir birey.',
            'Klasik Türk edebiyatı hayranı.',
            'Modern edebiyatı takip eden bir okur.',
            'Fotoğrafçılık ve yazı ile ilgilenen bir sanat sever.',
            'Sessiz bir okur, düşünceli bir dinleyici.',
            'Müzik ve edebiyat arasında köprü kuran bir ruh.',
            'Tarih ve kültür meraklısı.',
            'Doğa yürüyüşleri ve kitaplar — iki büyük tutkum.',
            'Her gün yeni bir şey öğrenmeye çalışan biri.',
            'Sinema ve edebiyat birbirinden ayrılmaz iki sanat.',
            'Güzel söz biriktiren, güzel insan arayan.',
            'Felsefe, psikoloji ve edebiyat üçgeninde dolaşan biri.',
            'Boş zamanlarımda yazı yazıyor, bol bol okuyorum.',
            'Hayallerini gerçeğe dönüştürmeye çalışan bir iyimser.',
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
            ['Roman', 'Polisiye', 'Gerilim'],
            ['Futbol', 'Basketbol', 'Spor'],
            ['Astronomi', 'Uzay', 'Bilim Kurgu'],
            ['Dijital Sanat', 'Grafik', 'Animasyon'],
            ['Bahçecilik', 'Doğa', 'Bitki Bakımı'],
        ];

        $users = [];
        $usedEmails = User::pluck('email')->toArray();
        $usedUsernames = User::pluck('username')->toArray();
        $now = now()->format('Y-m-d H:i:s');
        $domains = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'yandex.com', 'icloud.com'];

        for ($i = 0; $i < self::USER_COUNT; $i++) {
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
            $baseEmail = Str::slug($firstName . '.' . $surname, '.');
            $email = $baseEmail . '@' . $domains[array_rand($domains)];
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

            // Social media (lower probability than writers)
            $instagram = rand(0, 100) > 50 ? $username : null;
            $twitter = rand(0, 100) > 60 ? $username : null;
            $youtube = rand(0, 100) > 85 ? '@' . $username : null;
            $tiktok = rand(0, 100) > 75 ? $username : null;
            $spotify = rand(0, 100) > 90 ? 'https://open.spotify.com/user/' . $username : null;

            // Birthdate (1965-2007)
            $year = rand(1965, 2007);
            $month = rand(1, 12);
            $day = rand(1, 28);
            $birthdate = sprintf('%04d-%02d-%02d', $year, $month, $day);

            // Registration date (last 90 days)
            $createdAt = now()->subDays(rand(0, 90))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // %15 unverified
            $emailVerifiedAt = rand(1, 100) <= 85 ? $now : null;

            $users[] = [
                'name'              => $fullName,
                'username'          => $username,
                'email'             => $email,
                'password'          => $hashedPassword,
                'role_id'           => $kullaniciRole->id,
                'bio'               => rand(0, 100) > 30 ? $bios[array_rand($bios)] : null,
                'about'             => null,
                'location'          => rand(0, 100) > 20 ? $location : null,
                'website'           => null,
                'birthdate'         => rand(0, 100) > 25 ? $birthdate : null,
                'gender'            => $gender,
                'instagram'         => $instagram,
                'twitter'           => $twitter,
                'youtube'           => $youtube,
                'tiktok'            => $tiktok,
                'spotify'           => $spotify,
                'interests'         => rand(0, 100) > 40 ? json_encode($interests[array_rand($interests)]) : null,
                'is_public'         => (bool) rand(0, 1),
                'show_email'        => (bool) rand(0, 1),
                'show_last_seen'    => (bool) rand(0, 1),
                'allow_messages'    => rand(0, 100) > 20,
                'email_verified_at' => $emailVerifiedAt,
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

        $this->command->info(self::USER_COUNT . ' demo kullanıcı başarıyla oluşturuldu.');
    }
}
