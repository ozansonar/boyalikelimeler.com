<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\QnaStatus;
use App\Enums\RoleSlug;
use App\Models\QnaCategory;
use App\Models\QnaQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QnaQuestionSeeder extends Seeder
{
    /**
     * @var array<string, list<array{title: string, body: string}>>
     */
    private array $questions = [
        'Edebiyat' => [
            [
                'title' => 'Türk edebiyatında en etkili roman hangisidir sizce',
                'body'  => 'Cumhuriyet dönemi Türk edebiyatında pek çok önemli roman yazıldı. Yaşar Kemal\'in İnce Memed\'i mi, Oğuz Atay\'ın Tutunamayanlar\'ı mı yoksa başka bir eser mi sizin için en etkili romandır? Nedenini de açıklarsanız sevinirim.',
            ],
            [
                'title' => 'Şiir okuma alışkanlığı nasıl kazanılır',
                'body'  => 'Şiire ilgi duymaya başladım ama nereden başlayacağımı bilmiyorum. Hangi şairlerle başlamalıyım? Günlük şiir okuma alışkanlığı edinmek için önerileriniz nelerdir? Özellikle modern Türk şiiri hakkında tavsiyelerinizi bekliyorum.',
            ],
        ],
        'Felsefe' => [
            [
                'title' => 'Varoluşçuluk günlük hayatımızı nasıl etkiler',
                'body'  => 'Sartre ve Camus gibi varoluşçu filozofların düşüncelerini okuyorum. Varoluşçuluk felsefesinin günlük yaşamda pratik karşılığı nedir? Kararlarımızı ve yaşam biçimimizi gerçekten etkiler mi? Deneyimlerinizi paylaşır mısınız?',
            ],
            [
                'title' => 'Stoacılık modern çağda hâlâ geçerli mi',
                'body'  => 'Son zamanlarda stoacılık tekrar popüler olmaya başladı. Marcus Aurelius\'un Düşünceler kitabını okudum ve çok etkilendim. Sizce stoacı prensipler günümüzde uygulanabilir mi? Hangi stoacı alışkanlıkları hayatınıza dahil ettiniz?',
            ],
        ],
        'Tarih' => [
            [
                'title' => 'Osmanlı İmparatorluğunun en parlak dönemi hangisidir',
                'body'  => 'Osmanlı tarihinde Kanuni Sultan Süleyman dönemi genellikle en parlak dönem olarak gösterilir. Ancak bazı tarihçiler Fatih Sultan Mehmet ya da II. Bayezid dönemini öne çıkarır. Sizce Osmanlı\'nın altın çağı tam olarak hangi dönemdir ve neden?',
            ],
            [
                'title' => 'Kurtuluş Savaşının en kritik dönüm noktası neydi',
                'body'  => 'Kurtuluş Savaşı\'nda Sakarya Meydan Muharebesi, Büyük Taarruz, İnönü Muharebeleri gibi birçok kritik an yaşandı. Sizce savaşın kaderini belirleyen en önemli dönüm noktası hangisiydi? Tarihî kaynaklarla desteklerseniz çok sevinirim.',
            ],
        ],
        'Bilim' => [
            [
                'title' => 'Yapay zekâ bilimsel araştırmaları nasıl dönüştürecek',
                'body'  => 'Son yıllarda yapay zekâ bilimsel keşiflerde giderek daha fazla kullanılıyor. Protein katlanmasından ilaç keşfine kadar birçok alanda devrim yaratıyor. Sizce önümüzdeki 10 yılda yapay zekâ bilimi nasıl dönüştürecek? Hangi alanlarda en büyük etki bekliyorsunuz?',
            ],
            [
                'title' => 'Kuantum bilgisayarlar gerçekten her şeyi değiştirecek mi',
                'body'  => 'Kuantum bilgisayarlar hakkında çok iddialı haberler okuyoruz ama gerçek durumu anlamak güç. Kuantum üstünlüğü gerçekten yakın mı? Günlük hayatımızı etkilemesi ne kadar sürer? Bu konuda bilgili arkadaşların görüşlerini merak ediyorum.',
            ],
        ],
        'Sanat' => [
            [
                'title' => 'Dijital sanat geleneksel sanatın yerini alabilir mi',
                'body'  => 'NFT\'ler ve dijital illüstrasyon araçlarıyla sanat dünyası büyük bir dönüşüm geçiriyor. Dijital sanat geleneksel resim ve heykel sanatının yerini alabilir mi? Yoksa ikisi ayrı birer alan olarak devam mı edecek? Sanatçıların ve sanatseverlerin fikirlerini merak ediyorum.',
            ],
            [
                'title' => 'Müzik eğitimine başlamak için en uygun yaş kaçtır',
                'body'  => 'Çocuğuma müzik eğitimi aldırmak istiyorum ama hangi yaşta başlamanın en verimli olacağını bilmiyorum. Piyano ya da keman gibi enstrümanlar için ideal başlangıç yaşı nedir? Yetişkin olarak başlayanlar da başarılı olabiliyor mu?',
            ],
        ],
        'Psikoloji' => [
            [
                'title' => 'Prokrastinasyonun psikolojik sebepleri nelerdir',
                'body'  => 'Sürekli işleri erteleme problemi yaşıyorum ve bunun tembellikten ibaret olmadığını düşünüyorum. Prokrastinasyonun altında yatan psikolojik nedenler nelerdir? Bilimsel araştırmalara dayalı etkili çözüm yöntemleri önerebilir misiniz?',
            ],
            [
                'title' => 'Sosyal medya ruh sağlığımızı nasıl etkiliyor',
                'body'  => 'Günde ortalama 3-4 saat sosyal medya kullanıyorum ve bunun beni olumsuz etkilediğini hissediyorum. Karşılaştırma tuzağı, FOMO gibi kavramları duyuyorum. Sosyal medyanın ruh sağlığı üzerindeki etkileri hakkında neler biliyorsunuz? Sağlıklı kullanım için önerileriniz neler?',
            ],
        ],
        'Teknoloji' => [
            [
                'title' => 'Yazılım öğrenmek isteyenlere ilk dil olarak ne önerirsiniz',
                'body'  => 'Sıfırdan programlama öğrenmek istiyorum ama hangi dille başlayacağıma karar veremiyorum. Python, JavaScript, C# gibi pek çok seçenek var. İlk programlama dili olarak hangisini önerirsiniz ve neden? Öğrenme kaynağı önerileriniz de olursa çok sevinirim.',
            ],
            [
                'title' => 'Açık kaynak projelere nasıl katkıda bulunabilirim',
                'body'  => 'GitHub\'da açık kaynak projelere katkıda bulunmak istiyorum ama nereden başlayacağımı bilmiyorum. Junior seviyede biri hangi projelerle başlamalı? İlk PR\'ımı nasıl atabilirim? Deneyimli geliştiricilerin tavsiyelerini bekliyorum.',
            ],
        ],
        'Dil Bilgisi' => [
            [
                'title' => 'Türkçede en çok yapılan yazım hataları nelerdir',
                'body'  => 'Günlük yazışmalarda ve sosyal medyada sıkça yazım hatası yapıldığını görüyorum. "de/da" bitişik-ayrı yazımı, "ki" bağlacı, büyük-küçük harf kullanımı gibi konularda en yaygın hatalar nelerdir? Bunları düzeltmek için pratik ipuçları var mı?',
            ],
            [
                'title' => 'Yabancı kelimelerin Türkçe karşılıkları neden tutmuyor',
                'body'  => 'TDK birçok yabancı kelimeye Türkçe karşılık üretiyor ama "bilgisayar" gibi başarılı örneklerin yanında tutmayan karşılıklar da çok. Sizce yabancı kelimelere Türkçe karşılık bulma çabası ne kadar gerekli? Dil doğal evrimine mi bırakılmalı?',
            ],
        ],
        'Mitoloji' => [
            [
                'title' => 'Türk mitolojisinde en ilginç yaratık hangisidir',
                'body'  => 'Türk mitolojisi Yunan ve İskandinav mitolojileri kadar bilinmiyor maalesef. Anka kuşu, Tepegöz, Al Karısı gibi ilginç yaratıklar var. Sizce Türk mitolojisindeki en ilginç ve en az bilinen yaratık hangisi? Hikâyesini de paylaşır mısınız?',
            ],
            [
                'title' => 'Yunan ve Roma mitolojisi arasındaki temel farklar nelerdir',
                'body'  => 'Romalılar Yunan tanrılarını benimsedi ama isimleri ve bazı özellikleri değişti. Zeus-Jupiter, Ares-Mars gibi karşılıklar biliniyor ama daha derin farklılıklar da var mı? İki mitoloji arasındaki temel felsefik ve kültürel farkları merak ediyorum.',
            ],
        ],
        'Günlük Yaşam' => [
            [
                'title' => 'Sabah rutini oluşturmak gerçekten hayatı değiştirir mi',
                'body'  => 'Her yerde sabah rutininin önemi anlatılıyor ama benim için sabahları erken kalkmak işkence. Sabah rutini oluşturan ve bunu alışkanlık haline getiren var mı? Gerçekten verimliliğinizi artırdı mı? Pratik ve uygulanabilir bir sabah rutini nasıl olmalı?',
            ],
            [
                'title' => 'Evde çalışırken verimli olmanın sırrı nedir',
                'body'  => 'Uzaktan çalışmaya geçtim ama evde konsantre olmakta zorlanıyorum. Ev ortamında verimli çalışmak için ne tür düzenlemeler yapmalıyım? Pomodoro tekniği gibi yöntemler işe yarıyor mu? Evden çalışanların deneyimlerini ve önerilerini dinlemek isterim.',
            ],
        ],
    ];

    public function run(): void
    {
        $categories = QnaCategory::all()->keyBy('name');
        $memberUserIds = User::whereHas('role', function ($query): void {
            $query->where('slug', RoleSlug::Kullanici->value);
        })->pluck('id')->toArray();

        if (empty($memberUserIds)) {
            $memberUserIds = User::pluck('id')->toArray();
        }

        foreach ($this->questions as $categoryName => $questions) {
            $category = $categories->get($categoryName);

            if ($category === null) {
                continue;
            }

            foreach ($questions as $questionData) {
                $userId = $memberUserIds[array_rand($memberUserIds)];
                $createdAt = now()->subDays(rand(1, 45))->subHours(rand(0, 23));

                QnaQuestion::factory()->create([
                    'user_id'         => $userId,
                    'qna_category_id' => $category->id,
                    'title'           => $questionData['title'],
                    'slug'            => Str::slug($questionData['title']),
                    'body'            => $questionData['body'],
                    'status'          => QnaStatus::Approved,
                    'view_count'      => rand(10, 300),
                    'like_count'      => rand(0, 25),
                    'answer_count'    => 0,
                    'created_at'      => $createdAt,
                    'updated_at'      => $createdAt,
                ]);
            }
        }
    }
}
