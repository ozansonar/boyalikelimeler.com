<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $categories = Category::pluck('id', 'slug');

        $posts = [
            // ─── Şiir ───
            [
                'title'            => 'Gecenin Sessiz Çığlığı',
                'slug'             => 'gecenin-sessiz-cigligi',
                'excerpt'          => 'Karanlığın en koyu anında bile umut vardır; yeter ki yıldızlara bakmayı bilelim.',
                'body'             => '<p>Gece çöktüğünde şehrin üstüne,<br>Sessizlik bir nehir olur akar yavaşça.<br>Yıldızlar birer birer yanar gökyüzünde,<br>Her biri bir hikâye, her biri bir yaşça.</p><p>Sokak lambaları titrer rüzgârda,<br>Gölgeler dans eder kaldırım taşlarında.<br>Bir kedi geçer yolun karşısına,<br>Gözlerinde gecenin tüm sırları.</p><p>İnsanlar uyur evlerinde huzurla,<br>Rüyalar örter yorgun bedenleri.<br>Ama gece uyanıktır her zaman,<br>Dinler şehrin kalp atışını sessizce.</p><p>Ve şafak söktüğünde doğuda,<br>Gece geri çekilir yavaşça.<br>Ardında bırakır bin bir düş,<br>Ve yarına dair umutları taşıyarak.</p>',
                'category_slug'    => 'siir',
                'status'           => PostStatus::Published,
                'is_featured'      => true,
                'meta_title'       => 'Gecenin Sessiz Çığlığı — Şiir',
                'meta_description' => 'Karanlığın en koyu anında bile umut vardır. Gecenin şiirine kulak verin.',
            ],
            [
                'title'            => 'Baharın İlk Sözü',
                'slug'             => 'baharin-ilk-sozu',
                'excerpt'          => 'Kış uykusundan uyanan doğa, ilk çiçeğiyle konuşmaya başlar.',
                'body'             => '<p>Toprak uyandı uzun bir uykudan,<br>İlk çiğdem açtı beyaz, mor, sarı.<br>Kuşlar döndü göç yollarından,<br>Dallar yeşerdi, bitti kış yarı.</p><p>Rüzgâr artık sert değil yumuşak,<br>Taşır kokuları vadiden vadiye.<br>Arılar başladı çiçek aramaya,<br>Kelebekler dans eder çayırlarda.</p><p>Çocuklar koşar sokaklarda sevinçle,<br>Güneş ısıtır soğuk yüzleri.<br>Yaşlılar oturur parkın köşesinde,<br>Hatırlar eski baharları sessizce.</p><p>Doğa dirilir her bahar yeniden,<br>Umut yeşerir toprağın bağrından.<br>Ve biz öğreniriz tekrar tekrar,<br>Her bitiş aslında yeni bir başlangıçtır.</p>',
                'category_slug'    => 'siir',
                'status'           => PostStatus::Published,
                'is_featured'      => false,
                'meta_title'       => 'Baharın İlk Sözü — Şiir',
                'meta_description' => 'Kış uykusundan uyanan doğanın ilk şiiri. Baharın müjdecisi kelimeler.',
            ],

            // ─── Öykü ───
            [
                'title'            => 'Son Tren',
                'slug'             => 'son-tren',
                'excerpt'          => 'Peronda bekleyen yaşlı adam, son treni kaçırırsa hayatının en büyük fırsatını da kaçıracaktı.',
                'body'             => '<p>İstasyon saati gece yarısını gösteriyordu. Peronda tek başına duran yaşlı adam, elindeki solmuş fotoğrafa bir kez daha baktı. Fotoğraftaki genç kadın gülümsüyordu — kırk yıl önceki o gülümseme, hiç solmamıştı.</p><p>«Son tren on dakika sonra,» dedi istasyon görevlisi, yanından geçerken. Yaşlı adam başıyla onayladı. Bavulu küçüktü, tıpkı kalan ömrü gibi. Ama içinde taşıdığı umut, gençlik yıllarından bile büyüktü.</p><p>Kırk yıl önce bu istasyonda vedalaşmışlardı. «Geri döneceğim,» demişti genç kadın. Ama hayat araya girmişti — savaşlar, mesafeler, yanlış anlamalar. Ve şimdi, bir mektup gelmişti: «Hâlâ bekliyorum.»</p><p>Tren ışıkları uzaktan göründüğünde, yaşlı adam ayağa kalktı. Bacakları titriyordu ama kalbi sağlamdı. Trene binerken fotoğrafa son bir kez baktı ve gülümsedi.</p><p>«Geç kaldım,» diye fısıldadı. «Ama geldim.»</p><p>Tren hareket ettiğinde, istasyondaki saat gece yarısını bir dakika geçiyordu. Yeni bir gün başlıyordu — ve onunla birlikte, yeni bir hikâye.</p>',
                'category_slug'    => 'oyku',
                'status'           => PostStatus::Published,
                'is_featured'      => true,
                'meta_title'       => 'Son Tren — Öykü',
                'meta_description' => 'Kırk yıl sonra aynı istasyonda buluşan iki kalbin hikâyesi.',
            ],
            [
                'title'            => 'Kitapçının Kedisi',
                'slug'             => 'kitapcinin-kedisi',
                'excerpt'          => 'Eski sahaf dükkânında yaşayan kedi, müşterilere kitap seçmekte şaşırtıcı derecede yetenekliydi.',
                'body'             => '<p>Kadıköy\'ün dar sokaklarından birinde, tabelası yılların yıpratmasıyla solmuş bir sahaf vardı. «Kalem Kitabevi» yazıyordu kapının üstünde, ama herkes oraya «Kedinin Kitapçısı» derdi. Sebebi belliydi: Dükkânın gerçek sahibi, turuncu bir tekir kediydi.</p><p>Kedi — adı Mürekkep\'ti — her sabah vitrin camına kurulur, gelip geçenleri izlerdi. Ama asıl yeteneğini müşteriler içeri girdiğinde gösterirdi. Bir müşteri rafların arasında gezinirken, Mürekkep sessizce yanına gelir, bir kitabın önünde durur ve patisiyle hafifçe dokunurdu.</p><p>«Bu kedi hiç yanılmaz,» derdi dükkân sahibi Necmi amca gururla. «Kime ne kitap lazımsa, onu bulur.»</p><p>Gerçekten de Mürekkep\'in önerdiği kitapları alan müşteriler, her seferinde geri döner ve aynı şeyi söylerdi: «Hayatımda okuduğum en doğru kitaptı.»</p><p>Yıllar geçti, Necmi amca emekli oldu, dükkânı genç bir kadın devraldı. Ama Mürekkep kaldı — hâlâ vitrin camında oturuyor, hâlâ kitap seçiyor, hâlâ yanılmıyor.</p>',
                'category_slug'    => 'oyku',
                'status'           => PostStatus::Published,
                'is_featured'      => false,
                'meta_title'       => 'Kitapçının Kedisi — Öykü',
                'meta_description' => 'Müşterilere kitap seçen turuncu bir kedinin sıra dışı hikâyesi.',
            ],

            // ─── Deneme ───
            [
                'title'            => 'Dijital Çağda Yalnızlık',
                'slug'             => 'dijital-cagda-yalnizlik',
                'excerpt'          => 'Herkesin birbirine bağlı olduğu bir dünyada, gerçek bağ kurmak neden bu kadar zor?',
                'body'             => '<p>Cebimizdeki telefon titriyor, ekranlar parlıyor, bildirimler birbiri ardına geliyor. Sosyal medya hesaplarımızda yüzlerce, belki binlerce «arkadaşımız» var. Peki gerçekten bir şey paylaşmak istediğimizde, kaç kişiyi arayabiliriz?</p><p>Dijital çağ bize paradoksal bir armağan sundu: Sonsuz bağlantı imkânı ve onunla birlikte gelen derin bir yalnızlık. Marshall McLuhan\'ın «küresel köy» metaforu gerçekleşti belki, ama bu köyde herkes kendi odasına kapanmış durumda.</p><p>Bir kahve içmek için buluşmak yerine emoji gönderiyoruz. Birinin gözlerinin içine bakmak yerine profiline bakıyoruz. Sarılmak yerine kalp atıyoruz. Ve her gece yatağa girdiğimizde, günde yüzlerce mesaj atmamıza rağmen, içimizde tarif edemediğimiz bir boşluk hissediyoruz.</p><p>Belki de sorun teknolojide değil, onu nasıl kullandığımızda. Belki de dijital araçları gerçek ilişkilerin yerine değil, destekçisi olarak kullanmayı öğrenmeliyiz. Bir mesaj atıp «nasılsın?» demek güzel, ama kapıyı çalıp «geldim» demek bambaşka.</p><p>Çünkü insan, ekranların arkasına sığmayacak kadar büyük bir varlık. Ve yalnızlık, Wi-Fi sinyaliyle çözülemeyecek kadar derin bir sorun.</p>',
                'category_slug'    => 'deneme',
                'status'           => PostStatus::Published,
                'is_featured'      => true,
                'meta_title'       => 'Dijital Çağda Yalnızlık — Deneme',
                'meta_description' => 'Herkesin bağlı olduğu bir dünyada gerçek bağ kurmak neden bu kadar zor?',
            ],
            [
                'title'            => 'Zamanın Kıyısında Durmak',
                'slug'             => 'zamanin-kiyisinda-durmak',
                'excerpt'          => 'Zaman nehir gibi akar diyorlar ama belki de biz suyun içinde yüzmeyi bilmiyoruz.',
                'body'             => '<p>Çocukken yaz tatilleri sonsuza kadar sürerdi. Bir öğleden sonra, arka bahçede böcekleri izleyerek geçen saatler, bitmek bilmeyen bir hazineydi. Şimdi ise aylar, haftalara; haftalar, günlere sığıyor sanki.</p><p>Einstein zamanın göreceli olduğunu söylemişti. Fizik için bu doğru olabilir, ama insan deneyimi için çok daha derin bir anlam taşıyor. Mutlu anlar bir çırpıda geçerken, acı çektiğimiz saatler neden sonsuza kadar uzuyor?</p><p>Belki de zamanı yaşama biçimimiz, onu algılama biçimimizi belirliyor. Farkındalıkla yaşanan bir dakika, dalgınlıkla harcanan bir saatten daha uzun ve dolu olabilir. Bir çay bardağını usulca yudumlayarak, buharının dansını izleyerek içtiğinizde, o birkaç dakika bir meditasyona dönüşür.</p><p>Modern dünya bizi hızlandırıyor, «vakit nakittir» diyor, «çok işin var» diyor, «geç kalıyorsun» diyor. Ama belki de asıl geç kaldığımız şey, an\'ın kendisi.</p><p>Zaman nehir gibi akar, bu doğru. Ama nehrin kıyısında durup suyu seyretmeyi de bilelim. Çünkü hayat, akan suyun içinde değil, onu seyredenlerin gözlerinde saklı.</p>',
                'category_slug'    => 'deneme',
                'status'           => PostStatus::Published,
                'is_featured'      => false,
                'meta_title'       => 'Zamanın Kıyısında Durmak — Deneme',
                'meta_description' => 'Zaman nehri akıp giderken, an\'ın farkına varmanın önemi üzerine düşünceler.',
            ],

            // ─── Sanat ───
            [
                'title'            => 'Van Gogh\'un Yıldızlı Gecesi: Dehanın Karanlık Işığı',
                'slug'             => 'van-goghun-yildizli-gecesi',
                'excerpt'          => 'Bir akıl hastanesinin penceresinden bakarak çizilen tablo, nasıl oldu da dünyanın en tanınmış eseri oldu?',
                'body'             => '<p>1889 yılının haziran ayında, Fransa\'nın güneyindeki Saint-Rémy-de-Provence\'ta bir akıl hastanesinin penceresinden dışarı bakan bir adam vardı. Adı Vincent van Gogh\'du ve o pencereden gördüğü manzarayı, sanat tarihinin en ikonik tablosuna dönüştürecekti.</p><p>«Yıldızlı Gece» sıradan bir manzara resmi değildir. Van Gogh\'un iç dünyasının, ruhsal çalkantılarının, evrenle kurduğu derin bağın yansımasıdır. Gökyüzündeki girdaplar, fırça darbelerinin ritmi, yıldızların abartılı parlaklığı — bunların hepsi, sanatçının gerçeği değil, gerçeğin ötesini görme çabasıdır.</p><p>İlginç olan şudur: Van Gogh hayattayken sadece bir tablo satabilmiştir. «Yıldızlı Gece» de dahil olmak üzere, eserlerinin büyük çoğunluğu ölümünden sonra değer kazanmıştır. Bu, sanat tarihinin en acı paradokslarından biridir: En çok sevilen eserleri yaratan sanatçı, en çok acı çeken sanatçılardan biriydi.</p><p>Bugün «Yıldızlı Gece» New York\'taki MoMA müzesinde asılı duruyor. Her yıl milyonlarca insan o tablonun karşısında durup, Van Gogh\'un gördüğü geceyi görmeye çalışıyor. Ve belki de hissettikleri şey, yüz yıldan fazla bir süre önce o pencerenin önünde duran adamın hissettiğiyle aynı: Evrenin büyüklüğü karşısında duyulan huşu ve o büyüklüğün içindeki küçücük insanın anlam arayışı.</p>',
                'category_slug'    => 'sanat',
                'status'           => PostStatus::Published,
                'is_featured'      => false,
                'meta_title'       => 'Van Gogh\'un Yıldızlı Gecesi — Sanat İncelemesi',
                'meta_description' => 'Sanat tarihinin en ikonik tablosu Yıldızlı Gece\'nin hikâyesi ve Van Gogh\'un dehasının analizi.',
            ],
            [
                'title'            => 'Türk Sinemasında Yeni Dalga',
                'slug'             => 'turk-sinemasinda-yeni-dalga',
                'excerpt'          => 'Son on yılda Türk sineması uluslararası festivallerde ciddi bir varlık göstermeye başladı.',
                'body'             => '<p>Türk sineması, Yılmaz Güney\'in «Yol» filmiyle 1982\'de Cannes\'da Altın Palmiye kazandığından bu yana uzun bir yol kat etti. Son on yılda ise Nuri Bilge Ceylan, Semih Kaplanoğlu, Deniz Gamze Ergüven gibi yönetmenler sayesinde uluslararası arenada yeniden parlıyor.</p><p>Bu yeni dalganın en belirgin özelliği, hikâye anlatımındaki cesaret. Geleneksel melodramlardan uzaklaşan bu filmler, gündelik hayatın sessiz dramlarını, toplumsal çelişkileri ve bireyin iç dünyasını derinlikli bir şekilde işliyor.</p><p>Nuri Bilge Ceylan\'ın «Kış Uykusu» filmi 2014\'te Altın Palmiye kazandığında, bu sadece bir ödül değil, Türk sinemasının dünyaya verdiği bir mesajdı: «Biz buradayız ve anlatacak hikâyelerimiz var.»</p><p>Genç yönetmenler de bu mirası sahipleniyor. Kısa filmlerden uzun metrajlara, belgesellerden animasyonlara kadar geniş bir yelpazede üretim yapan yeni nesil sinemacılar, dijital platformların da desteğiyle hikâyelerini dünya izleyicisine ulaştırıyor.</p><p>Türk sinemasının geleceği, bu cesur ve özgün seslere bağlı. Ve görünen o ki, anlatacak daha çok hikâye var.</p>',
                'category_slug'    => 'sanat',
                'status'           => PostStatus::Published,
                'is_featured'      => false,
                'meta_title'       => 'Türk Sinemasında Yeni Dalga',
                'meta_description' => 'Uluslararası festivallerde ödüller toplayan Türk sinemasının yeni yüzü.',
            ],

            // ─── Edebiyat Dünyası ───
            [
                'title'            => 'Orhan Pamuk\'un Edebi Evreni',
                'slug'             => 'orhan-pamukun-edebi-evreni',
                'excerpt'          => 'Nobel ödüllü yazarımız Orhan Pamuk\'un romanlarında İstanbul, hafıza ve kimlik temasları.',
                'body'             => '<p>2006 yılında Nobel Edebiyat Ödülü\'nü alan Orhan Pamuk, Türk edebiyatını dünya sahnesine taşıyan en önemli isimlerden biridir. «Benim Adım Kırmızı», «Kar», «Masumiyet Müzesi» gibi eserleriyle Doğu ile Batı arasındaki gerilimi, İstanbul\'un hafızasını ve bireyin kimlik arayışını eşsiz bir dille anlattı.</p><p>Pamuk\'un romanlarının merkezinde hep İstanbul vardır. Ama bu, turistik kartpostallardaki İstanbul değil; melankolisiyle, hüznüyle, yıkılmış imparatorlukların gölgesiyle yaşayan bir şehirdir. «İstanbul: Hatıralar ve Şehir» adlı otobiyografik eserinde bu duyguyu «hüzün» kavramıyla tanımlar — şehrin kolektif melankolisi.</p><p>Romanlarındaki anlatım tekniği de dikkat çekicidir. Postmodern öğelerle geleneksel Doğu anlatılarını birleştiren Pamuk, okuyucuyu katmanlı bir hikâye labirentine davet eder. «Benim Adım Kırmızı»\'da bir cesedin ağzından başlayan anlatı, Osmanlı minyatür sanatının dünyasında dolaştırır bizi.</p><p>Pamuk\'un eserleri bugün altmıştan fazla dile çevrilmiş durumda. Her yeni romanı, hem Türk edebiyatı için hem de dünya edebiyatı için bir olay niteliğinde. Ve İstanbul, onun kaleminden dünyaya akmaya devam ediyor.</p>',
                'category_slug'    => 'edebiyat-dunyasi',
                'status'           => PostStatus::Published,
                'is_featured'      => true,
                'meta_title'       => 'Orhan Pamuk\'un Edebi Evreni',
                'meta_description' => 'Nobel ödüllü Orhan Pamuk\'un romanlarında İstanbul, hafıza ve kimlik temasları.',
            ],
            [
                'title'            => 'Genç Yazarlara Öneriler: İlk Romanınızı Yazmak',
                'slug'             => 'genc-yazarlara-oneriler',
                'excerpt'          => 'İlk romanınızı yazmak korkutucu olabilir ama doğru adımlarla bu yolculuk büyüleyici bir maceraya dönüşür.',
                'body'             => '<p>Her yazar, boş bir sayfanın karşısında aynı duyguyu hisseder: Hem heyecan hem korku. İlk romanınızı yazmak, dağın zirvesine tırmanmaya benzer — uzaktan imkânsız görünür ama her adım sizi hedefe yaklaştırır.</p><p><strong>1. Her Gün Yazın</strong><br>Profesyonel yazarların ortak özelliği, yazma disiplinidir. Günde 500 kelime bile olsa, her gün yazmak kas hafızası oluşturur. Stephen King günde 2000 kelime yazdığını söyler; siz 500\'den başlayın, önemli olan süreklilik.</p><p><strong>2. Mükemmeliyetçiliği Bırakın</strong><br>İlk taslak berbat olacak — ve bu tamamen normal. Hemingway\'in dediği gibi, «Her ilk taslak berbattır.» Amacınız mükemmel bir metin değil, ham bir malzeme üretmek olmalı. Düzenleme sonra gelir.</p><p><strong>3. Çok Okuyun</strong><br>İyi bir yazar olmak için iyi bir okuyucu olmak şart. Hem kendi türünüzde hem de farklı türlerde okuyun. Her okuduğunuz kitap, bilinçaltınıza bir şeyler ekler.</p><p><strong>4. Karakterlerinizi Tanıyın</strong><br>Okuyucuları bir romanın içine çeken şey, olay örgüsünden çok karakterlerdir. Karakterlerinizin geçmişini, korkularını, hayallerini bilin — yazmaya başlamadan önce.</p><p><strong>5. Sonunu Bilin</strong><br>Romanınızın sonunu bilmek, yolculuğun yönünü bilmek demektir. Haritasız yolculuk yapabilirsiniz ama bir pusulanız olsun.</p><p>Ve en önemlisi: Başlayın. Mükemmel zamanı beklemeyin, çünkü mükemmel zaman asla gelmez. En iyi zaman, şimdi.</p>',
                'category_slug'    => 'edebiyat-dunyasi',
                'status'           => PostStatus::Published,
                'is_featured'      => false,
                'meta_title'       => 'Genç Yazarlara Öneriler: İlk Romanınızı Yazmak',
                'meta_description' => 'İlk romanınızı yazmak için pratik öneriler ve ilham veren yazma teknikleri.',
            ],
        ];

        foreach ($posts as $index => $post) {
            $categoryId = $categories[$post['category_slug']] ?? null;
            unset($post['category_slug']);

            Post::updateOrCreate(
                ['slug' => $post['slug']],
                array_merge($post, [
                    'category_id'    => $categoryId,
                    'user_id'        => $user->id,
                    'allow_comments' => true,
                    'sort_order'     => $index,
                    'view_count'     => rand(50, 1500),
                    'published_at'   => now()->subDays(rand(1, 60)),
                ])
            );
        }
    }
}
