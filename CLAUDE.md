# Proje Kuralları

Sen kıdemli bir Laravel fullstack geliştiricisisin. Türkçe iletişim kur,
kod yorumları ve değişken isimleri İngilizce olsun.

## Stack

- PHP 8.3.30 / Laravel 12 / Blade / MySQL 8 / Bootstrap 5.3.8 CDN / Vanilla JS

## Tasarım Sadakati

- Kullanıcının verdiği orijinal tasarıma **BİREBİR** uyulmalı → ZORUNLU
- Tasarımda olmayan eleman ekleme → YASAK
- Tasarımda olan elemanı kaldırma veya değiştirme → YASAK
- Kendi inisiyatifle tasarım kararı alma → YASAK
- Şüphe durumunda kullanıcıya sor, kafana göre iş yapma

## Kırmızı Çizgiler

- Vite, npm, Node.js, Webpack → YASAK
- jQuery, React, Vue, Angular, Livewire, Inertia → YASAK
- Inline style (`style="..."`) → YASAK, her zaman class kullan
- Duplicate kod → YASAK, component/partial yap
- SoftDeletes → HER MODELDE ZORUNLU
- N+1 query → YASAK, eager loading kullan
- Controller'da iş mantığı → YASAK, Service katmanında yaz
- `$guarded = []` → YASAK, `$fillable` tanımla
- `declare(strict_types=1);` → her PHP dosyasında ZORUNLU

## Kodlama

- PSR-12 / FormRequest validation / Thin controllers
- PHP 8.3: typed properties, enums, readonly, match, null safe
- Fetch API (AJAX) / Bootstrap utility-first / BEM CSS / Mobile-first responsive
- Migration'da `down()` her zaman yaz / Index ekle / `DB::transaction()`

## Güvenlik

- CSRF her formda ve AJAX'ta / `{{ }}` escaped output / Policy authorization
- Hassas bilgiler `.env`'de / Rate limiting / Prepared statements

## Performans

- `Cache::remember()` / Pagination / Bulk insert / `exists()` not `count()`
- Görseller: `loading="lazy"` `img-fluid` WebP / JS body sonunda

### DB Sorgu Kuralları
- Blade view içinde doğrudan model/service çağrısı (`Model::where()`, `app(Service::class)`) → YASAK
- Layout/sidebar/header/footer gibi her sayfada render edilen parçalarda DB sorgusu → YASAK
- Bu verileri **View Composer** (`AppServiceProvider`) üzerinden ver, cache'li service metotlarını kullan
- Aynı veri bir request içinde 2+ kez çekiliyorsa → `Cache::remember()` veya değişkende tut
- Sayaç/badge sorguları (pending count, unread count vb.) → ZORUNLU cache + invalidation
- Bir controller'da aynı service metodu 3+ kez çağrılıyorsa → toplu çeken metot yaz (ör: `getAllGrouped()`)
- Service'te veri değiştiren her metot (store/update/delete) → ilgili count cache'i temizlemeli

## Git

- Türkçe commit: `[feat]: açıklama` / Tipler: feat, fix, refactor, style, docs, test

## SEO
- Her sayfada: title, meta description, canonical URL, Open Graph tags
- Semantic HTML: nav, main, article, section, aside, footer
- Görsellerde anlamlı alt text, heading hiyerarşisi (h1 > h2 > h3 sıralı)

## Admin Tema Kullanımı

- `resources/views/admin-theme/` dizininde hazır HTML tasarımlar mevcut → ZORUNLU kullan
- Yeni admin sayfası yaparken önce `admin-theme/README.md` dosyasını oku
- README'deki **Sidebar Full Navigation Tree** bölümünde hangi sayfa hangi HTML dosyasına karşılık geliyor yazıyor
- İlgili `.html` dosyasını bul ve **BİREBİR** Blade'e dönüştür
- HTML tasarımdaki CSS class'ları, yapıyı, section'ları, ikonları aynen koru
- Tasarımda kullanılan CSS class'ları `public/assets/admin/css/styles.css`'de yoksa `admin-theme/styles.css`'den bulup ekle
- Tasarımda kullanılan JS dosyaları varsa (ör: `product-add.js`) `public/assets/admin/js/` altına Laravel'e uyarlanmış halini ekle
- Kendi kafana göre standart form yapma → YASAK, her zaman tema dosyasını referans al
- **Detaylı rehber:** `admin-panel` skill'inde → CSS prefix sistemi, UI pattern'leri, sayfa oluşturma adımları, dikkat noktaları

## Dosya Yükleme (Upload)

- Tüm dosya yüklemeleri **`public/uploads/`** dizinine yapılır → ZORUNLU
- `Storage::disk('public')` veya `storage/` dizini → YASAK, kullanılmaz
- Tüm yükleme işlemleri **`App\Services\UploadService`** üzerinden yapılır → ZORUNLU
- Controller veya başka serviste doğrudan `$file->move()` / `$file->store()` → YASAK

### Dosya Adlandırma
- Format: `{slug}-{YmdHis}-{uniq5}.webp`
- `{slug}` → İçerik başlığının slugify hali (varsa), yoksa yükleyen kullanıcının adı-soyadı slugify
- `{YmdHis}` → Yükleme tarihi (ör: 20260303143025)
- `{uniq5}` → 5 karakterlik rastgele unique key
- Örnek: `kahve-falinda-gorunen-sehir-20260303143025-a7xk2.webp`

### Görsel İşleme
- Orijinal dosya **`originals/`** alt dizininde saklanır (orijinal format korunur)
- WebP dönüşümü → ZORUNLU (GD ile)
- Responsive varyantlar → ZORUNLU:
  - `thumb` → 150x150 (kare, crop)
  - `sm` → max 480px genişlik
  - `md` → max 768px genişlik
  - `lg` → max 1200px genişlik
- Varyant adlandırma: `{slug}-{YmdHis}-{uniq5}-{size}.webp`

### Görsel Gösterim
- View'lerde: `<x-responsive-image>` Blade component'i → ZORUNLU
- Fallback: `upload_url($path, $size)` helper
- `asset('storage/...')` → YASAK
- Her `<img>` etiketinde `loading="lazy"`, `img-fluid`, anlamlı `alt` → ZORUNLU

### UploadService Metotları
- `uploadImage(UploadedFile $file, string $directory, ?string $slug = null): string`
- `replaceImage(UploadedFile $file, string $directory, ?string $oldPath, ?string $slug = null): string`
- `deleteImage(?string $path): void` → Orijinal + tüm varyantları siler
- `url(string $path, ?string $size = null): string`

## Dosya Ayrımı (Front vs Admin)

- Front ve admin CSS/JS dosyaları **TAMAMEN AYRI** → aynı dosya iki taraf için KULLANILMAZ
- **Admin CSS:** `public/assets/admin/css/styles.css`
- **Admin JS:** `public/assets/admin/js/app.js` + sayfa özel JS'ler `public/assets/admin/js/` altında
- **Front CSS:** `public/css/` altında
- **Front JS:** `public/js/` altında
- Admin layout: `{{ asset('assets/admin/css/styles.css') }}` ve `{{ asset('assets/admin/js/app.js') }}`
- Front layout: `{{ asset('css/app.css') }}` ve `{{ asset('js/app.js') }}`

