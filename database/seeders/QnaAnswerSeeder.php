<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\QnaStatus;
use App\Enums\RoleSlug;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class QnaAnswerSeeder extends Seeder
{
    /**
     * Soru slug → 5 cevap.
     *
     * @var array<string, list<string>>
     */
    private array $answers = [
        // ── Edebiyat ──
        'turk-edebiyatinda-en-etkili-roman-hangisidir-sizce' => [
            'Benim için Oğuz Atay\'ın Tutunamayanlar\'ı tartışmasız bir numaradır. Türk aydınının bunalımını bu kadar derinlemesine işleyen başka bir roman yok. Postmodern anlatımı da dönemine göre çok ileri.',
            'Yaşar Kemal\'in İnce Memed\'i bence en etkili romandır. Anadolu insanının direniş hikâyesini evrensel bir dille anlatması onu dünya edebiyatı klasikleri arasına soktu. Nobel\'e aday gösterilmesi boşuna değil.',
            'Ahmet Hamdi Tanpınar\'ın Huzur romanını öneriyorum. İstanbul\'u, Doğu-Batı sentezini ve bireysel huzursuzluğu bu denli katmanlı işleyen başka bir eser düşünemiyorum. Her okuduğumda yeni şeyler keşfediyorum.',
            'Sabahattin Ali — Kürk Mantolu Madonna. Kısa ama o kadar yoğun ki, bittiğinde uzun süre etkisinden çıkamıyorsunuz. Raif Efendi\'nin sessiz trajedisi çok evrensel bir duygu.',
            'Orhan Pamuk\'un Benim Adım Kırmızı romanı diyorum. Osmanlı minyatür sanatı üzerinden Doğu-Batı çatışmasını anlatması, polisiye kurguyla birleştirmesi muhteşem. Dünya çapında da büyük yankı uyandırdı.',
        ],
        'siir-okuma-aliskanligi-nasil-kazanilir' => [
            'Nazım Hikmet ile başlamanı öneririm. "Memleketimden İnsan Manzaraları" gibi uzun şiirler yerine önce kısa lirik şiirleriyle başla. Sesi ve ritmi çok çekici, alışkanlık kazandırır.',
            'Ben her gece yatmadan önce bir şiir okuma alışkanlığı edindim. Cemal Süreya, Turgut Uyar ve Edip Cansever\'le başladım. Kısa ve imgelerle dolu şiirler başlangıç için ideal.',
            'Şiir okumayı podcast\'lerle birleştir. "Şiir Perisi" gibi kanallar var, şiirleri sesli okuyorlar. Önce dinle, sonra metne dön. Kulağın alışınca okumak da kolaylaşıyor.',
            'Attilâ İlhan\'ın "Ben Sana Mecburum" kitabı harika bir başlangıç noktası. Aşk şiirleri üzerinden şiire giriş yapmak motivasyon açısından çok iyi. Dili zengin ama anlaşılır.',
            'Günde bir şiir kuralı koy kendine. Telefona bir şiir uygulaması indir, her sabah bildirim gelsin. Küçük adımlarla başlayınca zamanla vazgeçilmez bir alışkanlığa dönüşüyor.',
        ],

        // ── Felsefe ──
        'varolusculuk-gunluk-hayatimizi-nasil-etkiler' => [
            'Varoluşçuluk bana "seçim sorumluluğu" bilinci kazandırdı. Artık "mecbur kaldım" demek yerine her kararımın farkında oluyorum. Bu başta ağır geliyor ama zamanla özgürleştirici.',
            'Sartre\'ın "varoluş özden önce gelir" ilkesini hayatıma uyguladığımda kariyer değiştirme cesareti buldum. Kimsenin beni bir kalıba sokma hakkı yok, kendimi her gün yeniden tanımlayabilirim.',
            'Camus\'nün absürd felsefesi bana paradoks gibi gelse de rahatlatıcı. Hayatın anlamsızlığını kabul edip yine de yaşamı sevmeyi öğretir. Sisifos Söyleni bu konuda çok aydınlatıcı.',
            'Günlük hayatta en çok "otantiklik" kavramı işime yarıyor. Başkalarının beklentilerine göre değil, kendi değerlerime göre yaşamaya çalışıyorum. Heidegger\'in "das Man" eleştirisi çok yerinde.',
            'Varoluşçuluk kaygıyı normalleştirdi benim için. Kierkegaard\'ın "kaygı özgürlüğün baş dönmesidir" sözü çok doğru. Kaygıdan kaçmak yerine onu anlamlandırmayı öğrendim.',
        ],
        'stoacilik-modern-cagda-hala-gecerli-mi' => [
            'Kesinlikle geçerli. Her sabah "kontrol edebildiklerim / edemediklerim" ayrımı yapıyorum. Trafik, hava durumu, başkalarının davranışları — bunlara sinirlenmeyi bıraktım. İnanılmaz rahatlatıcı.',
            'Seneca\'nın mektuplarını her hafta bir tane okuyorum. 2000 yıl öncesinden yazılmış ama sanki bugün için yazılmış. Özellikle zaman yönetimi ve ölüm farkındalığı konusundaki düşünceleri çok güçlü.',
            '"Premeditatio malorum" (olası kötülükleri önceden düşünme) tekniğini uyguluyorum. Sunum öncesi en kötü senaryoyu hayal ediyorum. Gerçekleşmeyince rahatlıyorum, gerçekleşirse hazırlıklı oluyorum.',
            'Epiktetos\'un "Enchiridion" kitabını cebimde taşıyorum (e-kitap olarak). Kısa kısa aforizmalar var, günün herhangi bir anında açıp okuyabilirsin. Pratik ve doğrudan.',
            'Stoacılığın "amor fati" (kaderi sev) prensibi hayatımı değiştirdi. Başıma gelen her şeyi şikâyet konusu yapmak yerine öğrenme fırsatı olarak görüyorum. Kolay değil ama denemeye değer.',
        ],

        // ── Tarih ──
        'osmanli-imparatorlugunun-en-parlak-donemi-hangisidir' => [
            'Kanuni dönemi denmesinin sebebi sadece askeri başarılar değil. Mimar Sinan\'ın eserleri, Bâkî\'nin şiirleri, hukuk reformları... Kültürel ve idari zirve bir arada yaşanmış. Ben de Kanuni dönemi diyorum.',
            'Fatih Sultan Mehmet dönemi bence daha kritiktir. İstanbul\'un fethi sadece askeri değil, bir çağ kapayıp çağ açan bir olay. Üniversite kurması, farklı dinlere hoşgörüsü çok ilerici.',
            'Tarihçi Halil İnalcık\'ın görüşüne katılıyorum: Kanuni sonrası "duraklama" aslında bir mit. İmparatorluk 17. yüzyılda da güçlüydü. Ama zirve olarak 1520-1566 arası yaygın kabul görüyor.',
            'Ben II. Bayezid dönemini küçümsememek gerektiğini düşünüyorum. İspanya\'dan sürülen Yahudileri kabul etmesi, ticaret ağlarının genişlemesi önemli. Ama "altın çağ" denince Kanuni dönemi öne çıkıyor.',
            'Sokullu Mehmed Paşa dönemini de dahil etmek lazım. Kanuni\'nin son yılları ve II. Selim dönemi, Sokullu\'nun dehası sayesinde parlak geçti. Don-Volga kanalı projesi bile tek başına vizyonu gösterir.',
        ],
        'kurtulus-savasinin-en-kritik-donum-noktasi-neydi' => [
            'Sakarya Meydan Muharebesi bence en kritik dönüm noktasıdır. 22 gün 22 gece süren bu savaş kaybedilseydi Ankara düşecek ve direniş çökecekti. Mustafa Kemal\'in "Hattı müdafaa yoktur, sathı müdafaa vardır" emri tarihi değiştirdi.',
            'Büyük Taarruz kesin sonucu getirdi ama bence asıl dönüm noktası TBMM\'nin açılmasıdır. Savaşı kazanacak siyasi irade ve meşruiyet buradan doğdu. Meclis olmasa ordu olmaz, ordu olmasa zafer olmazdı.',
            'I. İnönü Muharebesi psikolojik açıdan en kritik olandır. İlk kez düzenli ordu bir zafer kazandı ve "bu iş olabilir" inancı doğdu. Moral etkisi diğer tüm savaşlara yansıdı.',
            'Mudanya Mütarekesi\'ni de sayabiliriz. Savaş meydanında kazanılan zaferin diplomatik masada tescil edilmesi kritikti. İngilizlerin geri adım atması Türk tarafının haklılığını kanıtladı.',
            'Ben Dumlupınar Meydan Muharebesi\'ni ayrı bir yere koyuyorum. Başkomutanlık Muharebesi olarak da bilinir. 30 Ağustos düşman ordusunun tamamen imha edildiği gündür. Zafer Bayramı olarak kutlanması boşuna değil.',
        ],

        // ── Bilim ──
        'yapay-zeka-bilimsel-arastirmalari-nasil-donusturecek' => [
            'İlaç keşfinde devrim yaşanıyor. AlphaFold protein yapılarını çözdü, bu normalde yıllar sürecek bir işti. Önümüzdeki 10 yılda kişiye özel ilaç tedavisi sıradan hale gelebilir.',
            'Malzeme biliminde büyük etki bekliyorum. Yapay zekâ milyonlarca malzeme kombinasyonunu simüle edip en uygununu bulabiliyor. Süper iletkenler, batarya teknolojisi gibi alanlarda atılımlar göreceğiz.',
            'Bence en büyük etki veri analizi hızında olacak. Genomik, iklim bilimi, astronomi gibi veri yoğun alanlarda yapay zekâ insan araştırmacıların yıllar sürecek işini günlere indirecek.',
            'Dikkatli olmak da lazım. Yapay zekânın "halüsinasyon" problemi bilimde tehlikeli olabilir. Yanlış korelasyonlar bulup yanıltıcı sonuçlar üretebilir. İnsan denetimi hâlâ kritik.',
            'Matematik ve teorem kanıtlama alanında ilginç gelişmeler var. DeepMind\'ın matematikçilerle işbirliği yaptığı projeler yeni teoremler keşfetmeye başladı. Saf bilimlerde bile yapay zekâ artık yardımcı.',
        ],
        'kuantum-bilgisayarlar-gercekten-her-seyi-degistirecek-mi' => [
            'Kısa vadede hayır, uzun vadede belki. Kuantum bilgisayarlar her iş için uygun değil. Kriptografi, optimizasyon ve moleküler simülasyon gibi spesifik alanlarda fark yaratacaklar ama bilgisayarınızın yerini almayacaklar.',
            'Google\'ın kuantum üstünlüğü iddiası belirli bir problem içindi. Genel amaçlı kuantum bilgisayar hâlâ uzak. Error correction (hata düzeltme) sorunu çözülmeden pratik kullanım mümkün değil.',
            'İlaç ve malzeme biliminde kuantum simülasyonlar gerçekten çığır açabilir. Molekülleri kuantum düzeyinde simüle etmek klasik bilgisayarlar için imkânsız. Bu alanda 10-15 yıl içinde somut sonuçlar görebiliriz.',
            'Kriptografi alanında endişeler var. Kuantum bilgisayarlar mevcut şifreleme yöntemlerini kırabilir. Bu yüzden "post-kuantum kriptografi" çalışmaları şimdiden başladı. Bankacılık sektörü hazırlık yapıyor.',
            'Günlük hayata etkisi dolaylı olacak. Siz kuantum bilgisayar kullanmayacaksınız ama bulut üzerinden kuantum hesaplama hizmetleri alacaksınız. AWS ve IBM zaten bu hizmetleri sunmaya başladı.',
        ],

        // ── Sanat ──
        'dijital-sanat-geleneksel-sanatin-yerini-alabilir-mi' => [
            'Yerini almaz, yanına eklenir. Fotoğraf icat edildiğinde de resmin öleceği söylenmişti ama ikisi de yaşıyor. Dijital sanat yeni bir araç, yeni bir ifade biçimi. Tuval ve boya her zaman var olacak.',
            'NFT balonu patladı ama dijital sanat duruyor. Procreate, Photoshop gibi araçlarla inanılmaz işler çıkaran sanatçılar var. Önemli olan araç değil, arkasındaki yaratıcı vizyon.',
            'Geleneksel sanatın "aura"sı (Walter Benjamin\'in deyimiyle) dijitalde yok. Bir yağlı boya tablonun karşısında durmak ile ekranda görmek arasında derin bir fark var. Bu yüzden geleneksel sanat ölmez.',
            'Galericiler ve koleksiyonerler hâlâ fiziksel eserlere yatırım yapıyor. Dijital sanat piyasası büyüyor ama geleneksel sanat piyasasının yerini almaktan çok uzak. İkisi farklı ekosistemler.',
            'Ben ikisini birleştiren sanatçıları çok beğeniyorum. Önce kâğıda çizim yapıp sonra dijitalde renklendiren, AR ile zenginleştiren işler var. Gelecek hibrit sanatta bence.',
        ],
        'muzik-egitimine-baslamak-icin-en-uygun-yas-kactir' => [
            'Piyano için 5-6 yaş ideal başlangıç. Ama 4 yaşında Orff eğitimiyle ritim ve müzikalite geliştirmek çok faydalı. Küçük yaşta başlayanların kulak gelişimi daha iyi oluyor.',
            'Keman için 4-5 yaş önerilir çünkü erken başlamak kas hafızası için önemli. Suzuki metodu küçük çocuklar için çok etkili. Ama çocuğun zorla değil, keyifle yapması şart.',
            'Yetişkin olarak da başarılı olabilirsiniz, motivasyonunuz varsa. 30 yaşında piyano öğrenmeye başladım ve 3 yılda Chopin çalabilecek seviyeye geldim. Çocuklar kadar hızlı değilsiniz ama bilinçli çalışmayla ilerlersiniz.',
            'Çocuğunuzun ne zaman hazır olduğunu gözlemleyin. Müziğe ilgi gösteriyorsa, ritim tutabiliyorsa başlayabilir. Zorla başlatılan çocuklarda müzik nefretine dönüşebilir, dikkat edin.',
            'Müzik eğitimi sadece enstrüman değil. Şarkı söyleme, kulak eğitimi, nota okuma da önemli. 3-4 yaşında müzik ve hareket dersleriyle başlayıp 6 yaşında enstrümana geçmek ideal bir yol haritası.',
        ],

        // ── Psikoloji ──
        'prokrastinasyonun-psikolojik-sebepleri-nelerdir' => [
            'Prokrastinasyon tembellik değil, duygu düzenleme problemidir. Araştırmalar gösteriyor ki ertelemenin altında başarısızlık korkusu, mükemmeliyetçilik veya görevin yarattığı kaygı var. Görevi değil, göreve bağlı duyguyu erteliyoruz.',
            '"Temporal discounting" (zamansal iskonto) denen bir şey var. Beynimiz uzak gelecekteki ödülleri küçümser, anlık hazları büyütür. Bu yüzden Netflix izlemek tez yazmaktan cazip gelir. Çözüm: büyük görevleri küçük, anlık ödüllü parçalara böl.',
            'Pomodoro tekniği bende çok işe yaradı. 25 dakika çalış, 5 dakika ara. Beyin "sadece 25 dakika" deyince direnci kırılıyor. Başlamak en zor kısmı, bir kere başlayınca devam etmek kolay.',
            'Tim Pychyl\'ın araştırmalarına bak. "Sadece ilk adımı at" diyor. Tüm görevi düşünme, sadece ilk 2 dakikasını yap. Beyin başlayınca zaten momentum kazanıyor. Bende yüzde doksan işe yarıyor.',
            'Kendi prokrastinasyon tetikleyicilerini keşfet. Benim için sıkıcı ve belirsiz görevlerde oluyor. Görevi netleştirip küçük adımlara bölünce erteleme azalıyor. Bir de kendini affetmeyi öğren, suçluluk ertelemeyi artırıyor.',
        ],
        'sosyal-medya-ruh-sagligimizi-nasil-etkiliyor' => [
            'Jonathan Haidt\'in "The Anxious Generation" kitabını oku. Özellikle 2012 sonrası sosyal medya kullanımıyla ergen depresyonu arasında güçlü korelasyon gösteriyor. Karşılaştırma tuzağı gerçekten var ve zararlı.',
            'FOMO (kaçırma korkusu) bende çok şiddetliydi. Çözümüm: bildirim kapatmak ve günde belirli saatlerde bakmak. Sabah ilk iş telefona bakmamak özellikle önemli, güne başkalarının hayatıyla değil kendinle başla.',
            'Pasif kullanım ile aktif kullanım farkı büyük. Sadece scroll yapıp başkalarını izlemek kötü. Ama arkadaşlarla mesajlaşmak, ilgi alanlarında içerik paylaşmak olumlu etki yapıyor. Kullanım şeklini değiştir.',
            '"Dopamin detoksu" abartılı bir kavram ama sosyal medya molası vermek gerçekten işe yarıyor. 1 hafta Instagram\'dan uzak durduğumda kaygım belirgin şekilde azaldı. Denemeye değer.',
            'Ekran süresi takibi yap. Günde kaç saat harcadığını görünce şok olursun. Ben günde 4 saatten 1 saate düşürdüm. Kalan 3 saatte kitap okuyorum, yürüyüş yapıyorum. Ruh hâlim gözle görülür şekilde iyileşti.',
        ],

        // ── Teknoloji ──
        'yazilim-ogrenmek-isteyenlere-ilk-dil-olarak-ne-onerirsiniz' => [
            'Python ile başla, tereddüt etme. Sözdizimi temiz ve okunabilir, hata mesajları anlaşılır. Web, veri bilimi, yapay zekâ, otomasyon — her alanda kullanabilirsin. Udemy\'de Colt Steele veya Angela Yu kursları çok iyi.',
            'Hedefe göre değişir. Web geliştirme istiyorsan JavaScript ile başla çünkü hem frontend hem backend yapabilirsin. Genel programlama mantığı öğrenmek istiyorsan Python daha uygun.',
            'CS50 kursunu öneririm (Harvard, ücretsiz). C ile başlıyor ama temel bilgisayar bilimi kavramlarını o kadar iyi anlatıyor ki, sonra hangi dile geçersen geç sağlam temelin oluyor.',
            'Ben JavaScript ile başladım ve pişman değilim. Tarayıcıda hemen sonuç görmek motivasyonu yükseltiyor. Bir buton yap, tıklandığında bir şey olsun — bu heyecan başka dillerde o kadar kolay gelmiyor.',
            'Dil seçimi o kadar önemli değil aslında. Önemli olan programlama mantığını öğrenmek: değişkenler, döngüler, fonksiyonlar, veri yapıları. Bunları bir dilde öğrenince diğerine geçmek kolay. Python veya JavaScript ile başla, 3 ay sonra rahat edersin.',
        ],
        'acik-kaynak-projelere-nasil-katkida-bulunabilirim' => [
            '"Good first issue" etiketli issue\'ları ara GitHub\'da. Birçok büyük proje yeni başlayanlar için özellikle işaretlenmiş kolay görevler bırakıyor. Dokümantasyon düzeltmeleri bile değerli katkıdır, kodla başlamak zorunda değilsin.',
            'Önce kullandığın araçlara katkı yap. Bir kütüphanede bug bulduysan, dokümantasyonda eksiklik gördüysen bunları raporla veya düzelt. Kullandığın aracı zaten bildiğin için bağlamı anlamak kolay olur.',
            'First Contributions reposu ile başla (github.com/firstcontributions). PR açma sürecini adım adım öğretir. İlk PR\'ını atınca özgüven geliyor, sonrası çorap söküğü gibi.',
            'Hacktoberfest etkinliğine katıl (her yıl Ekim ayında). Açık kaynak katkısını teşvik eden bir etkinlik, birçok proje yeni başlayanlara özel issue\'lar açıyor. Topluluk da çok yardımsever oluyor.',
            'Projenin CONTRIBUTING.md dosyasını mutlaka oku. Kod stili, branch isimlendirme, commit mesajı formatı gibi kurallar var. Bunlara uymazsan PR\'ın reddedilebilir. Ayrıca küçük PR\'lar aç, dev PR\'lar review edilmesi zor.',
        ],

        // ── Dil Bilgisi ──
        'turkcede-en-cok-yapilan-yazim-hatalari-nelerdir' => [
            '"De/da" ayrımı en yaygın hata. Bağlaç olan "de/da" ayrı, ek olan "-de/-da" bitişik yazılır. Test: "de/da" çıkarılınca cümle anlam kaybediyorsa bağlaçtır, ayrı yazılır. "Ben de geldim" (bağlaç) vs "evde kaldım" (ek).',
            '"Ki" bağlacı genellikle ayrı yazılır: "dedi ki", "biliyorum ki". Ama "hâlbuki", "mademki", "oysaki" gibi kalıplaşmış kelimeler bitişik. "-ki" eki ise her zaman bitişik: "evdeki", "bahçedeki".',
            'Kesme işareti hatası çok yaygın. Özel isimlere gelen ekler kesme ile ayrılır: "Ankara\'da" ama "Büyük Millet Meclisi\'ne" değil "Büyük Millet Meclisine" (çünkü kurum adı). TDK\'nın kılavuzunu kontrol etmekte fayda var.',
            '"Yalnız/yanlız", "herkes/herkez", "hiçbir şey/hiçbirşey" çok sık yapılan hatalar. Doğruları: yalnız, herkes, hiçbir şey. Bir de "Türkçe\'de" değil "Türkçede" olacak, dil adlarına kesme konmaz.',
            '"İle" bağlacı sorunlu. Ek olarak kullanıldığında bitişik: "arabayla", "trenle". Ama bazı durumlarda ayrı yazılabilir: "Ali ile Veli". Pratik kural: kişi adlarıyla "ile" ayrı, nesnelerle bitişik (-la/-le).',
        ],
        'yabanci-kelimelerin-turkce-karsiliklari-neden-tutmuyor' => [
            '"Bilgisayar", "yazılım", "donanım" tuttu çünkü erken dönemde önerildi ve alternatifi yoktu. Ama "internet" için "genel ağ" dendi, kimse kullanmadı. Kelime günlük dile zaten yerleşmişse değiştirmek çok zor.',
            'Dil devrimine bakarsak zorlama karşılıklar tutmuyor. Halk doğal olarak benimsemeli. "Uçak" tuttu çünkü kısa ve mantıklı. "Yetişkinler eğitim merkezi" gibi uzun karşılıklar tutmuyor.',
            'Bence ikisi arasında denge kurulmalı. Teknik terimlerde uluslararası kelimeler kalmalı ("algoritma", "protein"), günlük dilde Türkçe tercih edilmeli. Her kelimeye karşılık bulmaya çalışmak gereksiz.',
            'Fransızlar bu konuda çok katı. "E-mail" yerine "courriel" diyorlar, kanunla zorunlu kıldılar. Ama gençler yine İngilizce kullanıyor. Dil doğal akışına bırakılmalı, zorlamayla olmuyor.',
            'TDK\'nın görevi karşılık üretmek ama dayatmak değil. Toplum beğenirse kullanır. "Bilişim" güzel bir örnek, tuttu. Ama "çağrışımlı bellek" (cache) gibi terimler günlük dile giremez. Dilin doğal evrimi en sağlıklısı.',
        ],

        // ── Mitoloji ──
        'turk-mitolojisinde-en-ilginc-yaratik-hangisidir' => [
            'Tepegöz bence en ilginç olanı. Dede Korkut Hikâyeleri\'ndeki bu tek gözlü dev, Yunan mitolojisindeki Kyklop\'tan bağımsız olarak ortaya çıkmış olabilir. Basat\'ın Tepegöz\'ü öldürme hikâyesi Odysseus-Polyphemos ile şaşırtıcı benzerlikler taşıyor.',
            'Al Karısı (Albastı) çok ilginç ve az bilinen bir figür. Lohusa kadınlara ve yeni doğan bebeklere musallat olan kötü ruh olarak anlatılır. Orta Asya\'dan Anadolu\'ya taşınmış, hâlâ bazı yörelerde inanılıyor.',
            'Koncolos (veya Konçolos) — Türk mitolojisindeki vampir benzeri yaratık. Ölen kişinin ruhu bedene geri dönüp geceleri dolaşır. Anadolu\'da "Concolis" olarak da biliniyor. Batı vampir mitinden farklı özellikleri var.',
            'Anka kuşu (Zümrüdüanka/Simurg) en bilineni ama detaylarını az kişi bilir. Kaf Dağı\'nda yaşar, tüm bilgeliğe sahiptir. Attâr\'ın "Mantıku\'t-Tayr" (Kuşların Dili) eserinde otuz kuşun Simurg\'u araması aslında kendilerini aramasıdır.',
            'Yelbegen (veya Celbeğen) — çok başlı dev. Başları kesilince yeniden çıkar. Altay ve Sibirya Türk mitolojisinde geçer. Yunan mitolojisindeki Hydra\'ya benzer ama bağımsız bir gelenek. Alp Er Tonga destanında karşımıza çıkıyor.',
        ],
        'yunan-ve-roma-mitolojisi-arasindaki-temel-farklar-nelerdir' => [
            'En büyük fark yaklaşımdadır. Yunan mitolojisi tanrıları insani zaaflarıyla anlatır — kıskanç, âşık, intikamcı. Roma mitolojisi ise tanrıları daha ciddi, devlet odaklı ve fonksiyonel gösterir. Romalılar pragmatikti, tanrıları da öyle.',
            'Ares Yunan\'da sevilmeyen, kaba kuvveti temsil eden bir tanrıydı. Mars ise Roma\'nın en saygın tanrılarından biriydi, Romulus ve Remus\'un babası. Savaş kavramına bakış tamamen farklı.',
            'Yunan mitolojisi bireysel kahramanlık hikâyelerine odaklanır (Herakles, Odysseus). Roma mitolojisi ise kuruluş ve devlet mitlerine ağırlık verir (Aeneas, Romulus). Toplumsal değerler farklı olduğu için mitler de farklılaştı.',
            'Romalılar Yunan tanrılarını aldı ama kendi yerel tanrılarını da korudu. Janus (kapılar tanrısı) tamamen Roma\'ya özgüdür, Yunan karşılığı yok. Bir de "genius" ve "numen" gibi kavramlar Roma\'ya aittir.',
            'Yaratılış mitleri çok farklı. Yunanlar Kaos\'tan düzenin çıkışını, Titanlar\'ı ve Olimposlular\'ı anlatır. Roma ise Troya\'dan kaçan Aeneas\'ın İtalya\'ya gelmesi ve Roma\'nın kuruluşuyla başlar. Vergilius\'un Aeneis\'i bu miti kanon haline getirdi.',
        ],

        // ── Günlük Yaşam ──
        'sabah-rutini-olusturmak-gercekten-hayati-degistirir-mi' => [
            'Değiştirdi, ama herkesin rutini farklı olmalı. Ben 06:30\'da kalkıyorum: su, 10 dk meditasyon, 20 dk yürüyüş, kahvaltı. Önemli olan erken kalkmak değil, güne bilinçli başlamak. Telefona bakmadan ilk 30 dakika kendine ayır.',
            'Abartılı "5\'te kalk, buz duşu al, 10 km koş" rutinleri sürdürülebilir değil. Basit tut: uyanınca 1 bardak su iç, 5 dakika germe hareketi yap, günün 3 önceliğini yaz. Bu kadar bile fark yaratıyor.',
            'Sabah rutini akşam rutiniyle başlar. Erken yatmazsan erken kalkamazsın. Ben 23:00\'te telefonu bırakıyorum, 23:30\'da yatıyorum, 06:30\'da doğal uyanıyorum. Uyku düzenin yoksa sabah rutini işkence olur.',
            '3 aydır uyguluyorum ve verimliliğim arttı diyebilirim. Ama asıl farkı "kararsızlık yorgunluğunu" azaltmasında görüyorum. Sabah ne yapacağım belli, düşünmem gerekmiyor. Bu zihinsel enerji tasarrufu sağlıyor.',
            'Herkes sabah insanı olmak zorunda değil. Gece çalışmayı seven biriysen zorla erken kalkman anlamsız. Önemli olan düzenli bir rutinin olması, ister sabah ister akşam. Kendinizi tanıyın ve ona göre düzenleyin.',
        ],
        'evde-calisirken-verimli-olmanin-sirri-nedir' => [
            'Ayrı bir çalışma alanı oluştur, yatak odasında çalışma. Beyin mekânla aktiviteyi ilişkilendirir. Küçük de olsa bir masa ve sandalyen olsun. İş bitince o alandan kalk, iş-yaşam sınırı koy.',
            'Pomodoro tekniği evde çalışanlar için birebir. 25 dakika odaklan, 5 dakika ara. Forest uygulamasını kullanıyorum, telefona dokunursan sanal ağacın kuruyor. Oyunlaştırma motivasyonu artırıyor.',
            'Sabah ofise gidiyormuş gibi hazırlan. Pijamayı çıkar, günlük kıyafetini giy. Beyne "çalışma zamanı" sinyali veriyor. Pijamalarla çalışınca "mola modundan" çıkamıyorsun.',
            'Toplantısız saatler belirle. Ben 09-12 arası "derin çalışma" yapıyorum, bu saatlerde toplantı almıyorum ve bildirimler kapalı. Öğleden sonra toplantılar ve hafif işler. Bu ayrım verimliliği ikiye katladı.',
            'En büyük düşman ev işleri. "Bir çamaşır atayım" diye kalkarsan 30 dakika gitti. Ev işlerini çalışma saatleri dışına taşı. Bir de buzdolabına gidiş sayısını azalt, yanına su şişesi ve atıştırmalık koy.',
        ],
    ];

    public function run(): void
    {
        $questions = QnaQuestion::all();
        $memberUserIds = User::whereHas('role', function ($query): void {
            $query->where('slug', RoleSlug::Kullanici->value);
        })->pluck('id')->toArray();

        if (empty($memberUserIds)) {
            $memberUserIds = User::pluck('id')->toArray();
        }

        foreach ($questions as $question) {
            $answerTexts = $this->answers[$question->slug] ?? null;

            if ($answerTexts === null) {
                continue;
            }

            $usedUserIds = [];
            $questionCreatedAt = $question->created_at;

            foreach ($answerTexts as $index => $body) {
                $availableUserIds = array_diff($memberUserIds, [$question->user_id], $usedUserIds);

                if (empty($availableUserIds)) {
                    $availableUserIds = array_diff($memberUserIds, [$question->user_id]);
                }

                if (empty($availableUserIds)) {
                    $availableUserIds = $memberUserIds;
                }

                $userId = $availableUserIds[array_rand($availableUserIds)];
                $usedUserIds[] = $userId;

                $answerCreatedAt = $questionCreatedAt->copy()
                    ->addHours(rand(1 + ($index * 4), 6 + ($index * 8)))
                    ->addMinutes(rand(0, 59));

                QnaAnswer::factory()->create([
                    'qna_question_id' => $question->id,
                    'user_id'         => $userId,
                    'body'            => $body,
                    'status'          => QnaStatus::Approved,
                    'like_count'      => rand(0, 20),
                    'created_at'      => $answerCreatedAt,
                    'updated_at'      => $answerCreatedAt,
                ]);
            }

            $question->update(['answer_count' => 5]);
        }
    }
}
