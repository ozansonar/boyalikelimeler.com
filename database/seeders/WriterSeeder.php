<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\RoleSlug;
use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WriterSeeder extends Seeder
{
    public function run(): void
    {
        $yazarRole = Role::where('slug', RoleSlug::Yazar->value)->firstOrFail();
        $categories = Category::pluck('id', 'slug');

        $writers = [
            [
                'name'     => 'Elif Yıldırım',
                'username' => 'yazar1',
                'email'    => 'yazar1@boyalikalemler.com',
                'bio'      => 'Kelimelerle resim çizen bir ruh. Şiir ve deneme yazarı.',
            ],
            [
                'name'     => 'Kaan Demir',
                'username' => 'yazar2',
                'email'    => 'yazar2@boyalikalemler.com',
                'bio'      => 'Hikâyelerin peşinde koşan bir kalem. Öykü ve roman tutkunu.',
            ],
            [
                'name'     => 'Zeynep Aksoy',
                'username' => 'yazar3',
                'email'    => 'yazar3@boyalikalemler.com',
                'bio'      => 'Sanatın her dalında iz bırakan bir yazar. Eleştiri ve inceleme.',
            ],
            [
                'name'     => 'Burak Şen',
                'username' => 'yazar4',
                'email'    => 'yazar4@boyalikalemler.com',
                'bio'      => 'Düşüncelerini kelimelere döken bir gezgin. Gezi ve deneme yazarı.',
            ],
            [
                'name'     => 'Ayşe Korkmaz',
                'username' => 'yazar5',
                'email'    => 'yazar5@boyalikalemler.com',
                'bio'      => 'Edebiyatın büyülü dünyasında kaybolanlardan. Şiir ve çeviri.',
            ],
        ];

        $allPosts = $this->getPostsData();

        foreach ($writers as $index => $writerData) {
            $user = User::updateOrCreate(
                ['email' => $writerData['email']],
                [
                    'name'              => $writerData['name'],
                    'username'          => $writerData['username'],
                    'email'             => $writerData['email'],
                    'password'          => 'Demo*12345.',
                    'bio'               => $writerData['bio'],
                    'role_id'           => $yazarRole->id,
                    'email_verified_at' => now(),
                ],
            );

            $writerPosts = $allPosts[$index];

            foreach ($writerPosts as $postOrder => $postData) {
                $categoryId = $categories[$postData['category_slug']] ?? $categories->first();
                unset($postData['category_slug']);

                Post::updateOrCreate(
                    ['slug' => $postData['slug']],
                    array_merge($postData, [
                        'category_id'    => $categoryId,
                        'user_id'        => $user->id,
                        'allow_comments' => true,
                        'sort_order'     => $postOrder,
                        'view_count'     => rand(30, 800),
                        'published_at'   => now()->subDays(rand(1, 90)),
                    ]),
                );
            }
        }
    }

    /**
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function getPostsData(): array
    {
        return [
            // ─── Yazar 1: Elif Yıldırım ─────────────────────────────
            [
                [
                    'title'            => 'Ay Işığında Bir Mektup',
                    'slug'             => 'ay-isiginda-bir-mektup',
                    'excerpt'          => 'Geceye yazılmış bir mektup, yıldızlara gönderilmiş bir dua.',
                    'body'             => '<p>Sevgili Gece,</p><p>Sana yazıyorum yine, herkes uyurken. Şehir sessizleşti, sokak lambaları titriyor rüzgârda. Penceremden ay\'ı izliyorum — yarım, tıpkı benim gibi.</p><p>Biliyor musun, insanlar gündüzleri maske takıyor. Gülümsüyorlar ama gözleri hüzünlü, konuşuyorlar ama söyledikleri boş. Sadece gece, sadece senin kollarında gerçek olabiliyoruz.</p><p>Bir şiir yazacaktım sana ama kelimeler yetmedi. Nasıl anlatayım, ay ışığının cildime değdiğinde hissettiğim o tuhaf huzuru? Nasıl tarif edeyim, gecenin koynunda kaybolmanın verdiği o garip cesareti?</p><p>Belki de en güzel şiirler yazılmayanlardır. Belki de en derin duygular, kelimelerin ötesinde yaşar. Sen bunu bilirsin, Gece. Çünkü sen, sessizliğin dilini konuşan tek varlıksın.</p><p>Yarın güneş doğduğunda bu mektup solacak. Ama biliyorum ki sen onu saklayacaksın, yıldızların arasında, bir sonraki geceye kadar.</p><p>Seni seven,<br>Bir gece kuşu.</p>',
                    'category_slug'    => 'siir',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Ay Işığında Bir Mektup — Şiir',
                    'meta_description' => 'Geceye yazılmış bir mektup, yıldızlara gönderilmiş bir dua.',
                ],
                [
                    'title'            => 'Kahve Falında Görünen Şehir',
                    'slug'             => 'kahve-falinda-gorunen-sehir',
                    'excerpt'          => 'Bir fincan kahvenin dibinde, kaybolmuş bir şehrin haritası belirdi.',
                    'body'             => '<p>İstanbul\'un arka sokaklarında, kimsenin bilmediği bir kahvehane var. Duvarları eski İstanbul fotoğraflarıyla kaplı, tavanda sararmış kâğıttan lambalar sallanıyor. Burada zamanın durduğunu hissedersiniz.</p><p>Fatma Teyze bu kahvehanenin tek falcısı. Yetmiş yaşında, gözleri çakmak çakmak, elleri titremiyor. Fincanınızı çevirdiğinizde sadece geleceği değil, geçmişi de okur.</p><p>«Senin fincanında bir şehir var,» dedi bana o gün. «Ama bu şehir haritada yok. Kalbinde yaşayan bir şehir bu.»</p><p>Dikkatle baktım fincanın dibine. Gerçekten de orada, kahve tellerinin arasında, bir şehrin silueti belirmişti. Kuleleri, köprüleri, dar sokakları — tanıdık ama yabancı bir yer.</p><p>«Herkesin içinde kayıp bir şehir vardır,» dedi Fatma Teyze, çayından bir yudum alarak. «Kimimiz onu ararız ömür boyu, kimimiz unuturuz. Ama o şehir her zaman oradadır, bizi bekler.»</p><p>O gün kahvehaneden çıktığımda İstanbul farklı görünüyordu. Her köşede kayıp şehrimin bir parçasını arıyordum. Ve belki de bu arayış, bir yazarın en güzel macerası.</p>',
                    'category_slug'    => 'deneme',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Kahve Falında Görünen Şehir — Deneme',
                    'meta_description' => 'Bir fincan kahvenin dibinde kaybolmuş bir şehrin haritası.',
                ],
                [
                    'title'            => 'Rüzgâra Bırakılan Kelimeler',
                    'slug'             => 'ruzgara-birakilan-kelimeler',
                    'excerpt'          => 'Bazı şiirler yazılmaz, rüzgâra fısıldanır.',
                    'body'             => '<p>Rüzgâr geldiğinde denizden,<br>Tuzlu kokular taşır saçlarıma.<br>Bir martı çığlık atar uzakta,<br>Ben sessizce dinlerim dünyayı.</p><p>Kelimeler uçuşur havada,<br>Her biri bir yaprak gibi dönerek.<br>Yakalamaya çalışırım birini,<br>Ama en güzelleri hep kaçar.</p><p>Annem derdi ki eskiden,<br>«Rüzgâra söylenen sözler geri gelmez.»<br>Ben de en güzel sözlerimi<br>Hep rüzgâra söyledim bu yüzden.</p><p>Çünkü geri gelmeyecek sözler,<br>En özgür olanlardır belki de.<br>Bir kuş gibi kanat çırpar gökyüzünde,<br>Ve bir başka yalnız kalbe konar sessizce.</p><p>Bu akşam da rüzgâr esiyor pencereden,<br>Ve ben yine fısıldıyorum ona.<br>Biliyorum ki bu kelimeler bir gün,<br>Sana ulaşacak — nerede olursan ol.</p>',
                    'category_slug'    => 'siir',
                    'status'           => PostStatus::Draft,
                    'is_featured'      => false,
                    'meta_title'       => 'Rüzgâra Bırakılan Kelimeler — Şiir',
                    'meta_description' => 'Bazı şiirler yazılmaz, rüzgâra fısıldanır ve özgür bırakılır.',
                ],
            ],

            // ─── Yazar 2: Kaan Demir ────────────────────────────────
            [
                [
                    'title'            => 'Terzi Baba\'nın Son Dikişi',
                    'slug'             => 'terzi-babanin-son-dikisi',
                    'excerpt'          => 'Elli yıllık terzi dükkânının kapanma gününde, iğne ve iplik son kez buluştu.',
                    'body'             => '<p>Dükkânın camındaki «Terzihan — 1974» yazısı, elli yılın yorgunluğunu taşıyordu. Hasan usta o sabah her zamankinden erken geldi. Anahtarı kilide soktuğunda eli titredi — son kez açıyordu bu kapıyı.</p><p>İçerisi kumaş kokuyordu, her zaman olduğu gibi. Raflar dolusu iplik masuraları, düğmeler, fermuarlar — her biri bir müşterinin, bir hikâyenin parçası. Şurada asılı duran ceket, emekli öğretmen Nazım Bey\'in bayramlık ceketiydi. Hiç almaya gelmemişti.</p><p>Hasan usta dikiş makinesinin başına oturdu. Bu makine babasından kalmıştı — 1974\'te, dükkân açıldığı gün, babası bu makineyle ilk dikişi atmıştı. «Düz dikiş at oğlum,» demişti. «Hayat gibi; eğri büğrü olmasın.»</p><p>Elli yıl boyunca düz dikiş attı Hasan usta. Gelinliklerin dantellerini işledi, askere giden oğulların pantolonlarını dikti, çocukların okul önlüklerini dar etti, geniş etti. Ellerinden geçen kumaşlar, bir şehrin tarihini anlatsaydı, ciltler dolusu kitap olurdu.</p><p>Son dikişi attığında saat öğleyi gösteriyordu. İğneyi kumaştan çekti, ipliği kesti, makineyi kapattı. Sonra ayağa kalkıp dükkânı son bir kez dolaştı.</p><p>Kapıyı kapatırken gözleri doldu. Ama gülümsüyordu — çünkü elli yıl boyunca hep düz dikiş atmıştı.</p>',
                    'category_slug'    => 'oyku',
                    'status'           => PostStatus::Published,
                    'is_featured'      => true,
                    'meta_title'       => 'Terzi Baba\'nın Son Dikişi — Öykü',
                    'meta_description' => 'Elli yıllık terzi dükkânının kapanma gününde bir ustanın vedası.',
                ],
                [
                    'title'            => 'Deniz Feneri Bekçisinin Günlüğü',
                    'slug'             => 'deniz-feneri-bekcisinin-gunlugu',
                    'excerpt'          => 'Dünyanın en yalnız mesleğini yapan adamın, en dolu kalbiyle yazdığı satırlar.',
                    'body'             => '<p><em>12 Kasım, Salı</em></p><p>Fırtına üçüncü gündür dinmiyor. Deniz kudurmuş gibi kayalıklara vuruyor. Fener bu gece de yanacak — her gece olduğu gibi. Otuz iki yıldır bu feneri yakmıyor muyum? Otuz iki yıldır bu ışık sayesinde kaç gemi kıyıya güvenle ulaştı, bilemem.</p><p><em>15 Kasım, Cuma</em></p><p>Bugün bir balıkçı teknesi geçti yakınımdan. İçindeki adam el salladı. Ben de salladım. İşte bu kadar — iki insanın denizin ortasında paylaştığı bir an. Ama o an, günümün en güzel anıydı.</p><p><em>20 Kasım, Çarşamba</em></p><p>Geceleri yıldızları sayıyorum. Dün 247\'ye kadar saydım, sonra uyuyakalmışım. Rüyamda bir şehirdeydim, kalabalık sokaklar, gürültü, boğucu sıcaklık. Uyandığımda denizin sesini duydum ve rahatladım. Yalnızlık herkesin korktuğu şey değil — bazen en büyük lüks.</p><p><em>25 Kasım, Pazartesi</em></p><p>Kış yaklaşıyor, kuşlar güneye göç ediyor. Onları izlemek güzel. Binlercesi, tek bir amaçla, tek bir yöne. İnsanlar da böyle olabilse — nereye gittiklerini, neden gittiklerini bilse.</p><p>Bu gece fener yine yanacak. Ve ben yine burada olacağım. Çünkü birinin bu ışığı yakması gerekiyor. Ve o biri, benim.</p>',
                    'category_slug'    => 'oyku',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Deniz Feneri Bekçisinin Günlüğü — Öykü',
                    'meta_description' => 'Dünyanın en yalnız mesleğinde bir adamın samimi günlük yazıları.',
                ],
                [
                    'title'            => 'Hikâye Anlatıcılığının Kayıp Sanatı',
                    'slug'             => 'hikaye-anlaticiligin-kayip-sanati',
                    'excerpt'          => 'Dedelerimiz ateş başında hikâye anlatırdı. Biz ise ekrana bakıyoruz.',
                    'body'             => '<p>Bir zamanlar — ki bütün güzel hikâyeler böyle başlar — insanlar ateş başında toplanır, birbirlerine hikâye anlatırlardı. Yaşlılar anlatırdı, gençler dinlerdi. Hikâyeler kuşaktan kuşağa aktarılır, her anlatımda biraz değişir, biraz zenginleşirdi.</p><p>Homeros\'un İlyada\'sı yüzyıllar boyunca sözlü olarak anlatıldı. Dede Korkut hikâyeleri Orta Asya\'nın bozkırlarından Anadolu\'ya, ağızdan ağıza taşındı. Binbir Gece Masalları\'nı Şehrazat her gece yeniden yarattı.</p><p>Peki şimdi ne oldu? Netflix açıyoruz, sosyal medya kaydırıyoruz, podcast dinliyoruz. Hikâye hâlâ hayatımızın merkezinde — ama anlatıcı kayboldu. Yerine algoritma geldi.</p><p>Oysa bir insanın gözlerine bakarak dinlediğiniz hikâye, ekrandan izlediğiniz hikâyeden bambaşkadır. Çünkü o hikâyenin içinde anlatıcının nefesi vardır, duraklamaları vardır, gözlerindeki ışık vardır.</p><p>Belki de dijital çağın en büyük kaybı, teknoloji değil, göz teması. Ve belki de en güzel direniş, birini karşınıza oturtup «Sana bir şey anlatacağım» demektir.</p>',
                    'category_slug'    => 'deneme',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Hikâye Anlatıcılığının Kayıp Sanatı — Deneme',
                    'meta_description' => 'Ateş başından ekrana: hikâye anlatıcılığının dönüşümü.',
                ],
            ],

            // ─── Yazar 3: Zeynep Aksoy ──────────────────────────────
            [
                [
                    'title'            => 'Frida Kahlo: Acının Renkli Portresi',
                    'slug'             => 'frida-kahlo-acinin-renkli-portresi',
                    'excerpt'          => 'Acıyı sanata dönüştüren kadın: Frida Kahlo\'nun hayatı ve eserleri.',
                    'body'             => '<p>Frida Kahlo\'nun hayatı, acı ve sanatın nasıl iç içe geçebileceğinin en çarpıcı örneğidir. Altı yaşında çocuk felci, on sekiz yaşında korkunç bir trafik kazası — bedeni paramparça oldu ama ruhu kırılmadı. Tam tersine, o kırık bedenden dünyanın en güçlü sanat eserlerinden bazıları doğdu.</p><p>Otoportreleri sadece kendi yüzünün resimleri değildir. Her biri bir manifesto, bir çığlık, bir itiraftır. «Kırık Sütun» tablosunda bedenini ikiye yarılmış olarak resmeder — içindeki kırık sütun, hem fiziksel acısını hem de ruhsal çatlaklarını simgeler.</p><p>Diego Rivera ile olan fırtınalı aşkı, Meksika devrimi, komünizm, feminizm — Frida\'nın sanatı tüm bu unsurları birleştiren bir mozaiktir. Ama en çarpıcı olanı, tüm bu acıya rağmen eserlerindeki renk cümbüşüdür. Kırmızılar, sarılar, yeşiller — sanki acıya inat hayatı kutluyordu.</p><p>Bugün Frida Kahlo sadece bir ressam değil, bir ikondur. Yüzü tişörtlerde, posterlerde, kahve kupalarında. Ama asıl mirası, «acı yaşanır ama ona teslim olunmaz» mesajıdır.</p><p>«Kanatlarım varsa neden yürüyeyim ki?» demişti Frida. İşte bu cümle, onun tüm sanatının özetidir.</p>',
                    'category_slug'    => 'sanat',
                    'status'           => PostStatus::Published,
                    'is_featured'      => true,
                    'meta_title'       => 'Frida Kahlo: Acının Renkli Portresi — Sanat',
                    'meta_description' => 'Acıyı sanata dönüştüren Frida Kahlo\'nun hayatı ve eserlerinin analizi.',
                ],
                [
                    'title'            => 'Minimalizm: Az Çoktur mu?',
                    'slug'             => 'minimalizm-az-coktur-mu',
                    'excerpt'          => 'Sanattan yaşam tarzına uzanan minimalizm akımı gerçekten bizi özgürleştiriyor mu?',
                    'body'             => '<p>«Az çoktur» — mimar Ludwig Mies van der Rohe\'nin bu ünlü sözü, minimalizmin manifestosu haline geldi. Ama bu felsefe gerçekten işe yarıyor mu?</p><p>Minimalizm sanatta 1960\'larda başladı. Donald Judd\'ın geometrik heykelleri, Agnes Martin\'in neredeyse boş tualleri, John Cage\'in sessizliği müziğe dahil edişi — bunların hepsi «fazlalıktan arınma» arayışıydı.</p><p>Bugün minimalizm bir yaşam tarzına dönüştü. Marie Kondo bize eşyalarımızla konuşmamızı öğretti. Tiny house hareketi büyük evlere meydan okudu. Dijital minimalizm sosyal medya bağımlılığına karşı çıktı.</p><p>Ama işte paradoks: Minimalizm bile bir endüstriye dönüştü. «Minimalist» etiketli ürünler genellikle daha pahalı. «Az»ın kendisi bir lükse dönüştü.</p><p>Belki de gerçek minimalizm bir estetik değil, bir bilinç halidir. Ne kadar az eşyaya sahip olduğunuzla değil, sahip olduklarınızın ne kadar anlamlı olduğuyla ilgilidir. Bir oda dolusu kitap, minimalist olmayabilir ama eğer her biri okunmuş ve sevilmişse, o oda bir minimalistin rüyasıdır.</p><p>Az her zaman çok mudur? Hayır. Ama doğru olan, her zaman yeterlidir.</p>',
                    'category_slug'    => 'deneme',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Minimalizm: Az Çoktur mu? — Deneme',
                    'meta_description' => 'Sanattan yaşam tarzına minimalizm akımının sorgulanması.',
                ],
                [
                    'title'            => 'Sokak Sanatının Sessiz Devrimi',
                    'slug'             => 'sokak-sanatinin-sessiz-devrimi',
                    'excerpt'          => 'Müze duvarlarından sokak duvarlarına taşan sanat, kimin için ve ne için?',
                    'body'             => '<p>Banksy bir gecede bir duvarı boyar ve ertesi sabah o duvar milyonlarca dolar eder. Ama asıl soru şu: Bir duvar resmi ne zaman «vandalizm» olmaktan çıkıp «sanat» oluyor?</p><p>Sokak sanatının tarihi, insanlık tarihi kadar eskidir. Mağara resimleri, tarihin ilk grafitileri değil miydi? Pompei\'nin duvarlarındaki yazılar, Roma\'nın ilk sokak sanatı değil miydi? İnsan, düz bir yüzey gördüğünde oraya bir şey çizme dürtüsünü her zaman hissetmiştir.</p><p>Modern sokak sanatı 1970\'lerde New York\'un metro vagonlarıyla başladı. TAKI 183, CORNBREAD gibi öncüler, isimlerini şehrin her yerine yazdı. Bu bir kimlik arayışıydı — «Ben buradayım» demenin en ilkel ve en güçlü yolu.</p><p>Bugün İstanbul\'un sokaklarında da bu devrim yaşanıyor. Kadıköy\'ün duvarları, Beyoğlu\'nun ara sokakları, Balat\'ın renkli cepheleri — hepsi birer açık hava galerisi. Yerel sanatçılar toplumsal mesajlarını, politik görüşlerini, şiirlerini duvarlara aktarıyor.</p><p>Sokak sanatı demokratik bir sanattır. Müze bileti gerektirmez, sanat eğitimi şart değildir. Sadece bir duvar, bir sprey ve söylenecek bir şey yeter. Ve belki de bu yüzden, tüm sanat formları arasında en dürüst olanıdır.</p>',
                    'category_slug'    => 'sanat',
                    'status'           => PostStatus::Archived,
                    'is_featured'      => false,
                    'meta_title'       => 'Sokak Sanatının Sessiz Devrimi — Sanat',
                    'meta_description' => 'Müze duvarlarından sokak duvarlarına taşan sanatın evrimi.',
                ],
            ],

            // ─── Yazar 4: Burak Şen ─────────────────────────────────
            [
                [
                    'title'            => 'Kapadokya\'da Bir Sabah',
                    'slug'             => 'kapadokya-da-bir-sabah',
                    'excerpt'          => 'Peri bacalarının gölgesinde, balonların dans ettiği bir sabah vakti.',
                    'body'             => '<p>Saat beşte uyandım. Dışarısı henüz karanlıktı ama uzaktan bir uğultu duyuluyordu — balonların yakıtları ısınıyordu. Mağara otelin küçük terasına çıktığımda, Göreme vadisi henüz uyuyordu.</p><p>İlk ışıklar doğudan süzüldüğünde, manzara bir tabloya dönüştü. Peri bacaları — binlerce yıllık erozyonun oyduğu bu tuhaf kayalar — şafağın pembe ışığında yavaş yavaş beliriyordu. Sanki toprak, gökyüzüne uzanmak için parmaklarını kaldırmıştı.</p><p>Sonra balonlar yükselmeye başladı. Önce biri, sonra beşi, sonra yirmisi, sonra sayamadığım kadar. Rengârenk, devasa, sessiz — gökyüzünde dans eden jellyfish\'ler gibi. Her biri bir sepet dolusu insanı taşıyordu, ama aşağıdan bakınca küçücük noktalar gibi görünüyorlardı.</p><p>Kapadokya\'yı özel kılan sadece manzarası değil. Bu topraklar binlerce yıllık tarihin, kültürlerin, inançların izini taşıyor. Hititler, Romalılar, Bizanslılar, Selçuklular — hepsi bu vadilerde yaşamış, bu kayalara sığınmış, bu toprağa hikâyelerini bırakmış.</p><p>Güneş tamamen doğduğunda, çayımı son yudumladım. Balonlar artık küçük noktalar olmuştu gökyüzünde. Ve ben, bu kadim toprağın üzerinde, zamanın dışında bir an yaşamıştım.</p>',
                    'category_slug'    => 'deneme',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Kapadokya\'da Bir Sabah — Deneme',
                    'meta_description' => 'Peri bacalarının gölgesinde balonların dans ettiği büyülü bir sabah.',
                ],
                [
                    'title'            => 'Suskunluğun Şiiri',
                    'slug'             => 'suskunlugun-siiri',
                    'excerpt'          => 'Bazen en güzel şiir, hiç söylenmeyendir.',
                    'body'             => '<p>Susuyorum,<br>Çünkü bazı şeyler kelimelerden büyük.<br>Denizin önünde durduğumda mesela,<br>Ne söyleyebilirim ki dalgalara?</p><p>Dağların zirvesinde,<br>Bulutların arasında bir kartal süzülürken,<br>Hangi kelime yetişebilir ona?<br>Hangi cümle o kadar yükseğe çıkabilir?</p><p>Sevdiğim insanın gözlerine baktığımda,<br>Dudaklarım kilitlenir bazen.<br>Çünkü «seni seviyorum» demek,<br>O bakışın yanında çok sıradan kalır.</p><p>Yağmur yağarken penceremin önünde,<br>Her damla bir nota çalar camdа.<br>Ben dinlerim sessizce,<br>Çünkü en güzel müzik, doğanın bestesidir.</p><p>Suskunluk korkaklık değildir her zaman,<br>Bazen en cesur ifadedir.<br>Çünkü susmayı bilmek,<br>Dinlemeyi bilmektir aslında.</p><p>Ve bu dünya, dinlenmeye muhtaç<br>Belki de konuşmaktan çok.</p>',
                    'category_slug'    => 'siir',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Suskunluğun Şiiri',
                    'meta_description' => 'Bazen en güzel şiir, hiç söylenmeyendir. Suskunluğun gücü üzerine.',
                ],
                [
                    'title'            => 'Kitap Kokusu Üzerine Bir Savunma',
                    'slug'             => 'kitap-kokusu-uzerine-bir-savunma',
                    'excerpt'          => 'E-kitaplar pratik olabilir ama basılı kitabın kokusunu hiçbir ekran veremez.',
                    'body'             => '<p>Bir sahafa girdiğinizde burnunuza çarpan o koku — eski kâğıdın, mürekkebin, zamanın karışımı — hiçbir parfümle kıyaslanamaz. Bu koku, bilginin fiziksel halidir.</p><p>Bilim insanları bu kokunun kaynağını açıkladı: Kâğıttaki lignin ve selülozun yıllar içinde parçalanmasıyla ortaya çıkan organik bileşikler. Vanilya, badem, çiçek notaları taşıyan bu kimyasal kokteyl, beynimizin ödül merkezini aktive ediyormuş. Yani kitap kokusu gerçekten mutluluk veriyor — bilimsel olarak kanıtlanmış.</p><p>E-kitapları seviyorum, pratik olduklarını kabul ediyorum. Tatile giderken bavula on kitap sığdırmak yerine bir tablet almak hayat kurtarıcı. Ama gecenin bir yarısı, yatakta, loş ışıkta bir kitabı elinize aldığınızda hissettiğiniz o ağırlık, o doku, o koku — bunu hiçbir ekran veremez.</p><p>Bir kitabı hediye ettiğinizde ilk sayfasına yazdığınız not, yıllar sonra o kitap raftan çekildiğinde yeniden canlanan bir hatıra olur. Kindle\'a yazdığınız dijital not, aynı etkiyi yapabilir mi?</p><p>Kitap kokusu nostaljiden ibaret değil. O koku, insanlığın bilgiyi koruma, aktarma ve paylaşma çabasının fiziksel kanıtıdır. Ve bu kanıtı elimizde tutmaya devam etmeliyiz.</p>',
                    'category_slug'    => 'edebiyat-dunyasi',
                    'status'           => PostStatus::Draft,
                    'is_featured'      => false,
                    'meta_title'       => 'Kitap Kokusu Üzerine Bir Savunma — Edebiyat',
                    'meta_description' => 'Basılı kitabın kokusunun e-kitapla kıyaslanamaz büyüsü üzerine.',
                ],
            ],

            // ─── Yazar 5: Ayşe Korkmaz ──────────────────────────────
            [
                [
                    'title'            => 'Nâzım\'ın Mavi Gözleri',
                    'slug'             => 'nazimin-mavi-gozleri',
                    'excerpt'          => 'Nâzım Hikmet\'in şiirlerindeki mavi renk, özgürlüğün ve hasretin simgesidir.',
                    'body'             => '<p>Nâzım Hikmet\'in şiirlerinde mavi renk, sıradan bir renk değildir. Mavi, onun için özgürlüktür — hapishane duvarlarının ardında özlediği gökyüzü, deniz, Boğaz\'ın suları.</p><p>«Mavi Liman» şiirinde mavi, ulaşılmak istenen ama bir türlü varılamayan yerdir. «Davet» şiirinde ise mavi gözlü dev, halkın gücünün simgesidir — güçlü, kararlı, durdurulamaz.</p><p>On yıllarca hapis yatan bir adam için renklerin anlamı farklılaşır. Gri duvarlar, demir parmaklıklar, beton zemin — bu monotonluğun içinde mavi, hayatın kendisi olur. Hapishane mektuplarında sık sık gökyüzünden bahseder: «Bugün gökyüzü çok güzeldi» diye yazar, sanki bir lüksü tarif eder gibi.</p><p>Nâzım\'ın şiirlerini okurken mavinin tonlarını fark edin: Bazen açık mavi — umut ve naiflik; bazen koyu mavi — derinlik ve melankoli; bazen turkuaz — Akdeniz\'in çağrısı ve vatan hasreti.</p><p>Ve ölürken Moskova\'da, İstanbul\'dan binlerce kilometre uzakta, son nefesinde acaba maviyi mi gördü? Boğaz\'ın sularını mı, Kadıköy\'ün gökyüzünü mü?</p><p>Bilmiyoruz. Ama bildiğimiz şu: Onun şiirleri var oldukça, mavi hiçbir zaman sıradan bir renk olmayacak.</p>',
                    'category_slug'    => 'edebiyat-dunyasi',
                    'status'           => PostStatus::Published,
                    'is_featured'      => true,
                    'meta_title'       => 'Nâzım\'ın Mavi Gözleri — Edebiyat',
                    'meta_description' => 'Nâzım Hikmet\'in şiirlerindeki mavi rengin özgürlük ve hasret simgeciliği.',
                ],
                [
                    'title'            => 'Kış Bahçesinde Üç Kıta',
                    'slug'             => 'kis-bahcesinde-uc-kita',
                    'excerpt'          => 'Kar yağarken yazılan üç kıtalık bir şiir, üç mevsimlik bir özlem.',
                    'body'             => '<p><strong>I.</strong></p><p>Kar yağıyor usul usul,<br>Dallar beyaz gelinliklerde.<br>Bir serçe titriyor teldekin üstünde,<br>Ben pencereden izliyorum sessizce.<br>Ellerimde bir fincan çay,<br>İçimde bir fincan hüzün.</p><p><strong>II.</strong></p><p>Hatıralar da kar gibi yağar bazen,<br>Sessiz, ince, durmaksızın.<br>Bir gülümseme düşer aklıma,<br>Sonra bir ses, sonra bir koku.<br>Lavanta mıydı, papatya mı?<br>Artık hatırlamıyorum — ama güzeldi.</p><p><strong>III.</strong></p><p>Kış bahçesinde bir bank var,<br>Üstünde kimse oturmuyor.<br>Ama izler var karda — iki çift ayak izi,<br>Yan yana yürümüş, sonra ayrılmış.<br>Her ayrılık bir iz bırakır arkasında,<br>Ve kar ne kadar yağarsa yağsın,<br>Bazı izler hiç silinmez.</p>',
                    'category_slug'    => 'siir',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Kış Bahçesinde Üç Kıta — Şiir',
                    'meta_description' => 'Kar yağarken yazılan üç kıtalık bir şiir, üç mevsimlik bir özlem.',
                ],
                [
                    'title'            => 'Çevirmenin Görünmez Emeği',
                    'slug'             => 'cevirmenin-gorunmez-emegi',
                    'excerpt'          => 'Bir eseri başka bir dile çevirmek, onu yeniden yazmaktır aslında.',
                    'body'             => '<p>Bir romanı elinize aldığınızda kapağa bakarsınız: Yazarın adı büyük harflerle yazılıdır. Ama küçük bir satır daha vardır, genellikle gözden kaçan: «Çeviren: ...»</p><p>Çevirmen edebiyatın görünmez kahramanıdır. Bir eseri bir dilden başka bir dile aktarmak, sadece kelimeleri değiştirmek değildir. Bir kültürü, bir duygu dünyasını, bir ritmi başka bir dile taşımaktır.</p><p>Düşünün: Dostoyevski\'yi Türkçe okuyabiliyorsak, Kafka\'nın «Dönüşüm»ünü anlayabiliyorsak, García Márquez\'in «Yüzyıllık Yalnızlık»ında kaybolabiliyorsak — bunun için bir çevirmene borçluyuz.</p><p>İyi çeviri, orijinalin ruhunu yakalar. Kötü çeviri, kelimeleri aktarır ama ruhu kaçırır. Örneğin Nâzım Hikmet\'in «Yaşamaya Dair» şiirini İngilizce\'ye çevirirken, «dair» kelimesinin karşılığını bulmak başlı başına bir macera.</p><p>Çevirmenler genellikle düşük ücretlerle çalışır, isimleri nadiren anılır, ödüllerde görmezden gelinir. Ama onlar olmasaydı, dünya edebiyatı diye bir kavram olmazdı. Her birimiz kendi dilimizin adasında mahsur kalırdık.</p><p>Bu yazıyı okuyan çevirmenler bilsin: Sizin emeğiniz, köprü kurmaktır — insanlar arasında, kültürler arasında, dünyalar arasında. Ve hiçbir köprü, kurucusu kadar değerli değildir.</p>',
                    'category_slug'    => 'edebiyat-dunyasi',
                    'status'           => PostStatus::Published,
                    'is_featured'      => false,
                    'meta_title'       => 'Çevirmenin Görünmez Emeği — Edebiyat',
                    'meta_description' => 'Edebiyat çevirmenlerinin görünmez ama vazgeçilmez emeğine saygı.',
                ],
            ],
        ];
    }
}
