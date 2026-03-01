# Boyalı Kelimeler — Frontend Tema

**Sosyal Çöküntüye Sanatsal Direniş**

Boyalı Kelimeler, sanat ve edebiyatın buluştuğu bir topluluk platformunun frontend HTML temasıdır.
Bu repo, Laravel 12 projesine entegre edilmek üzere hazırlanmış statik HTML/CSS/JS dosyalarını içerir.
Tüm sayfalar Blade template'e dönüştürülerek kullanılacaktır.

---

## Teknoloji Stack

| Teknoloji | Versiyon | Kullanım |
|-----------|----------|----------|
| Bootstrap | 5.3.8 | CSS framework (CDN) |
| Font Awesome | 6.x | İkon seti (CDN) |
| Swiper.js | 11.x | Slider/carousel (CDN) |
| AOS.js | 2.3.4 | Scroll animasyonları (CDN) |
| Vanilla JS | ES6+ | Etkileşimler (jQuery yok) |

> **Not:** Vite, npm, Webpack gibi build araçları kullanılmaz. Tüm kütüphaneler CDN üzerinden yüklenir.

---

## Renk Paleti

| Değişken | Renk | Kullanım |
|----------|------|----------|
| `--color-gold` | `#c8a96e` | Ana vurgu rengi, başlıklar, butonlar |
| `--color-cream` | `#f5f0e8` | Metin rengi |
| `--color-silver` | `#a0a0a0` | İkincil metin |
| `--color-black-deep` | `#0a0a0a` | Sayfa arka planı |
| `--color-black-card` | `#111111` | Kart arka planları |

---

## Proje Yapısı

```
boyalikelimeler.com-theme/
├── public/
│   ├── css/
│   │   └── app.css              # Ana stil dosyası (tüm front sayfalar)
│   ├── js/
│   │   └── app.js               # Ana JavaScript dosyası (tüm front sayfalar)
│   └── images/
│       └── logo.svg             # Site logosu (SVG)
├── index.html                   # Ana sayfa
├── blog.html                    # Blog listeleme sayfası
├── blog-detay.html              # Blog yazı detay sayfası
├── icerikler.html               # İçerik listeleme sayfası
├── icerik-detay.html            # İçerik detay sayfası
├── hakkimizda.html              # Hakkımızda sayfası
├── iletisim.html                # İletişim formu sayfası
├── login.html                   # Giriş yap sayfası
├── register.html                # Kayıt ol sayfası
├── sifremi-unuttum.html         # Şifremi unuttum sayfası
├── sifre-yenile.html            # Şifre yenileme sayfası
├── profile.html                 # Yazar profil sayfası
├── profile-edit.html            # Profil düzenleme sayfası
├── istatistiklerim.html         # Kullanıcı istatistikleri sayfası
├── yazi-gonder.html             # Yazı gönderme formu
├── yazilarim.html               # Kullanıcının yazıları yönetim sayfası
├── soz-meydani.html             # Söz Meydanı (forum) ana sayfası
├── soz-meydani-kategori.html    # Forum kategori sayfası
├── soz-meydani-soru.html        # Forum soru detay sayfası
├── yoldaslar.html               # Yönetim ekibi sayfası
├── yoldaslar-sanat-ekibi.html   # Sanat ekibi alt sayfası
├── CLAUDE.md                    # Claude Code geliştirme kuralları
├── .gitignore                   # Git ignore kuralları
└── README.md                    # Bu dosya
```

---

## Sayfa Açıklamaları

### Ana Sayfalar

#### `index.html` — Ana Sayfa
Platformun giriş sayfası. Hero slider, kategori kartları (Altın Kalem, Altın Fırça, Dergimiz, Astroloji),
yazı listesi, günün sözü, anket, film önerisi, video galerisi ve mini CTA kutuları içerir.
Tüm bölümler AOS animasyonları ile desteklenir.

#### `hakkimizda.html` — Hakkımızda
Boyalı Kelimeler'in misyonu, vizyonu ve "Sosyal Çöküntüye Sanatsal Direniş" manifestosunu sunar.
Platform hakkında detaylı bilgi ve ekip tanıtımı içerir.

#### `iletisim.html` — İletişim
Ziyaretçilerin soru, öneri ve iş birliği talepleri için kullanacağı iletişim formu sayfası.
Ad, e-posta, konu ve mesaj alanları içerir.

---

### Blog Modülü

#### `blog.html` — Blog Listeleme
Sanat, edebiyat, kültür ve etkinlik kategorilerindeki blog yazılarını listeler.
Kart tabanlı grid düzeninde, her kartta kapak görseli, başlık, özet ve tarih bilgisi bulunur.

#### `blog-detay.html` — Blog Yazı Detayı
Tek bir blog yazısının tam içeriğini gösterir. Yazar bilgisi, yayın tarihi, okunma süresi,
sosyal paylaşım butonları, etiketler ve ilgili yazılar bölümü içerir.

---

### İçerik Modülü

#### `icerikler.html` — İçerik Listeleme
Kullanıcıların gönderdiği şiir, hikaye, deneme ve roman gibi edebi içerikleri listeler.
Kategori filtreleme ve sıralama seçenekleri mevcuttur.

#### `icerik-detay.html` — İçerik Detayı
Edebi bir içeriğin tam metnini gösterir. Yazar profil kartı, beğeni/yorum sistemi,
benzer içerikler önerileri ve sosyal paylaşım butonları içerir.

---

### Kimlik Doğrulama (Auth) Sayfaları

#### `login.html` — Giriş Yap
Kullanıcı giriş formu. E-posta ve şifre alanları, "Beni hatırla" seçeneği
ve sosyal giriş alternatifleri içerir. Kayıt ol ve şifremi unuttum bağlantıları mevcut.

#### `register.html` — Kayıt Ol
Yeni kullanıcı kayıt formu. Ad, kullanıcı adı, e-posta, şifre alanları
ve kullanım koşulları onayı içerir.

#### `sifremi-unuttum.html` — Şifremi Unuttum
E-posta adresi girerek şifre sıfırlama bağlantısı isteyen sayfa.
Kullanıcıya e-posta ile sıfırlama linki gönderilir.

#### `sifre-yenile.html` — Şifre Yenile
E-posta ile gelen sıfırlama linkinden sonra yeni şifre belirleme formu.
Yeni şifre ve şifre onayı alanları içerir.

---

### Kullanıcı Profil Sayfaları

#### `profile.html` — Yazar Profili
Bir yazarın herkese açık profil sayfası. Profil fotoğrafı, biyografi, istatistikler
(yazı sayısı, okunma, beğeni), yazıları, şiirleri ve sanat eserleri tab'lar halinde listelenir.

#### `profile-edit.html` — Profil Düzenle
Kullanıcının kendi profil bilgilerini güncelleyebildiği sayfa. Ad, biyografi,
sosyal medya bağlantıları ve profil fotoğrafı değiştirme alanları içerir.
**noindex** — arama motorlarından gizli.

#### `istatistiklerim.html` — İstatistiklerim
Kullanıcının kendi yazılarının okunma istatistiklerini gösteren dashboard sayfası.
Aylık, haftalık ve günlük grafiklere sahiptir.
**noindex** — arama motorlarından gizli.

---

### Yazı Yönetimi

#### `yazi-gonder.html` — Yazı Gönder
Kullanıcıların şiir, hikaye, deneme veya roman türünde içerik gönderebildiği form sayfası.
Başlık, kategori seçimi (Tom Select kütüphanesi), metin editörü ve kapak görseli yükleme alanları içerir.
**noindex** — arama motorlarından gizli.

#### `yazilarim.html` — Yazılarım
Kullanıcının gönderdiği tüm yazıları listelediği ve yönetebildiği sayfa.
Onay durumu (beklemede, yayında, reddedildi), düzenleme ve silme işlemleri mevcut.
**noindex** — arama motorlarından gizli.

---

### Söz Meydanı (Forum)

#### `soz-meydani.html` — Söz Meydanı Ana Sayfa
Soru-cevap ve tartışma platformunun ana sayfası. Edebiyat, sanat, psikoloji, astroloji
gibi kategoriler ve popüler sorular listelenir.

#### `soz-meydani-kategori.html` — Kategori Sayfası
Belirli bir kategoriye ait soruların listelendiği sayfa. Filtreleme ve sıralama
seçenekleri, her soruda cevap sayısı ve görüntülenme bilgisi bulunur.

#### `soz-meydani-soru.html` — Soru Detay Sayfası
Tek bir sorunun tüm cevaplarıyla birlikte gösterildiği sayfa.
Soruyu beğenme, cevap yazma, en iyi cevabı seçme işlevleri içerir.

---

### Ekip Sayfaları

#### `yoldaslar.html` — Yönetim Ekibi
Platform yöneticilerini, sanat koordinatörlerini ve yaratıcı ekibi tanıtan sayfa.
Kart tabanlı düzen ile her üyenin fotoğrafı, unvanı ve kısa biyografisi gösterilir.

#### `yoldaslar-sanat-ekibi.html` — Sanat Ekibi
Sanat ekibinin detaylı tanıtımını yapan alt sayfa. Koordinatörler, küratörler
ve sanat yönetmenlerinin profil kartları yer alır.

---

## Teknik Dosyalar

### `public/css/app.css` — Ana Stil Dosyası
Tüm frontend sayfaları için kullanılan CSS dosyasıdır. BEM metodolojisi ile yazılmıştır.

**İçerik:**
- CSS değişkenleri (renkler, fontlar, gölgeler, kenarlıklar, geçişler)
- Navbar ve mega menü stilleri
- Hero slider bileşeni
- Kart bileşenleri (category-card, mini-cta, creative-grid vb.)
- Blog ve içerik listeleme/detay stilleri
- Form stilleri (login, register, iletişim, yazı gönder)
- Profil sayfası ve istatistik dashboard stilleri
- Söz Meydanı forum stilleri
- Footer bileşeni
- Responsive breakpoint'ler (mobile-first yaklaşım)

### `public/js/app.js` — Ana JavaScript Dosyası
Tüm frontend sayfaları için kullanılan vanilla JavaScript dosyasıdır. jQuery kullanılmaz.

**İçerik:**
- Navbar scroll efekti (sayfa kaydırıldığında arka plan değişimi)
- Mega menü toggle (mobil ve desktop)
- Swiper.js slider başlatma
- AOS.js animasyon başlatma
- Video modal aç/kapat
- Söz Meydanı oy verme etkileşimleri
- Form doğrulama yardımcıları

### `public/images/logo.svg` — Site Logosu
Boyalı Kelimeler markasının SVG formatındaki vektörel logosudur.
Navbar ve footer'da kullanılır.

### `CLAUDE.md` — Geliştirme Kuralları
Claude Code AI ile geliştirme yaparken uyulması gereken kuralları içerir:
- Teknoloji stack kuralları (Laravel 12, PHP 8.3, Bootstrap 5.3.8)
- Yasaklı teknolojiler (Vite, npm, jQuery, React vb.)
- Kodlama standartları (PSR-12, strict types, eager loading)
- Güvenlik gereksinimleri (CSRF, XSS koruması, rate limiting)
- SEO gereksinimleri (meta tags, semantic HTML)
- Dosya yükleme kuralları (UploadService)
- Admin ve front CSS/JS ayrımı

### `.gitignore` — Git Ignore
Versiyon kontrolünden hariç tutulan dosyaları belirler:
- `.env` dosyaları, log dosyaları, IDE konfigürasyonları
- `vendor/`, `node_modules/` dizinleri
- `public/uploads/*` (`.gitkeep` hariç)
- OS dosyaları (`.DS_Store`, `Thumbs.db`)

---

## Laravel'e Dönüştürme Rehberi

Bu tema Laravel 12 projesine şu şekilde entegre edilecektir:

1. **Layout:** Navbar ve footer `resources/views/layouts/front.blade.php` dosyasına taşınır
2. **Sayfalar:** Her HTML dosyası `resources/views/front/` altında Blade template'e dönüştürülür
3. **Partials:** Tekrar eden bileşenler (kart, breadcrumb vb.) `resources/views/components/` altına alınır
4. **CSS/JS:** `public/css/` ve `public/js/` dizinleri aynen korunur (Vite kullanılmaz)
5. **Görseller:** `public/images/` dizini aynen korunur, kullanıcı yüklemeleri `public/uploads/` altına yapılır
6. **Rotalar:** Her sayfa için `routes/web.php`'de rota tanımlanır

---

## Tasarım Prensipleri

- **Mobile-first:** Önce mobil tasarım, sonra büyük ekranlar
- **BEM CSS:** Block-Element-Modifier metodolojisi (`category-card__title--active`)
- **Semantic HTML:** `nav`, `main`, `article`, `section`, `aside`, `footer` etiketleri
- **Erişilebilirlik:** Skip-to-content linkleri, aria-label'lar, alt text'ler
- **SEO:** Her sayfada title, meta description, canonical URL, Open Graph tags
- **Performans:** Lazy loading görseller, CDN kütüphaneler, minimal CSS/JS

---

## Lisans

Bu tema Boyalı Kelimeler platformuna özeldir. Tüm hakları saklıdır.

Yazılım ve Tasarım: [Ozan SONAR](https://ozansonar.net/)
