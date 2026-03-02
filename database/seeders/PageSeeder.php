<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $pages = [
            [
                'title'            => 'Hakkımızda',
                'slug'             => 'hakkimizda',
                'excerpt'          => 'Boyalı Kelimeler\'in hikâyesi, misyonu ve vizyonu hakkında bilgi edinin.',
                'body'             => '<h2>Biz Kimiz?</h2><p>Boyalı Kelimeler, 2024 yılında edebiyat ve sanat sevdalılarının buluşma noktası olarak doğdu. Amacımız, kelimelerin gücüne inanan insanları bir araya getirmek ve Türkçe edebiyatın zenginliğini dijital dünyaya taşımaktır.</p><h2>Misyonumuz</h2><p>Edebiyatı herkes için erişilebilir kılmak, genç yazarları desteklemek ve sanatın iyileştirici gücünü yaymak. Her kelimenin bir rengi, her cümlenin bir melodisi olduğuna inanıyoruz. Bu platformda şairler şiirlerini, yazarlar hikâyelerini, düşünürler fikirlerini paylaşıyor.</p><h2>Vizyonumuz</h2><p>Türkçe edebiyatın en kapsamlı dijital platformu olmak ve yeni nesil okuyucu-yazar köprüsünü kurmak. Geleneksel edebiyatın değerlerini korurken, modern anlatım tekniklerini kucaklıyoruz.</p><h2>Ekibimiz</h2><p>Boyalı Kelimeler ekibi; editörler, yazarlar, tasarımcılar ve teknoloji uzmanlarından oluşan tutkulu bir gruptur. Her birimiz, kelimelerin dünyayı daha güzel bir yer yapabileceğine inanıyoruz.</p>',
                'meta_title'       => 'Hakkımızda — Boyalı Kelimeler',
                'meta_description' => 'Boyalı Kelimeler edebiyat ve sanat platformunun hikâyesi, misyonu ve vizyonu.',
                'is_active'        => true,
                'sort_order'       => 0,
            ],
            [
                'title'            => 'İletişim',
                'slug'             => 'iletisim',
                'excerpt'          => 'Bizimle iletişime geçin, sorularınızı ve önerilerinizi paylaşın.',
                'body'             => '<h2>Bize Ulaşın</h2><p>Boyalı Kelimeler ekibine aşağıdaki kanallardan ulaşabilirsiniz. Her mesajınızı dikkatle okuyoruz ve en kısa sürede yanıt vermeye çalışıyoruz.</p><h3>E-posta</h3><p>Genel sorularınız için: <strong>iletisim@boyalikelimeler.com</strong></p><p>İçerik önerileri için: <strong>editor@boyalikelimeler.com</strong></p><h3>Sosyal Medya</h3><p>Bizi sosyal medya hesaplarımızdan takip edebilir, güncel paylaşımlarımızdan haberdar olabilirsiniz.</p><h3>Yazarlarımıza Katılın</h3><p>Siz de Boyalı Kelimeler\'de yazmak istiyorsanız, bir özgeçmiş ve en az iki örnek yazınızla birlikte bize e-posta gönderebilirsiniz. Editör ekibimiz başvurunuzu değerlendirecektir.</p><h3>Geri Bildirim</h3><p>Platformumuzla ilgili her türlü geri bildirimi memnuniyetle karşılıyoruz. Sitemizi daha iyi hale getirmek için önerileriniz çok değerli.</p>',
                'meta_title'       => 'İletişim — Boyalı Kelimeler',
                'meta_description' => 'Boyalı Kelimeler ekibiyle iletişime geçin. Sorularınız ve önerileriniz için bize ulaşın.',
                'is_active'        => true,
                'sort_order'       => 1,
            ],
            [
                'title'            => 'Yapay Zekâ ve Edebiyatın Geleceği',
                'slug'             => 'yapay-zeka-ve-edebiyatin-gelecegi',
                'excerpt'          => 'Yapay zekâ edebiyatı nasıl dönüştürüyor? İnsan yaratıcılığı ile makine zekâsının kesişim noktası.',
                'body'             => '<h2>Makineler Yazabilir mi?</h2><p>2020\'lerin ortasında yapay zekâ, edebiyat dünyasının kapısını güçlü bir şekilde çaldı. GPT modelleri şiir yazıyor, hikâye kurgulıyor, hatta roman taslakları oluşturuyor. Peki bu, edebiyatın sonu mu yoksa yeni bir çağın başlangıcı mı?</p><h2>İnsan Yaratıcılığının Eşsizliği</h2><p>Yapay zekâ, milyarlarca kelimeyi analiz ederek kalıplar oluşturabilir. Ama bir çocuğun ilk kez denizi gördüğündeki hayreti, bir âşığın ayrılık acısını, bir yaşlının ömrünün sonunda hissettiği huzuru gerçekten «anlayabilir» mi? Edebiyat sadece kelimelerden ibaret değildir — kelimelerin arkasındaki deneyim, acı, sevinç ve insanlık durumudur onu eşsiz kılan.</p><h2>Yapay Zekâ Bir Araç mıdır?</h2><p>Tıpkı matbaanın kitabı demokratikleştirmesi gibi, yapay zekâ da yazma sürecini dönüştürebilir. Yazarlar için araştırma asistanı, editörler için düzeltme aracı, yayınevleri için analiz partneri olabilir. Önemli olan, teknolojiyi insan yaratıcılığının yerine değil, yanına koymaktır.</p><h2>Gelecekte Neler Olacak?</h2><p>Yapay zekâ ile insan iş birliğinden doğan yeni edebi formlar görebiliriz. İnteraktif hikâyeler, kişiselleştirilmiş anlatılar, çok dilli eş zamanlı edebiyat... Olasılıklar sınırsız. Ama bir şey kesin: İnsan ruhunun derinliklerinden gelen bir şiirin yerini hiçbir algoritma alamaz.</p>',
                'meta_title'       => 'Yapay Zekâ ve Edebiyatın Geleceği',
                'meta_description' => 'Yapay zekâ edebiyatı nasıl dönüştürüyor? İnsan yaratıcılığı ile makine zekâsının kesişim noktası.',
                'is_active'        => true,
                'sort_order'       => 2,
            ],
            [
                'title'            => 'AI Destekli Yaratıcı Yazarlık',
                'slug'             => 'ai-destekli-yaratici-yazarlik',
                'excerpt'          => 'Yapay zekâyı yazarlık sürecinize nasıl dahil edebilirsiniz? Pratik rehber ve ipuçları.',
                'body'             => '<h2>Yapay Zekâ ile Yazma Süreci</h2><p>Yapay zekâ araçları, yazarların yaratıcı süreçlerini zenginleştirmek için güçlü bir potansiyele sahip. Ama bu araçları verimli kullanmak, onları doğru konumlandırmayı gerektirir.</p><h2>Beyin Fırtınası Partneri</h2><p>Yazarın karşılaştığı en büyük engellerden biri, boş sayfa sendromudur. AI araçları bu noktada harika bir beyin fırtınası ortağı olabilir. «Bir balıkçı köyünde geçen gotik bir hikâye için 10 farklı açılış cümlesi öner» gibi talepler, yaratıcı sürecin kilidini açabilir.</p><h2>Araştırma Asistanı</h2><p>Tarihî bir roman yazıyorsanız, dönemin giyim kuşamından yemek kültürüne, mimari detaylardan günlük yaşam pratiklerine kadar araştırmanız gereken çok şey var. AI, bu araştırma sürecini hızlandırarak yazara daha çok «yazma» zamanı kazandırır.</p><h2>Dil ve Üslup Koçu</h2><p>Farklı yazım tarzlarını denemek, cümle yapılarını çeşitlendirmek, kelime hazinesini genişletmek — AI bu konularda anlık geri bildirim sunabilir. Ama unutmayın: Sizin sesiniz, sizin üslubunuz eşsizdir. AI\'ı bir ayna olarak kullanın, sesinizin yerine değil.</p><h2>Düzenleme ve Revizyon</h2><p>İlk taslağı yazdıktan sonra, AI tutarsızlıkları bulmada, tempo sorunlarını tespit etmede ve diyalogların doğallığını değerlendirmede yardımcı olabilir. Ama son karar her zaman yazarın olmalıdır.</p><h2>Altın Kural</h2><p>AI bir yardımcıdır, yazar değil. En iyi sonuçlar, insan yaratıcılığını merkeze koyup teknolojiyi destek olarak kullanan yaklaşımdan doğar. Çünkü okuyucunun aradığı şey, bir algoritmanın çıktısı değil, bir insanın yüreğinden geçenlerdir.</p>',
                'meta_title'       => 'AI Destekli Yaratıcı Yazarlık — Rehber',
                'meta_description' => 'Yapay zekâyı yazarlık sürecinize nasıl entegre edebilirsiniz? Pratik öneriler ve kullanım rehberi.',
                'is_active'        => true,
                'sort_order'       => 3,
            ],
            [
                'title'            => 'Dijital Edebiyat: Ekrandan Kalbe',
                'slug'             => 'dijital-edebiyat-ekrandan-kalbe',
                'excerpt'          => 'Dijital platformlarda edebiyat nasıl tüketiliyor? E-kitaptan podcast\'e, sosyal medyadan blog\'a.',
                'body'             => '<h2>Edebiyatın Dijital Dönüşümü</h2><p>Gutenberg\'in matbaası nasıl bir devrim yarattıysa, internet de edebiyat için benzer bir kırılma noktasıdır. Bugün bir şiir, yazıldığı andan itibaren saniyeler içinde dünya genelinde milyonlarca insana ulaşabilir.</p><h2>E-Kitap Devrimi</h2><p>E-kitaplar, okuma alışkanlıklarını kökten değiştirdi. Artık tek bir cihazda binlerce kitap taşınabiliyor. Kindle, Kobo gibi platformlar, bağımsız yazarlara kendi eserlerini yayımlama imkânı sunuyor. Bu demokratikleşme, edebiyat dünyasına yeni sesler kazandırdı.</p><h2>Sesli Kitaplar ve Podcast\'ler</h2><p>Sesli kitap pazarı her yıl büyüyor. İnsanlar araba sürerken, spor yaparken, ev işi yaparken edebiyatla buluşuyor. Edebiyat podcast\'leri ise kitap incelemeleri, yazar söyleşileri ve edebi tartışmalarla yeni bir kitle oluşturuyor.</p><h2>Sosyal Medyada Edebiyat</h2><p>Instagram\'da şiir paylaşan hesaplar milyonlarca takipçiye ulaşıyor. TikTok\'ta #BookTok hareketi, genç nesli okumaya yönlendiriyor. Twitter\'da kısa hikâye akımları, 280 karakterde edebiyat yapmanın mümkün olduğunu gösteriyor.</p><h2>Blog ve Çevrimiçi Dergiler</h2><p>Boyalı Kelimeler gibi platformlar, geleneksel edebiyat dergilerinin dijital karşılığı olarak işlev görüyor. Editöryal süzgeçten geçen kaliteli içerik, dijital ortamda okuyucuyla buluşuyor.</p><h2>Geleceğe Bakış</h2><p>Sanal gerçeklik edebiyatı, interaktif hikâyeler, AI destekli kişiselleştirilmiş anlatılar... Dijital edebiyatın geleceği heyecan verici. Ama özünde değişmeyen bir şey var: İyi bir hikâyenin gücü, hangi platformda olursa olsun, kalplere dokunmaya devam ediyor.</p>',
                'meta_title'       => 'Dijital Edebiyat: Ekrandan Kalbe',
                'meta_description' => 'Dijital platformlarda edebiyatın dönüşümü. E-kitap, podcast, sosyal medya ve çevrimiçi dergiler.',
                'is_active'        => true,
                'sort_order'       => 4,
            ],
            [
                'title'            => 'Gizlilik Politikası',
                'slug'             => 'gizlilik-politikasi',
                'excerpt'          => 'Boyalı Kelimeler gizlilik politikası ve kişisel verilerin korunması hakkında bilgilendirme.',
                'body'             => '<h2>Gizlilik Politikası</h2><p>Boyalı Kelimeler olarak kişisel verilerinizin korunmasına büyük önem veriyoruz. Bu politika, sitemizi kullanırken toplanan bilgilerin nasıl işlendiğini açıklamaktadır.</p><h3>Toplanan Bilgiler</h3><p>Sitemize kayıt olduğunuzda ad-soyad, e-posta adresi gibi temel bilgilerinizi topluyoruz. Ayrıca çerezler aracılığıyla site kullanım verilerinizi anonim olarak analiz ediyoruz.</p><h3>Bilgilerin Kullanımı</h3><p>Toplanan bilgiler yalnızca hizmet kalitesini artırmak, size kişiselleştirilmiş içerik sunmak ve yasal yükümlülüklerimizi yerine getirmek amacıyla kullanılmaktadır.</p><h3>Bilgi Güvenliği</h3><p>Kişisel verileriniz, endüstri standartlarına uygun güvenlik önlemleriyle korunmaktadır. SSL şifreleme, güvenli sunucular ve düzenli güvenlik denetimleri ile verilerinizin güvenliğini sağlıyoruz.</p><h3>Üçüncü Taraflarla Paylaşım</h3><p>Kişisel verileriniz, yasal zorunluluklar dışında üçüncü taraflarla paylaşılmaz. Analitik hizmetleri için anonim veriler kullanılabilir.</p><h3>Haklarınız</h3><p>KVKK kapsamında kişisel verilerinize erişim, düzeltme, silme ve taşıma haklarına sahipsiniz. Bu haklarınızı kullanmak için iletisim@boyalikelimeler.com adresine e-posta gönderebilirsiniz.</p>',
                'meta_title'       => 'Gizlilik Politikası — Boyalı Kelimeler',
                'meta_description' => 'Boyalı Kelimeler gizlilik politikası. Kişisel verilerinizin nasıl toplandığı ve korunduğu hakkında bilgi.',
                'is_active'        => true,
                'sort_order'       => 5,
            ],
            [
                'title'            => 'Kullanım Koşulları',
                'slug'             => 'kullanim-kosullari',
                'excerpt'          => 'Boyalı Kelimeler platformunu kullanırken uyulması gereken kurallar ve koşullar.',
                'body'             => '<h2>Kullanım Koşulları</h2><p>Boyalı Kelimeler platformunu kullanarak aşağıdaki koşulları kabul etmiş sayılırsınız. Lütfen bu koşulları dikkatlice okuyunuz.</p><h3>Üyelik</h3><p>Sitemize üye olmak ücretsizdir. Üyelik sırasında verdiğiniz bilgilerin doğru ve güncel olması gerekmektedir. Hesabınızın güvenliğinden siz sorumlusunuz.</p><h3>İçerik Paylaşımı</h3><p>Platformda paylaştığınız tüm içeriklerin telif haklarına sahip olmanız veya paylaşım izninin bulunması gerekmektedir. Başkalarına ait eserleri izinsiz paylaşmak yasaktır.</p><h3>Fikri Mülkiyet</h3><p>Boyalı Kelimeler platformundaki tasarım, logo, yazılım ve özgün içerikler telif hakkı ile korunmaktadır. İzinsiz kullanım, kopyalama veya dağıtım yasaktır.</p><h3>Kullanıcı Davranışları</h3><p>Platformda hakaret, ayrımcılık, nefret söylemi ve yasalara aykırı içerik paylaşmak kesinlikle yasaktır. Bu kuralları ihlal eden hesaplar uyarı yapılmaksızın kapatılabilir.</p><h3>Sorumluluk Sınırı</h3><p>Boyalı Kelimeler, kullanıcılar tarafından paylaşılan içeriklerin doğruluğunu garanti etmez. Platform, üçüncü taraf bağlantılardan kaynaklanan zararlardan sorumlu tutulamaz.</p><h3>Değişiklikler</h3><p>Bu kullanım koşulları önceden bildirim yapılmaksızın güncellenebilir. Güncel koşulları düzenli olarak kontrol etmenizi öneririz.</p>',
                'meta_title'       => 'Kullanım Koşulları — Boyalı Kelimeler',
                'meta_description' => 'Boyalı Kelimeler platformunun kullanım koşulları ve kuralları.',
                'is_active'        => true,
                'sort_order'       => 6,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['slug' => $page['slug']],
                array_merge($page, [
                    'user_id' => $user->id,
                ])
            );
        }
    }
}
