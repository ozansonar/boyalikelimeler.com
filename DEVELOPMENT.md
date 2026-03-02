# Boyalı Kelimeler - Geliştirme Dokümantasyonu

Bu dosya, projenin geliştirme sürecini, yapılan işleri, mimari kararları ve planlanan geliştirmeleri kayıt altına alır.

---

## Proje Bilgileri

| Bilgi | Değer |
|-------|-------|
| **Proje** | Boyalı Kelimeler - Edebiyat & Sanat Platformu |
| **Stack** | PHP 8.3.30, Laravel 12, Blade, MySQL 8, Bootstrap 5.3.8 CDN, Vanilla JS |
| **Domain** | boyalikelimeler.com |
| **Başlangıç Tarihi** | Mart 2026 |

---

## Mimari Kararlar

### Katmanlı Yapı (Service Layer Pattern)
- **Controller** → Sadece request/response yönetimi, iş mantığı YASAK
- **Service** → Tüm iş mantığı burada, caching, DB işlemleri
- **Model** → Eloquent ilişkileri, scope'lar, accessor/mutator'lar
- **FormRequest** → Validation kuralları

### Dosya Yükleme
- Tüm dosyalar `public/uploads/` dizinine yüklenir
- `UploadService` ile WebP dönüşümü ve responsive varyantlar (thumb, sm, md, lg)
- `upload_url($path, $size)` helper ile URL oluşturma

### Admin Tema Sistemi
- `resources/views/admin-theme/` altında 47 referans HTML dosyası
- Her sayfanın kendine ait CSS prefix'i var (usr-, stg-, cl-, ca- vb.)
- HTML tasarım **birebir** Blade'e dönüştürülür
- CSS: `public/assets/admin/css/styles.css`
- JS: `public/assets/admin/js/` altında sayfa bazlı dosyalar

### Front vs Admin Ayrımı
- Admin CSS/JS: `public/assets/admin/`
- Front CSS/JS: `public/css/` ve `public/js/`
- İki taraf birbirinden tamamen bağımsız

---

## Tamamlanan Modüller

### 1. Kimlik Doğrulama (Authentication)

**Dosyalar:**
- `app/Services/AuthService.php` — Giriş/çıkış iş mantığı
- `app/Http/Controllers/Auth/` — Login, register, forgot password controller'ları
- `resources/views/auth/` — Login, register, forgot password Blade'leri

**Özellikler:**
- Kullanıcı girişi/çıkışı
- Şifre sıfırlama
- Rol tabanlı yetkilendirme (admin, editor, author, user)

---

### 2. Kullanıcı Yönetimi (Users)

**Dosyalar:**
- `app/Models/User.php` — Kullanıcı modeli (SoftDeletes)
- `app/Models/Role.php` — Rol modeli
- `app/Services/UserService.php` — Kullanıcı CRUD, istatistik, filtreleme
- `app/Http/Controllers/Admin/UserController.php` — Admin CRUD controller
- `resources/views/admin/users/` — index, create, edit, _form Blade'leri
- `public/assets/admin/js/users.js` — Counter animasyon, silme modal, toplu işlem

**Veritabanı:**
- `users` tablosu — name, email, password, role_id, avatar, bio, is_active
- `roles` tablosu — name, slug, description, is_active

**Özellikler:**
- Kullanıcı listeleme (durum sekmeleri, arama, rol filtresi, sayfalama)
- Kullanıcı ekleme/düzenleme/silme
- Rol atama (Admin, Editör, Yazar, Kullanıcı)
- İstatistik kartları (toplam, aktif, pasif, admin sayısı)
- Avatar yükleme (WebP dönüşümü)

---

### 3. Kategori Yönetimi (Categories)

**Dosyalar:**
- `app/Models/Category.php` — Kategori modeli (SoftDeletes)
- `app/Services/CategoryService.php` — CRUD, istatistik, slug oluşturma
- `app/Http/Controllers/Admin/CategoryController.php` — Admin CRUD
- `resources/views/admin/categories/` — index, create, edit, _form
- `database/seeders/CategorySeeder.php` — 5 varsayılan kategori

**Veritabanı:**
- `categories` tablosu — name, slug, description, image, color, sort_order, is_active

**Seed Verileri (5 kategori):**
1. Şiir
2. Öykü
3. Deneme
4. Sanat
5. Edebiyat Dünyası

---

### 4. İçerik Yönetimi (Posts / Blog)

**Dosyalar:**
- `app/Models/Post.php` — Post modeli (SoftDeletes), ilişkiler: user, category
- `app/Services/PostService.php` — CRUD, istatistik, slug, durum filtreleme
- `app/Http/Controllers/Admin/PostController.php` — Admin CRUD
- `resources/views/admin/posts/` — index, create, edit, _form
- `public/assets/admin/js/blog-list.js` — Liste sayfası JS
- `public/assets/admin/js/content-add.js` — İçerik ekleme/düzenleme JS
- `database/seeders/PostSeeder.php` — 10 örnek içerik

**Veritabanı:**
- `posts` tablosu — title, slug, excerpt, content, featured_image, category_id, user_id, status (draft/published/archived), is_featured, view_count, published_at, meta_title, meta_description

**Tema Referansları:**
- Liste: `content-list.html` (cl- prefix)
- Ekleme/Düzenleme: `content-add.html` (ca- prefix)

**Seed Verileri (10 içerik, her kategoriden 2'şer):**
1. Gecenin Sessiz Çığlığı (Şiir)
2. Baharın İlk Sözü (Şiir)
3. Son Tren (Öykü)
4. Kitapçının Kedisi (Öykü)
5. Dijital Çağda Yalnızlık (Deneme)
6. Zamanın Kıyısında Durmak (Deneme)
7. Van Gogh'un Yıldızlı Gecesi (Sanat)
8. Türk Sinemasında Yeni Dalga (Sanat)
9. Orhan Pamuk'un Edebi Evreni (Edebiyat Dünyası)
10. Genç Yazarlara Öneriler (Edebiyat Dünyası)

---

### 5. Sayfa Yönetimi (Pages)

**Dosyalar:**
- `app/Models/Page.php` — Sayfa modeli (SoftDeletes)
- `app/Services/PageService.php` — CRUD, istatistik, slug oluşturma
- `app/Http/Controllers/Admin/PageController.php` — Admin CRUD
- `resources/views/admin/pages/` — index, create, edit, _form
- `database/seeders/PageSeeder.php` — 7 varsayılan sayfa

**Veritabanı:**
- `pages` tablosu — title, slug, content, featured_image, template, status (draft/published), sort_order, is_in_footer, meta_title, meta_description

**Seed Verileri (7 sayfa):**
1. Hakkımızda
2. İletişim
3. Yapay Zeka ve Edebiyatın Geleceği
4. AI Destekli Yaratıcı Yazarlık
5. Dijital Edebiyat: Ekrandan Kalbe
6. Gizlilik Politikası
7. Kullanım Koşulları

---

### 6. Menü Yönetimi (Menus)

**Dosyalar:**
- `app/Models/Menu.php` — Menü modeli (SoftDeletes)
- `app/Models/MenuItem.php` — Menü öğesi modeli (SoftDeletes), self-referencing parent/children
- `app/Services/MenuService.php` — CRUD, konum bazlı getirme, sıralama, cache
- `app/Http/Controllers/Admin/MenuController.php` — Menü CRUD + öğe yönetimi
- `resources/views/admin/menus/` — index, create, edit, items, _menu_form
- `database/seeders/MenuSeeder.php` — Header, footer menüleri varsayılan öğelerle

**Veritabanı:**
- `menus` tablosu — name, slug, location, description, is_active
- `menu_items` tablosu — menu_id, parent_id, title, url, target, icon, sort_order, is_active

**Özellikler:**
- Menü oluşturma/düzenleme/silme
- Menü öğesi ekleme/düzenleme/silme
- Sürükle-bırak ile sıralama (Sortable.js)
- Hiyerarşik yapı (parent/child)
- Konum bazlı menü atama (header, footer_discover, footer_competitions, footer_corporate)
- View Composer ile otomatik menü yükleme (`layouts.front`)

---

### 7. Site Ayarları (Settings)

**Dosyalar:**
- `app/Models/Setting.php` — Key-value ayar modeli
- `app/Services/SettingService.php` — Get/set, grup bazlı okuma/yazma, cache (1 saat)
- `app/Http/Controllers/Admin/SettingController.php` — 6 grup güncelleme + SMTP test
- `resources/views/admin/settings/index.blade.php` — 6 sekmeli ayar sayfası
- `public/assets/admin/js/settings.js` — Sekme geçişi, önizleme, SEO counter
- `database/seeders/SettingSeeder.php` — 35 varsayılan ayar

**Veritabanı:**
- `settings` tablosu — group, key, value (unique: group+key)

**Tema Referansı:** `settings.html` (stg- prefix)

**6 Ayar Grubu ve İçerikleri:**

#### a) Genel (general)
- Site adı, site açıklaması, site URL'si
- Logo yükleme (WebP dönüşümü)
- Favicon yükleme (WebP dönüşümü)

#### b) İletişim (contact)
- E-posta adresi
- Telefon numarası
- Adres

#### c) Sosyal Medya (social)
- Facebook, Twitter/X, Instagram
- YouTube, LinkedIn, GitHub

#### d) SEO (seo)
- Meta başlık, meta açıklama
- Meta anahtar kelimeler
- Google Analytics ID
- robots.txt yönetimi
- Google SEO önizleme

#### e) E-posta / SMTP (smtp)
- SMTP sunucu, port, şifreleme
- Kullanıcı adı, şifre
- Gönderen adı, gönderen e-posta
- **Test mail gönderme:** Alıcı, konu, mesaj alanları ile SMTP test

#### f) Bakım Modu (maintenance)
- Bakım modu açma/kapama toggle
- Bakım mesajı
- IP whitelist (bakım modunda erişime izin verilen IP'ler)

**Teknik Detaylar:**
- Ayarlar 1 saatlik cache ile saklanır (`Cache::remember`)
- Her grup ayrı validation ve update metodu
- SMTP test maili, DB'deki SMTP ayarlarını dinamik olarak `config()` üzerine yazar
- Logo/favicon yüklemede `UploadService` kullanılır
- Tab geçişi URL'de `?tab=` parametresi ile persist edilir

---

### 8. Mail Loglama (Mail Logs)

**Dosyalar:**
- `app/Models/MailLog.php` — Mail log modeli (SoftDeletes)
- `app/Services/MailLogService.php` — İstatistik, filtreleme, CRUD, cache
- `app/Http/Controllers/Admin/MailLogController.php` — Liste, detay, silme
- `app/Listeners/MailEventSubscriber.php` — Laravel mail event subscriber
- `resources/views/admin/mail-logs/index.blade.php` — Liste sayfası
- `resources/views/admin/mail-logs/show.blade.php` — Detay sayfası
- `public/assets/admin/js/mail-logs.js` — Counter animasyon, silme modal

**Veritabanı:**
- `mail_logs` tablosu — user_id, to_email, to_name, subject, body (longText), mailable_class, status (pending/sent/failed), error_message, sent_at

**Nasıl Çalışır:**
1. Laravel'in `MessageSending` event'i tetiklendiğinde → `MailLog` kaydı `pending` statüsüyle oluşturulur
2. `MessageSent` event'i tetiklendiğinde → ilgili log `sent` olarak güncellenir
3. Hata durumunda → `failed` statüsü ve hata mesajı kaydedilir
4. `AppServiceProvider::boot()` içinde `Event::subscribe(MailEventSubscriber::class)` ile kayıtlı

**Admin Panel Özellikleri:**
- 4 istatistik kartı: Toplam, Gönderilen, Başarısız, Bekleyen
- Durum sekmeleri ile filtreleme (Tümü, Gönderilen, Başarısız, Bekleyen)
- Arama (e-posta, konu) + tarih filtresi
- Tablo: Durum, Alıcı, Konu, Mail Türü, Tarih, İşlemler
- Detay sayfası: Mail bilgileri + HTML body önizlemesi (iframe içinde)
- Log silme (CSRF korumalı modal)

---

## Veritabanı Yapısı

### Migration Sırası
```
1. users, cache, jobs (Laravel varsayılan)
2. roles
3. add_role_id_to_users
4. categories
5. posts
6. pages
7. menus + menu_items
8. settings
9. mail_logs
```

### Seeder Çalıştırma Sırası
```php
// DatabaseSeeder.php
$this->call([
    RoleSeeder::class,      // 4 rol: admin, editor, author, user
    UserSeeder::class,      // Admin kullanıcı
    MenuSeeder::class,      // Header + footer menüleri
    SettingSeeder::class,   // 35 varsayılan ayar
    CategorySeeder::class,  // 5 kategori
    PostSeeder::class,      // 10 içerik
    PageSeeder::class,      // 7 sayfa
]);
```

---

## Admin Panel Sidebar Yapısı

```
Ana Menü
  └─ Dashboard

İçerik Yönetimi
  ├─ İçerikler (Posts)
  ├─ Kategoriler (Categories)
  └─ Sayfalar (Pages)

Yönetim
  ├─ Kullanıcılar (Users)
  └─ Menüler (Menus)

Sistem
  ├─ Mail Logları (Mail Logs)
  └─ Ayarlar (Settings)
```

---

## Route Yapısı (Admin)

```
GET    /admin                              → DashboardController@index
GET    /admin/users                        → UserController@index
POST   /admin/users                        → UserController@store
GET    /admin/users/create                 → UserController@create
GET    /admin/users/{user}/edit            → UserController@edit
PUT    /admin/users/{user}                 → UserController@update
DELETE /admin/users/{user}                 → UserController@destroy

GET    /admin/categories                   → CategoryController@index
POST   /admin/categories                   → CategoryController@store
GET    /admin/categories/create            → CategoryController@create
GET    /admin/categories/{category}/edit   → CategoryController@edit
PUT    /admin/categories/{category}        → CategoryController@update
DELETE /admin/categories/{category}        → CategoryController@destroy

GET    /admin/posts                        → PostController@index
POST   /admin/posts                        → PostController@store
GET    /admin/posts/create                 → PostController@create
GET    /admin/posts/{post}/edit            → PostController@edit
PUT    /admin/posts/{post}                 → PostController@update
DELETE /admin/posts/{post}                 → PostController@destroy

GET    /admin/pages                        → PageController@index
POST   /admin/pages                        → PageController@store
GET    /admin/pages/create                 → PageController@create
GET    /admin/pages/{page}/edit            → PageController@edit
PUT    /admin/pages/{page}                 → PageController@update
DELETE /admin/pages/{page}                 → PageController@destroy

GET    /admin/menus                        → MenuController@index
POST   /admin/menus                        → MenuController@store
GET    /admin/menus/create                 → MenuController@create
GET    /admin/menus/{menu}/edit            → MenuController@edit
PUT    /admin/menus/{menu}                 → MenuController@update
DELETE /admin/menus/{menu}                 → MenuController@destroy
GET    /admin/menus/{menu}/items           → MenuController@items
POST   /admin/menus/{menu}/items           → MenuController@storeItem
PUT    /admin/menus/{menu}/items/{item}    → MenuController@updateItem
DELETE /admin/menus/{menu}/items/{item}    → MenuController@destroyItem
POST   /admin/menus/{menu}/items/reorder   → MenuController@reorderItems

GET    /admin/settings                     → SettingController@index
PUT    /admin/settings/general             → SettingController@updateGeneral
PUT    /admin/settings/contact             → SettingController@updateContact
PUT    /admin/settings/social              → SettingController@updateSocial
PUT    /admin/settings/seo                 → SettingController@updateSeo
PUT    /admin/settings/smtp                → SettingController@updateSmtp
PUT    /admin/settings/maintenance         → SettingController@updateMaintenance
DELETE /admin/settings/remove-logo         → SettingController@removeLogo
DELETE /admin/settings/remove-favicon      → SettingController@removeFavicon
POST   /admin/settings/clear-cache        → SettingController@clearCache
POST   /admin/settings/send-test-mail     → SettingController@sendTestMail

GET    /admin/mail-logs                    → MailLogController@index
GET    /admin/mail-logs/{mailLog}          → MailLogController@show
DELETE /admin/mail-logs/{mailLog}          → MailLogController@destroy
```

---

## Dosya Yapısı Özeti

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── PostController.php
│   │   │   ├── PageController.php
│   │   │   ├── MenuController.php
│   │   │   ├── MailLogController.php
│   │   │   └── SettingController.php
│   │   └── Auth/
│   │       └── ...
│   └── Requests/
│       └── ...
├── Listeners/
│   └── MailEventSubscriber.php
├── Models/
│   ├── User.php
│   ├── Role.php
│   ├── Category.php
│   ├── Post.php
│   ├── Page.php
│   ├── Menu.php
│   ├── MenuItem.php
│   ├── Setting.php
│   └── MailLog.php
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    ├── AuthService.php
    ├── UserService.php
    ├── CategoryService.php
    ├── PostService.php
    ├── PageService.php
    ├── MenuService.php
    ├── SettingService.php
    ├── MailLogService.php
    └── UploadService.php

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 0001_01_01_000003_create_roles_table.php
│   ├── 0001_01_01_000004_add_role_id_to_users_table.php
│   ├── 2026_03_02_000001_create_categories_table.php
│   ├── 2026_03_02_000002_create_posts_table.php
│   ├── 2026_03_02_000003_create_pages_table.php
│   ├── 2026_03_02_000004_create_menus_table.php
│   ├── 2026_03_02_000005_create_settings_table.php
│   └── 2026_03_02_000006_create_mail_logs_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── RoleSeeder.php
    ├── UserSeeder.php
    ├── CategorySeeder.php
    ├── PostSeeder.php
    ├── PageSeeder.php
    ├── MenuSeeder.php
    └── SettingSeeder.php

resources/views/
├── admin/
│   ├── categories/
│   ├── dashboard.blade.php
│   ├── mail-logs/
│   ├── menus/
│   ├── pages/
│   ├── posts/
│   ├── settings/
│   └── users/
├── layouts/
│   ├── admin.blade.php
│   └── front.blade.php
└── partials/
    └── admin/
        ├── sidebar.blade.php
        └── topbar.blade.php

public/assets/admin/
├── css/
│   └── styles.css
└── js/
    ├── app.js
    ├── blog-list.js
    ├── content-add.js
    ├── mail-logs.js
    ├── settings.js
    └── users.js
```

---

## Planlanan Geliştirmeler

### Frontend (Kullanıcı Arayüzü)
- [ ] Ana sayfa tasarımı (hero, son yazılar, kategoriler)
- [ ] Kategori listeleme sayfası
- [ ] Kategori detay sayfası (o kategorideki yazılar)
- [ ] İçerik detay sayfası (tekil post görüntüleme)
- [ ] Statik sayfa görüntüleme
- [ ] Arama fonksiyonu
- [ ] Tag/etiket sistemi
- [ ] Yorum sistemi
- [ ] İletişim formu (mail gönderme entegrasyonu)
- [ ] Responsive tasarım (mobile-first)
- [ ] SEO optimizasyonu (meta tags, Open Graph, canonical URL)
- [ ] Sitemap.xml otomatik oluşturma
- [ ] RSS feed

### Admin Panel Geliştirmeleri
- [ ] Dashboard istatistikleri (Chart.js grafikleri)
- [ ] Toplu işlemler (bulk delete, bulk status change)
- [ ] Medya yöneticisi (galeri, dosya yükleme)
- [ ] Yorum yönetimi
- [ ] Profil düzenleme sayfası
- [ ] Aktivite logları (kim ne yaptı)
- [ ] İçerik düzenleme geçmişi (revizyon takibi)

### Teknik Altyapı
- [ ] Bakım modu middleware'i
- [ ] Rate limiting (API ve form submit)
- [ ] Queue sistemi (mail gönderimi için)
- [ ] Otomatik yedekleme (backup)
- [ ] Test yazma (Feature + Unit testler)
- [ ] CI/CD pipeline

---

## Sürüm Geçmişi

### v0.1.0 — Temel Altyapı (Mart 2026)
- Laravel 12 kurulumu
- Authentication sistemi (login, register, forgot password)
- Rol sistemi (admin, editor, author, user)
- Admin panel layout (sidebar, topbar, dark tema)
- UploadService (WebP dönüşümü, responsive varyantlar)

### v0.2.0 — İçerik Yönetimi (Mart 2026)
- Kullanıcı yönetimi (CRUD, istatistikler, filtreleme)
- Kategori yönetimi (CRUD, renk, sıralama)
- İçerik yönetimi (CRUD, durum yönetimi, öne çıkarma, SEO)
- Sayfa yönetimi (CRUD, şablon seçimi, footer bağlantısı)

### v0.3.0 — Sistem Modülleri (Mart 2026)
- Menü yönetimi (hiyerarşik yapı, sürükle-bırak sıralama, konum atama)
- Site ayarları (6 grup: genel, iletişim, sosyal medya, SEO, SMTP, bakım)
- Mail loglama (otomatik event subscriber, durum takibi, detay görüntüleme)
- SMTP test mail gönderme
- Kategori, içerik ve sayfa seed verileri

---

*Bu dosya projenin gelişim sürecini takip etmek amacıyla tutulmaktadır.*
*Son güncelleme: 02.03.2026*
