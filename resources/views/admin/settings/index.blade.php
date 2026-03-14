@extends('layouts.admin')

@section('title', 'Ayarlar — Boyalı Kelimeler Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house"></i> Ana Sayfa</a></li>
            <li class="breadcrumb-item active text-teal">Ayarlar</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">Site Ayarları</h1>
            <p class="page-subtitle">Logo, iletişim bilgileri, sosyal medya, SEO ve sistem tercihleri</p>
        </div>
    </div>

    <!-- Settings Layout -->
    <div class="stg-layout">

        <!-- Settings Nav (Desktop) -->
        <div class="stg-nav d-none d-lg-block" data-aos="fade-right" data-aos-delay="100">
            <div class="stg-nav-inner">
                <a href="#stg-homepage" class="stg-nav-item {{ ($tab ?? '') === 'homepage' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-homepage')">
                    <i class="bi bi-house-heart"></i>
                    <div><span>Anasayfa</span><small>Hero başlık, alt başlık & açıklama</small></div>
                </a>
                <a href="#stg-general" class="stg-nav-item {{ ($tab ?? 'general') === 'general' && ($tab ?? '') !== 'homepage' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-general')">
                    <i class="bi bi-sliders2"></i>
                    <div><span>Genel</span><small>Site adı, logo & temel ayarlar</small></div>
                </a>
                <a href="#stg-contact" class="stg-nav-item {{ ($tab ?? '') === 'contact' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-contact')">
                    <i class="bi bi-telephone"></i>
                    <div><span>İletişim</span><small>E-posta, telefon & adres</small></div>
                </a>
                <a href="#stg-social" class="stg-nav-item {{ ($tab ?? '') === 'social' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-social')">
                    <i class="bi bi-share"></i>
                    <div><span>Sosyal Medya</span><small>Sosyal medya hesap linkleri</small></div>
                </a>
                <a href="#stg-seo" class="stg-nav-item {{ ($tab ?? '') === 'seo' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-seo')">
                    <i class="bi bi-search"></i>
                    <div><span>SEO</span><small>Meta etiketleri & analitik</small></div>
                </a>
                <a href="#stg-email" class="stg-nav-item {{ ($tab ?? '') === 'smtp' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-email')">
                    <i class="bi bi-envelope-at"></i>
                    <div><span>E-posta (SMTP)</span><small>Sunucu yapılandırması</small></div>
                </a>
                <a href="#stg-mail-theme" class="stg-nav-item {{ ($tab ?? '') === 'mail_theme' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-mail-theme')">
                    <i class="bi bi-palette"></i>
                    <div><span>Mail Teması</span><small>Renk, footer & sosyal medya</small></div>
                </a>
                <a href="#stg-recaptcha" class="stg-nav-item {{ ($tab ?? '') === 'recaptcha' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-recaptcha')">
                    <i class="bi bi-shield-check"></i>
                    <div><span>reCAPTCHA</span><small>Google reCAPTCHA v2 doğrulama</small></div>
                </a>
                <a href="#stg-maintenance" class="stg-nav-item {{ ($tab ?? '') === 'maintenance' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-maintenance')">
                    <i class="bi bi-tools"></i>
                    <div><span>Bakım Modu</span><small>Planlı bakım & sistem durumu</small></div>
                </a>
            </div>
        </div>

        <!-- Settings Nav (Mobile) -->
        <div class="d-lg-none mb-3">
            <select class="stg-select" onchange="switchSettingsTab(this.value, null)">
                <option value="stg-homepage" {{ ($tab ?? '') === 'homepage' ? 'selected' : '' }}>Anasayfa</option>
                <option value="stg-general" {{ ($tab ?? 'general') === 'general' && ($tab ?? '') !== 'homepage' ? 'selected' : '' }}>Genel</option>
                <option value="stg-contact" {{ ($tab ?? '') === 'contact' ? 'selected' : '' }}>İletişim</option>
                <option value="stg-social" {{ ($tab ?? '') === 'social' ? 'selected' : '' }}>Sosyal Medya</option>
                <option value="stg-seo" {{ ($tab ?? '') === 'seo' ? 'selected' : '' }}>SEO</option>
                <option value="stg-email" {{ ($tab ?? '') === 'smtp' ? 'selected' : '' }}>E-posta (SMTP)</option>
                <option value="stg-mail-theme" {{ ($tab ?? '') === 'mail_theme' ? 'selected' : '' }}>Mail Teması</option>
                <option value="stg-recaptcha" {{ ($tab ?? '') === 'recaptcha' ? 'selected' : '' }}>reCAPTCHA</option>
                <option value="stg-maintenance" {{ ($tab ?? '') === 'maintenance' ? 'selected' : '' }}>Bakım Modu</option>
            </select>
        </div>

        <!-- Settings Content -->
        <div class="stg-content">

            {{-- ==================== 0. ANASAYFA ==================== --}}
            <div class="stg-panel {{ ($tab ?? '') === 'homepage' ? 'active' : '' }}" id="stg-homepage">
                <form action="{{ route('admin.settings.update.homepage') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-house-heart"></i> Anasayfa Ayarları</h5>
                            <p>Hero bölümündeki başlık, alt başlık ve açıklama metinlerini yönetin</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <!-- Hero Bölümü -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Hero Bölümü</h6>
                            <p>Anasayfanın en üstündeki karşılama alanı metinleri</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Ana Başlık</label>
                            <input type="text" name="hero_title" class="stg-input" value="{{ old('hero_title', $homepage['hero_title'] ?? '') }}" placeholder="Boyalı Kelimeler">
                            <small class="stg-hint">Hero bölümündeki büyük başlık (h1)</small>
                            @error('hero_title') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Alt Başlık</label>
                            <input type="text" name="hero_subtitle" class="stg-input" value="{{ old('hero_subtitle', $homepage['hero_subtitle'] ?? '') }}" placeholder="Sosyal Çöküntüye Sanatsal Direniş">
                            <small class="stg-hint">Ana başlığın hemen altındaki açıklama satırı</small>
                            @error('hero_subtitle') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Etiket (Tagline)</label>
                            <input type="text" name="hero_tagline" class="stg-input" value="{{ old('hero_tagline', $homepage['hero_tagline'] ?? '') }}" placeholder="— Bir Sanat Hareketi —">
                            <small class="stg-hint">Çizginin altındaki kısa etiket metni</small>
                            @error('hero_tagline') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Açıklama Metni</label>
                            <textarea name="hero_description" class="stg-textarea" rows="3" placeholder="Kelimelerin boyandığı, fırçaların konuştuğu...">{{ old('hero_description', $homepage['hero_description'] ?? '') }}</textarea>
                            <small class="stg-hint">Hero bölümünün alt kısmındaki tanıtım paragrafı</small>
                            @error('hero_description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- YouTube Kanal Videoları -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6><i class="bi bi-youtube text-neon-red me-2"></i>YouTube Kanal Videoları</h6>
                            <p>Anasayfada otomatik gösterilecek YouTube kanal videolarının ayarları</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">YouTube Kanal ID</label>
                            <input type="text" name="youtube_channel_id" class="stg-input" value="{{ old('youtube_channel_id', $homepage['youtube_channel_id'] ?? '') }}" placeholder="UCxxxxxxxxxxxxxxxxxxxxxx">
                            <small class="stg-hint">YouTube kanal ID'si (UC ile başlar). Kanal sayfası → Sağ tık → Sayfa kaynağını görüntüle → "channel_id" aratın. Boş bırakılırsa video bölümü gizlenir.</small>
                            @error('youtube_channel_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">YouTube Data API Key</label>
                            <input type="text" name="youtube_api_key" class="stg-input" value="{{ old('youtube_api_key', $homepage['youtube_api_key'] ?? '') }}" placeholder="AIzaSy...">
                            <small class="stg-hint">Google Cloud Console → APIs & Services → Credentials → API Key oluşturun. YouTube Data API v3'ü etkinleştirmeniz gerekir. Bu sayede Shorts filtrelenir ve daha fazla video gösterilir.</small>
                            @error('youtube_api_key') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Sidebar Video -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6><i class="bi bi-play-btn-fill text-neon-red me-2"></i>Sidebar YouTube Video</h6>
                            <p>Anasayfa sağ sidebar'da "En Çok Okunanlar" ile "Dergimizi Alın" arasında gösterilecek YouTube videosu</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">YouTube Video Key</label>
                            <input type="text" name="sidebar_youtube_video_key" class="stg-input" value="{{ old('sidebar_youtube_video_key', $homepage['sidebar_youtube_video_key'] ?? '') }}" placeholder="dQw4w9WgXcQ">
                            <small class="stg-hint">YouTube video URL'sindeki key değeri. Örnek: https://www.youtube.com/watch?v=<strong>dQw4w9WgXcQ</strong> → sadece <strong>dQw4w9WgXcQ</strong> kısmını yazın. Boş bırakılırsa video kutusu gizlenir.</small>
                            @error('sidebar_youtube_video_key') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </form>

                <!-- Haftanın Film Önerileri -->
                <form action="{{ route('admin.settings.update.weekly-movies') }}" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-film"></i> Haftanın Film Önerisi</h5>
                            <p>Anasayfa sidebar'da gösterilen haftalık film listesini yönetin</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Gösterim Ayarı</h6>
                            <p>Sidebar'da kaç film gösterileceğini belirleyin</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Gösterilecek Film Sayısı</label>
                            <input type="number" name="weekly_movies_count" class="stg-input" min="1" max="20" value="{{ old('weekly_movies_count', $homepage['weekly_movies_count'] ?? '5') }}">
                            <small class="stg-hint">Sidebar'da listelenecek maksimum film sayısı</small>
                        </div>
                    </div>

                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Film Listesi</h6>
                            <p>Film adı zorunlu, yıl / yönetmen / link opsiyoneldir</p>
                        </div>

                        <div id="weeklyMoviesList">
                            @php
                                $movies = json_decode($homepage['weekly_movies'] ?? '[]', true) ?: [];
                            @endphp
                            @forelse($movies as $i => $movie)
                                <div class="stg-movie-row mb-3" data-index="{{ $i }}">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="stg-movie-number">{{ $i + 1 }}</span>
                                        <input type="text" name="movies[{{ $i }}][title]" class="stg-input flex-grow-1" value="{{ $movie['title'] ?? '' }}" placeholder="Film adı *" required>
                                        <button type="button" class="btn btn-sm btn-outline-danger stg-movie-remove rounded-circle" title="Kaldır"><i class="bi bi-trash"></i></button>
                                    </div>
                                    <div class="d-flex gap-2 ms-4">
                                        <input type="text" name="movies[{{ $i }}][year]" class="stg-input" value="{{ $movie['year'] ?? '' }}" placeholder="Yıl" maxlength="4">
                                        <input type="text" name="movies[{{ $i }}][director]" class="stg-input" value="{{ $movie['director'] ?? '' }}" placeholder="Yönetmen">
                                        <input type="url" name="movies[{{ $i }}][link]" class="stg-input flex-grow-1" value="{{ $movie['link'] ?? '' }}" placeholder="Link (opsiyonel)">
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted" id="noMovieText">Henüz film eklenmemiş.</p>
                            @endforelse
                        </div>

                        <button type="button" class="btn btn-sm btn-teal mt-2" id="addMovieBtn">
                            <i class="bi bi-plus-lg me-1"></i> Film Ekle
                        </button>
                    </div>
                </form>
            </div>

            {{-- ==================== 1. GENEL AYARLAR ==================== --}}
            <div class="stg-panel {{ ($tab ?? 'general') === 'general' && ($tab ?? '') !== 'homepage' ? 'active' : '' }}" id="stg-general">
                <form action="{{ route('admin.settings.update.general') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-sliders2"></i> Genel Ayarlar</h5>
                            <p>Sitenizin temel bilgilerini ve tercihlerini buradan yönetin</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <!-- Site Bilgileri -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Site Bilgileri</h6>
                            <p>Temel site adı, açıklama ve logo ayarları</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Site Adı</label>
                            <input type="text" name="site_name" class="stg-input" value="{{ old('site_name', $general['site_name'] ?? '') }}" placeholder="Site adını girin">
                            <small class="stg-hint">Bu ad başlık çubuğunda ve maillerde görüntülenir</small>
                            @error('site_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Site Açıklaması</label>
                            <textarea name="site_description" class="stg-textarea" rows="3" placeholder="Kısa bir açıklama yazın">{{ old('site_description', $general['site_description'] ?? '') }}</textarea>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Site Logosu</label>
                            <div class="stg-logo-upload">
                                <div class="stg-logo-preview" id="logoPreview">
                                    @if(!empty($general['logo']))
                                        <div class="stg-logo-current d-none" id="logoDefault">BK</div>
                                        <img class="stg-logo-img" id="logoImg" src="/uploads/{{ $general['logo'] }}" alt="Logo">
                                    @else
                                        <div class="stg-logo-current" id="logoDefault">BK</div>
                                        <img class="stg-logo-img d-none" id="logoImg" src="" alt="Logo">
                                    @endif
                                </div>
                                <div class="stg-logo-actions">
                                    <input type="file" name="logo" id="logoInput" accept="image/png,image/jpeg,image/svg+xml,image/webp" hidden>
                                    <button type="button" class="stg-btn stg-btn-sm" onclick="document.getElementById('logoInput').click()"><i class="bi bi-upload"></i> Logo Yükle</button>
                                    @if(!empty($general['logo']))
                                        <a href="javascript:void(0)" class="stg-btn stg-btn-sm stg-btn-ghost" id="logoRemoveBtn" onclick="openConfirmModal({
                                            title: 'Logo Kaldır',
                                            message: 'Mevcut logo kaldırılacak. Devam etmek istiyor musunuz?',
                                            iconClass: 'bi-trash3',
                                            type: 'warning',
                                            btnHtml: '<i class=\'bi bi-trash3\'></i> Evet, Kaldır',
                                            onConfirm: function() { window.location.href = '{{ route('admin.settings.remove-logo') }}'; }
                                        })"><i class="bi bi-trash3"></i> Kaldır</a>
                                    @endif
                                    <small class="text-muted">PNG, JPG, SVG veya WebP. Maks. 2 MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Favicon</label>
                            <div class="stg-logo-upload">
                                <div class="stg-logo-preview" id="faviconPreview">
                                    @if(!empty($general['favicon']))
                                        <img class="stg-logo-img" id="faviconImg" src="/uploads/{{ $general['favicon'] }}" alt="Favicon">
                                    @else
                                        <div class="stg-logo-current" id="faviconDefault"><i class="bi bi-globe2"></i></div>
                                        <img class="stg-logo-img d-none" id="faviconImg" src="" alt="Favicon">
                                    @endif
                                </div>
                                <div class="stg-logo-actions">
                                    <input type="file" name="favicon" id="faviconInput" accept="image/png,image/x-icon,image/svg+xml" hidden>
                                    <button type="button" class="stg-btn stg-btn-sm" onclick="document.getElementById('faviconInput').click()"><i class="bi bi-upload"></i> Favicon Yükle</button>
                                    @if(!empty($general['favicon']))
                                        <a href="javascript:void(0)" class="stg-btn stg-btn-sm stg-btn-ghost" onclick="openConfirmModal({
                                            title: 'Favicon Kaldır',
                                            message: 'Mevcut favicon kaldırılacak. Devam etmek istiyor musunuz?',
                                            iconClass: 'bi-trash3',
                                            type: 'warning',
                                            btnHtml: '<i class=\'bi bi-trash3\'></i> Evet, Kaldır',
                                            onConfirm: function() { window.location.href = '{{ route('admin.settings.remove-favicon') }}'; }
                                        })"><i class="bi bi-trash3"></i> Kaldır</a>
                                    @endif
                                    <small class="text-muted">PNG, ICO veya SVG. Maks. 512 KB, 32x32px önerilir</small>
                                </div>
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Site URL'si</label>
                            <div class="stg-input-group">
                                <span class="stg-input-prefix">https://</span>
                                <input type="text" name="site_url" class="stg-input" value="{{ old('site_url', $general['site_url'] ?? '') }}" placeholder="domain.com">
                            </div>
                        </div>
                    </div>

                    <!-- Bölgesel Ayarlar -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Bölgesel Ayarlar</h6>
                            <p>Dil ve saat dilimi tercihleri</p>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label">Dil</label>
                                <select name="language" class="stg-select">
                                    <option value="tr" {{ ($general['language'] ?? 'tr') === 'tr' ? 'selected' : '' }}>Türkçe</option>
                                    <option value="en" {{ ($general['language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
                                </select>
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label">Saat Dilimi</label>
                                <select name="timezone" class="stg-select">
                                    <option value="Europe/Istanbul" {{ ($general['timezone'] ?? '') === 'Europe/Istanbul' ? 'selected' : '' }}>Europe/Istanbul (UTC+3)</option>
                                    <option value="Europe/London" {{ ($general['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London (UTC+0)</option>
                                    <option value="America/New_York" {{ ($general['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York (UTC-5)</option>
                                    <option value="Asia/Tokyo" {{ ($general['timezone'] ?? '') === 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (UTC+9)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ==================== 2. İLETİŞİM ==================== --}}
            <div class="stg-panel {{ ($tab ?? '') === 'contact' ? 'active' : '' }}" id="stg-contact">
                <form action="{{ route('admin.settings.update.contact') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-telephone"></i> İletişim Bilgileri</h5>
                            <p>Sitenizin iletişim bilgilerini yönetin</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>İletişim Detayları</h6>
                            <p>E-posta, telefon ve adres bilgileri</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">E-posta Adresi</label>
                            <input type="email" name="email" class="stg-input" value="{{ old('email', $contact['email'] ?? '') }}" placeholder="iletisim@domain.com">
                            <small class="stg-hint">Kullanıcıların size ulaşabileceği e-posta adresi</small>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Telefon Numarası</label>
                            <input type="text" name="phone" class="stg-input" value="{{ old('phone', $contact['phone'] ?? '') }}" placeholder="+90 (5XX) XXX XX XX">
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Adres</label>
                            <textarea name="address" class="stg-textarea" rows="3" placeholder="Açık adres">{{ old('address', $contact['address'] ?? '') }}</textarea>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Google Harita Embed Kodu</label>
                            <textarea name="map_embed" class="stg-textarea" rows="3" placeholder="<iframe src='...'></iframe>">{{ old('map_embed', $contact['map_embed'] ?? '') }}</textarea>
                            <small class="stg-hint">Google Maps'ten alınan iframe embed kodunu buraya yapıştırın</small>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ==================== 3. SOSYAL MEDYA ==================== --}}
            <div class="stg-panel {{ ($tab ?? '') === 'social' ? 'active' : '' }}" id="stg-social">
                <form action="{{ route('admin.settings.update.social') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-share"></i> Sosyal Medya</h5>
                            <p>Sosyal medya hesap linklerinizi yönetin</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Sosyal Medya Hesapları</h6>
                            <p>Sitenizde ve footer'da gösterilecek sosyal medya linkleri</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label"><i class="bi bi-facebook text-neon-blue me-2"></i>Facebook</label>
                            <div class="stg-input-group">
                                <span class="stg-input-prefix">https://</span>
                                <input type="url" name="facebook" class="stg-input" value="{{ old('facebook', $social['facebook'] ?? '') }}" placeholder="facebook.com/boyalikelimeler">
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label"><i class="bi bi-twitter-x me-2"></i>X (Twitter)</label>
                            <div class="stg-input-group">
                                <span class="stg-input-prefix">https://</span>
                                <input type="url" name="twitter" class="stg-input" value="{{ old('twitter', $social['twitter'] ?? '') }}" placeholder="x.com/boyalikelimeler">
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label"><i class="bi bi-instagram text-neon-pink me-2"></i>Instagram</label>
                            <div class="stg-input-group">
                                <span class="stg-input-prefix">https://</span>
                                <input type="url" name="instagram" class="stg-input" value="{{ old('instagram', $social['instagram'] ?? '') }}" placeholder="instagram.com/boyalikelimeler">
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label"><i class="bi bi-youtube text-neon-red me-2"></i>YouTube</label>
                            <div class="stg-input-group">
                                <span class="stg-input-prefix">https://</span>
                                <input type="url" name="youtube" class="stg-input" value="{{ old('youtube', $social['youtube'] ?? '') }}" placeholder="youtube.com/@boyalikelimeler">
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label"><i class="bi bi-tiktok me-2"></i>TikTok</label>
                            <div class="stg-input-group">
                                <span class="stg-input-prefix">https://</span>
                                <input type="url" name="tiktok" class="stg-input" value="{{ old('tiktok', $social['tiktok'] ?? '') }}" placeholder="tiktok.com/@boyalikelimeler">
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label"><i class="bi bi-linkedin text-neon-blue me-2"></i>LinkedIn</label>
                            <div class="stg-input-group">
                                <span class="stg-input-prefix">https://</span>
                                <input type="url" name="linkedin" class="stg-input" value="{{ old('linkedin', $social['linkedin'] ?? '') }}" placeholder="linkedin.com/company/boyalikelimeler">
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label"><i class="bi bi-whatsapp text-neon-green me-2"></i>WhatsApp</label>
                            <div class="stg-input-group">
                                <span class="stg-input-prefix">+90</span>
                                <input type="tel" name="whatsapp" class="stg-input" value="{{ old('whatsapp', $social['whatsapp'] ?? '') }}" placeholder="5537091992">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ==================== 4. SEO ==================== --}}
            <div class="stg-panel {{ ($tab ?? '') === 'seo' ? 'active' : '' }}" id="stg-seo">
                <form action="{{ route('admin.settings.update.seo') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-search"></i> SEO Ayarları</h5>
                            <p>Arama motoru optimizasyonu ve analitik ayarları</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <!-- Meta Etiketleri -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Varsayılan Meta Etiketleri</h6>
                            <p>Sayfa bazlı tanımlanmadığında kullanılacak varsayılan değerler</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Meta Başlık</label>
                            <input type="text" name="meta_title" class="stg-input" value="{{ old('meta_title', $seo['meta_title'] ?? '') }}" placeholder="Site başlığı — açıklama" maxlength="70" oninput="updateSeoCounter(this, 70)">
                            <small class="stg-hint"><span id="metaTitleCount">{{ mb_strlen($seo['meta_title'] ?? '') }}</span>/70 karakter</small>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Meta Açıklama</label>
                            <textarea name="meta_description" class="stg-textarea" rows="3" placeholder="Site hakkında kısa açıklama" maxlength="170" oninput="updateSeoCounter(this, 170)">{{ old('meta_description', $seo['meta_description'] ?? '') }}</textarea>
                            <small class="stg-hint"><span id="metaDescCount">{{ mb_strlen($seo['meta_description'] ?? '') }}</span>/170 karakter</small>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Meta Anahtar Kelimeler</label>
                            <input type="text" name="meta_keywords" class="stg-input" value="{{ old('meta_keywords', $seo['meta_keywords'] ?? '') }}" placeholder="edebiyat, şiir, sanat, dergi">
                            <small class="stg-hint">Virgülle ayırarak yazın</small>
                        </div>
                    </div>

                    <!-- Google -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Google Entegrasyonu</h6>
                            <p>Google Analytics ve Search Console doğrulama</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Google Analytics Tracking ID</label>
                            <input type="text" name="google_analytics" class="stg-input" value="{{ old('google_analytics', $seo['google_analytics'] ?? '') }}" placeholder="G-XXXXXXXXXX">
                            <small class="stg-hint">Google Analytics 4 ölçüm kimliği</small>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Google Search Console Doğrulama</label>
                            <input type="text" name="google_verification" class="stg-input" value="{{ old('google_verification', $seo['google_verification'] ?? '') }}" placeholder="Doğrulama meta etiketi içeriği">
                        </div>
                    </div>

                    <!-- Robots.txt -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Robots.txt</h6>
                            <p>Arama motoru botlarının erişim kuralları</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Robots.txt İçeriği</label>
                            <textarea name="robots_txt" class="stg-textarea font-monospace" rows="6" placeholder="User-agent: *&#10;Allow: /">{{ old('robots_txt', $seo['robots_txt'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- SEO Preview -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Google Önizleme</h6>
                        </div>
                        <div class="ca-seo-preview" data-aos="fade-up">
                            <div class="ca-seo-url">{{ config('app.url') }}</div>
                            <div class="ca-seo-title" id="seoPreviewTitle">{{ $seo['meta_title'] ?? 'Site Başlığı' }}</div>
                            <div class="ca-seo-desc" id="seoPreviewDesc">{{ $seo['meta_description'] ?? 'Site açıklaması burada görünecek.' }}</div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ==================== 5. E-POSTA (SMTP) ==================== --}}
            <div class="stg-panel {{ ($tab ?? '') === 'smtp' ? 'active' : '' }}" id="stg-email">
                <form action="{{ route('admin.settings.update.smtp') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-envelope-at"></i> E-posta (SMTP) Ayarları</h5>
                            <p>Giden e-posta sunucusu yapılandırması</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>SMTP Sunucusu</h6>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-field-wide">
                                <label class="stg-label">SMTP Sunucu Adresi</label>
                                <input type="text" name="host" class="stg-input" value="{{ old('host', $smtp['host'] ?? '') }}" placeholder="smtp.gmail.com">
                            </div>
                            <div class="stg-field flex-1">
                                <label class="stg-label">Port</label>
                                <input type="number" name="port" class="stg-input" value="{{ old('port', $smtp['port'] ?? '587') }}" placeholder="587">
                            </div>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label">Kullanıcı Adı</label>
                                <input type="text" name="username" class="stg-input" value="{{ old('username', $smtp['username'] ?? '') }}" placeholder="user@domain.com">
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label">Şifre</label>
                                <input type="password" name="password" class="stg-input" value="" placeholder="{{ !empty($smtp['password']) ? '••••••••  (değiştirmek için yeni şifre girin)' : 'SMTP şifresi' }}">
                            </div>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label">Şifreleme</label>
                                <select name="encryption" class="stg-select">
                                    <option value="tls" {{ ($smtp['encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($smtp['encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="none" {{ ($smtp['encryption'] ?? '') === 'none' ? 'selected' : '' }}>Yok</option>
                                </select>
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label">Gönderen Adı</label>
                                <input type="text" name="from_name" class="stg-input" value="{{ old('from_name', $smtp['from_name'] ?? '') }}" placeholder="Gönderen adı">
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Gönderen E-posta</label>
                            <input type="email" name="from_email" class="stg-input" value="{{ old('from_email', $smtp['from_email'] ?? '') }}" placeholder="noreply@domain.com">
                        </div>
                    </div>

                    <!-- Mail Logosu -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Mail Logosu</h6>
                            <p>Giden e-postalara gömülecek logo (CID olarak eklenir, her mail istemcisinde görünür)</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Logo Görseli</label>
                            <div class="stg-logo-upload">
                                <div class="stg-logo-preview" id="mailLogoPreview">
                                    @if(!empty($smtp['mail_logo']))
                                        <div class="stg-logo-current d-none" id="mailLogoDefault"><i class="bi bi-envelope-paper"></i></div>
                                        <img class="stg-logo-img" id="mailLogoImg" src="/uploads/{{ $smtp['mail_logo'] }}" alt="Mail Logo">
                                    @else
                                        <div class="stg-logo-current" id="mailLogoDefault"><i class="bi bi-envelope-paper"></i></div>
                                        <img class="stg-logo-img d-none" id="mailLogoImg" src="" alt="Mail Logo">
                                    @endif
                                </div>
                                <div class="stg-logo-actions">
                                    <input type="file" name="mail_logo" id="mailLogoInput" accept="image/png" hidden>
                                    <button type="button" class="stg-btn stg-btn-sm" onclick="document.getElementById('mailLogoInput').click()"><i class="bi bi-upload"></i> Logo Yükle</button>
                                    @if(!empty($smtp['mail_logo']))
                                        <a href="javascript:void(0)" class="stg-btn stg-btn-sm stg-btn-ghost" onclick="openConfirmModal({
                                            title: 'Mail Logosu Kaldır',
                                            message: 'Mevcut mail logosu kaldırılacak. Devam etmek istiyor musunuz?',
                                            iconClass: 'bi-trash3',
                                            type: 'warning',
                                            btnHtml: '<i class=\'bi bi-trash3\'></i> Evet, Kaldır',
                                            onConfirm: function() { window.location.href = '{{ route('admin.settings.remove-mail-logo') }}'; }
                                        })"><i class="bi bi-trash3"></i> Kaldır</a>
                                    @endif
                                    <small class="text-muted">Sadece PNG. Maks. 1 MB. Boyut: 400×400px</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gönderim Modu -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Gönderim Modu</h6>
                            <p>Maillerin gerçek alıcılara mı yoksa test adreslerine mi gideceğini belirleyin</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Mod Seçimi</label>
                            <div class="stg-mode-toggle" id="sendModeToggle">
                                <label class="stg-mode-option">
                                    <input type="radio" name="send_mode" value="normal" {{ ($smtp['send_mode'] ?? 'normal') === 'normal' ? 'checked' : '' }} onchange="toggleSendMode()">
                                    <div class="stg-mode-card">
                                        <i class="bi bi-send-check"></i>
                                        <span>Normal Mod</span>
                                        <small>Mailler asıl alıcıya gider</small>
                                    </div>
                                </label>
                                <label class="stg-mode-option">
                                    <input type="radio" name="send_mode" value="developer" {{ ($smtp['send_mode'] ?? '') === 'developer' ? 'checked' : '' }} onchange="toggleSendMode()">
                                    <div class="stg-mode-card stg-mode-card--dev">
                                        <i class="bi bi-bug"></i>
                                        <span>Developer / Test Mod</span>
                                        <small>Tüm mailler test adreslerine yönlendirilir</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="stg-field {{ ($smtp['send_mode'] ?? 'normal') === 'developer' ? '' : 'd-none' }}" id="debugEmailsField">
                            <label class="stg-label">Test E-posta Adresleri</label>
                            <input type="text" name="debug_emails" class="stg-input" value="{{ old('debug_emails', $smtp['debug_emails'] ?? '') }}" placeholder="test@example.com, dev@example.com">
                            <small class="stg-hint">Virgülle ayırarak birden fazla adres yazabilirsiniz. Tüm giden mailler bu adreslere yönlendirilecektir.</small>
                            @error('debug_emails') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </form>

                <!-- Test E-postası -->
                <form action="{{ route('admin.settings.send-test-mail') }}" method="POST">
                    @csrf
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Test E-postası Gönder</h6>
                            <p>SMTP ayarlarının doğru çalışıp çalışmadığını test edin</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Alıcı E-posta</label>
                            <input type="email" name="test_to" class="stg-input" value="{{ old('test_to') }}" placeholder="test@example.com" required>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Konu</label>
                            <input type="text" name="test_subject" class="stg-input" value="{{ old('test_subject', 'Boyalı Kelimeler — E-posta Bilgilendirmesi') }}" placeholder="E-posta konusu" required>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Mesaj</label>
                            <textarea name="test_body" class="stg-textarea" rows="4" placeholder="Mesajınızı yazın..." required>{{ old('test_body', 'Merhaba, bu e-posta Boyalı Kelimeler platformu üzerinden gönderilmiştir. E-posta yapılandırmanız başarıyla tamamlanmıştır. Herhangi bir sorunuz olursa bizimle iletişime geçebilirsiniz.') }}</textarea>
                        </div>

                        <button type="submit" class="stg-btn"><i class="bi bi-send"></i> Test Maili Gönder</button>
                    </div>
                </form>
            </div>

            {{-- ==================== 6. MAIL TEMASI ==================== --}}
            <div class="stg-panel {{ ($tab ?? '') === 'mail_theme' ? 'active' : '' }}" id="stg-mail-theme">
                <form action="{{ route('admin.settings.update.mail-theme') }}" method="POST" id="mailThemeForm">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-palette"></i> Mail Teması</h5>
                            <p>E-posta şablonu renkleri, footer yazısı ve sosyal medya ayarları</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="javascript:void(0)" class="stg-btn stg-btn-sm stg-btn-ghost" onclick="openConfirmModal({
                                title: 'Mail Temasını Sıfırla',
                                message: 'Mail teması varsayılan değerlere sıfırlanacak. Devam etmek istiyor musunuz?',
                                iconClass: 'bi-arrow-counterclockwise',
                                type: 'warning',
                                btnHtml: '<i class=\'bi bi-arrow-counterclockwise\'></i> Evet, Sıfırla',
                                onConfirm: function() { window.location.href = '{{ route('admin.settings.mail-theme.reset') }}'; }
                            })"><i class="bi bi-arrow-counterclockwise"></i> Sıfırla</a>
                            <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                        </div>
                    </div>

                    <!-- Renk Paleti -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Renk Paleti</h6>
                            <p>E-posta şablonunda kullanılan renkleri özelleştirin</p>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label">Ana Renk (Primary)</label>
                                <div class="stg-color-field">
                                    <input type="color" name="primary_color" id="mtPrimaryColor" value="{{ old('primary_color', $mailTheme['primary_color'] ?? '#D4AF37') }}" onchange="updateMailThemePreview()">
                                    <input type="text" class="stg-input stg-input-sm" value="{{ old('primary_color', $mailTheme['primary_color'] ?? '#D4AF37') }}" oninput="syncColorInput(this, 'mtPrimaryColor')" maxlength="7">
                                </div>
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label">Koyu Ana Renk (Primary Dark)</label>
                                <div class="stg-color-field">
                                    <input type="color" name="primary_dark" id="mtPrimaryDark" value="{{ old('primary_dark', $mailTheme['primary_dark'] ?? '#A68B4B') }}" onchange="updateMailThemePreview()">
                                    <input type="text" class="stg-input stg-input-sm" value="{{ old('primary_dark', $mailTheme['primary_dark'] ?? '#A68B4B') }}" oninput="syncColorInput(this, 'mtPrimaryDark')" maxlength="7">
                                </div>
                            </div>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label">Arka Plan Rengi</label>
                                <div class="stg-color-field">
                                    <input type="color" name="bg_color" id="mtBgColor" value="{{ old('bg_color', $mailTheme['bg_color'] ?? '#0F0F12') }}" onchange="updateMailThemePreview()">
                                    <input type="text" class="stg-input stg-input-sm" value="{{ old('bg_color', $mailTheme['bg_color'] ?? '#0F0F12') }}" oninput="syncColorInput(this, 'mtBgColor')" maxlength="7">
                                </div>
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label">Kart Arka Planı</label>
                                <div class="stg-color-field">
                                    <input type="color" name="card_bg" id="mtCardBg" value="{{ old('card_bg', $mailTheme['card_bg'] ?? '#1A1A1E') }}" onchange="updateMailThemePreview()">
                                    <input type="text" class="stg-input stg-input-sm" value="{{ old('card_bg', $mailTheme['card_bg'] ?? '#1A1A1E') }}" oninput="syncColorInput(this, 'mtCardBg')" maxlength="7">
                                </div>
                            </div>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label">Metin Rengi</label>
                                <div class="stg-color-field">
                                    <input type="color" name="text_color" id="mtTextColor" value="{{ old('text_color', $mailTheme['text_color'] ?? '#F5F5F0') }}" onchange="updateMailThemePreview()">
                                    <input type="text" class="stg-input stg-input-sm" value="{{ old('text_color', $mailTheme['text_color'] ?? '#F5F5F0') }}" oninput="syncColorInput(this, 'mtTextColor')" maxlength="7">
                                </div>
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label">Soluk Metin Rengi</label>
                                <div class="stg-color-field">
                                    <input type="color" name="text_muted" id="mtTextMuted" value="{{ old('text_muted', $mailTheme['text_muted'] ?? '#9B9EA3') }}" onchange="updateMailThemePreview()">
                                    <input type="text" class="stg-input stg-input-sm" value="{{ old('text_muted', $mailTheme['text_muted'] ?? '#9B9EA3') }}" oninput="syncColorInput(this, 'mtTextMuted')" maxlength="7">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Ayarları -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Footer Ayarları</h6>
                            <p>E-posta alt bilgisinde görünecek metin ve sosyal medya ayarları</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Footer Yazısı</label>
                            <textarea name="footer_text" class="stg-textarea" rows="3" placeholder="Ör: Bu e-posta Boyalı Kelimeler tarafından gönderilmiştir." oninput="updateMailThemePreview()">{{ old('footer_text', $mailTheme['footer_text'] ?? '') }}</textarea>
                            <small class="stg-hint">Copyright yazısının üstünde görünür. Boş bırakılabilir.</small>
                        </div>

                        <div class="stg-toggle-item">
                            <div class="stg-toggle-info">
                                <i class="bi bi-share text-neon-blue"></i>
                                <div>
                                    <span>Sosyal Medya Linkleri</span>
                                    <small>Footer'da sosyal medya hesaplarınızı gösterin (Sosyal Medya sekmesindeki linkler kullanılır)</small>
                                </div>
                            </div>
                            <input type="hidden" name="show_social" value="0">
                            <label class="stg-switch">
                                <input type="checkbox" name="show_social" value="1" {{ ($mailTheme['show_social'] ?? '1') === '1' ? 'checked' : '' }} onchange="updateMailThemePreview()">
                                <span class="stg-switch-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Canlı Önizleme -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Canlı Önizleme</h6>
                            <p>Ayarları değiştirdikçe e-posta şablonu burada güncellenir</p>
                        </div>

                        <div class="stg-mail-preview-wrapper">
                            <div class="stg-mail-preview-toolbar">
                                <span><i class="bi bi-eye"></i> E-posta Önizleme</span>
                                <button type="button" class="stg-btn stg-btn-sm stg-btn-ghost" onclick="updateMailThemePreview()"><i class="bi bi-arrow-clockwise"></i> Yenile</button>
                            </div>
                            <div class="stg-mail-preview-frame">
                                <iframe id="mailThemePreviewFrame" sandbox="allow-same-origin" style="width: 100%; height: 580px; border: none; border-radius: 0 0 8px 8px; background: #0F0F12;"></iframe>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ==================== 7. RECAPTCHA ==================== --}}
            <div class="stg-panel {{ ($tab ?? '') === 'recaptcha' ? 'active' : '' }}" id="stg-recaptcha">
                <form action="{{ route('admin.settings.update.recaptcha') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-shield-check"></i> Google reCAPTCHA v2</h5>
                            <p>Form spam koruması için Google reCAPTCHA onay kutusu ayarları</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <!-- Durum -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>reCAPTCHA Durumu</h6>
                            <p>Form doğrulamasını açıp kapatabilirsiniz</p>
                        </div>

                        <div class="stg-toggle-list">
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span>reCAPTCHA Doğrulama</span>
                                    <small>Açık olduğunda formlarda "Ben robot değilim" onay kutusu gösterilir</small>
                                </div>
                                <label class="stg-switch">
                                    <input type="hidden" name="enabled" value="0">
                                    <input type="checkbox" name="enabled" value="1" {{ ($recaptcha['enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                                    <span class="stg-switch-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- API Anahtarları -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>API Anahtarları</h6>
                            <p>Google reCAPTCHA v2 "I'm not a robot" Checkbox anahtarları</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Site Key (Public Key)</label>
                            <input type="text" name="site_key" class="stg-input" value="{{ old('site_key', $recaptcha['site_key'] ?? '') }}" placeholder="6Lc...">
                            <small class="stg-hint">Google reCAPTCHA admin panelinden aldığınız site anahtarı</small>
                            @error('site_key') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Secret Key (Private Key)</label>
                            <input type="password" name="secret_key" class="stg-input" value="" placeholder="{{ !empty($recaptcha['secret_key']) ? '••••••••  (değiştirmek için yeni key girin)' : '6Lc...' }}" autocomplete="off">
                            <small class="stg-hint">Google reCAPTCHA admin panelinden aldığınız gizli anahtar</small>
                            @error('secret_key') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Bilgilendirme -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Bilgilendirme</h6>
                            <p>reCAPTCHA uygulandığı formlar</p>
                        </div>

                        <div class="stg-toggle-list">
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span><i class="bi bi-check-circle text-success me-1"></i> Giriş Yap</span>
                                    <small>/giris sayfası</small>
                                </div>
                            </div>
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span><i class="bi bi-check-circle text-success me-1"></i> Kayıt Ol</span>
                                    <small>/kayit-ol sayfası</small>
                                </div>
                            </div>
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span><i class="bi bi-check-circle text-success me-1"></i> Şifremi Unuttum</span>
                                    <small>/sifremi-unuttum sayfası</small>
                                </div>
                            </div>
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span><i class="bi bi-check-circle text-success me-1"></i> İletişim</span>
                                    <small>/iletisim sayfası</small>
                                </div>
                            </div>
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span><i class="bi bi-check-circle text-success me-1"></i> Yorum Formları</span>
                                    <small>İçerik ve blog yorum formları</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            {{-- ==================== 8. BAKIM MODU ==================== --}}
            <div class="stg-panel {{ ($tab ?? '') === 'maintenance' ? 'active' : '' }}" id="stg-maintenance">
                <form action="{{ route('admin.settings.update.maintenance') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="stg-panel-header">
                        <div>
                            <h5><i class="bi bi-tools"></i> Bakım Modu</h5>
                            <p>Planlı bakım ve sistem durumu</p>
                        </div>
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Kaydet</button>
                    </div>

                    <!-- Bakım Toggle -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Bakım Modu</h6>
                            <p>Etkinleştirildiğinde kullanıcılar bakım sayfasını görür, admin erişimi devam eder</p>
                        </div>

                        <input type="hidden" name="enabled" value="0">
                        <div class="stg-maintenance-toggle">
                            <div class="stg-maint-status" id="maintStatus">
                                <div class="stg-maint-indicator {{ ($maintenance['enabled'] ?? '0') === '1' ? 'stg-maint-on' : 'stg-maint-off' }}"></div>
                                <div>
                                    <span id="maintLabel">{{ ($maintenance['enabled'] ?? '0') === '1' ? 'Bakım Modu Aktif' : 'Bakım Modu Kapalı' }}</span>
                                    <small>{{ ($maintenance['enabled'] ?? '0') === '1' ? 'Kullanıcılar bakım sayfasını görüyor' : 'Sistem normal çalışıyor' }}</small>
                                </div>
                            </div>
                            <label class="stg-switch stg-switch-lg">
                                <input type="checkbox" id="maintToggle" name="enabled" value="1" {{ ($maintenance['enabled'] ?? '0') === '1' ? 'checked' : '' }} onchange="toggleMaintenance(this)">
                                <span class="stg-switch-slider"></span>
                            </label>
                        </div>

                        <div class="stg-field mt-3">
                            <label class="stg-label">Bakım Mesajı</label>
                            <textarea name="message" class="stg-textarea" rows="3" placeholder="Kullanıcılara gösterilecek mesaj">{{ old('message', $maintenance['message'] ?? '') }}</textarea>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">IP Beyaz Liste</label>
                            <input type="text" name="allowed_ips" class="stg-input" value="{{ old('allowed_ips', $maintenance['allowed_ips'] ?? '') }}" placeholder="IP adresi (virgülle ayırın)">
                            <small class="stg-hint">Bu IP'ler bakım modunda da siteye erişebilir</small>
                        </div>
                    </div>

                    <!-- Sistem Durumu -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Sistem Durumu</h6>
                        </div>

                        <div class="stg-system-status">
                            <div class="stg-status-item">
                                <div class="stg-status-dot stg-dot-ok"></div>
                                <span>Web Sunucusu</span>
                                <small class="stg-status-uptime">Çalışıyor</small>
                            </div>
                            <div class="stg-status-item">
                                <div class="stg-status-dot stg-dot-ok"></div>
                                <span>Veritabanı (MySQL)</span>
                                <small class="stg-status-uptime">Bağlı</small>
                            </div>
                            <div class="stg-status-item">
                                <div class="stg-status-dot stg-dot-ok"></div>
                                <span>PHP Sürümü</span>
                                <small class="stg-status-uptime">{{ PHP_VERSION }}</small>
                            </div>
                            <div class="stg-status-item">
                                <div class="stg-status-dot stg-dot-ok"></div>
                                <span>Laravel Sürümü</span>
                                <small class="stg-status-uptime">{{ app()->version() }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tehlikeli Bölge -->
                    <div class="stg-section stg-danger-zone">
                        <div class="stg-section-title">
                            <h6 class="text-neon-red"><i class="bi bi-exclamation-triangle-fill"></i> Tehlikeli Bölge</h6>
                            <p>Bu işlemler dikkatli kullanılmalıdır</p>
                        </div>

                        <div class="stg-danger-list">
                            <div class="stg-danger-item">
                                <div>
                                    <span>Önbelleği Temizle</span>
                                    <small>Tüm uygulama önbelleğini temizle</small>
                                </div>
                                <a href="javascript:void(0)" class="stg-btn stg-btn-sm stg-btn-warning" onclick="openConfirmModal({
                                    title: 'Önbelleği Temizle',
                                    message: 'Tüm önbellek temizlenecek. Devam etmek istiyor musunuz?',
                                    iconClass: 'bi-trash3',
                                    type: 'warning',
                                    btnHtml: '<i class=\'bi bi-trash3\'></i> Evet, Temizle',
                                    onConfirm: function() { window.location.href = '{{ route('admin.settings.clear-cache') }}'; }
                                })"><i class="bi bi-trash3"></i> Temizle</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div><!-- stg-content -->
    </div><!-- stg-layout -->

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/settings.js') }}"></script>
<script>
(function () {
    'use strict';

    var list = document.getElementById('weeklyMoviesList');
    var addBtn = document.getElementById('addMovieBtn');
    if (!list || !addBtn) return;

    function getNextIndex() {
        var rows = list.querySelectorAll('.stg-movie-row');
        var max = -1;
        rows.forEach(function (r) {
            var idx = parseInt(r.getAttribute('data-index'), 10);
            if (idx > max) max = idx;
        });
        return max + 1;
    }

    function renumberRows() {
        var rows = list.querySelectorAll('.stg-movie-row');
        rows.forEach(function (row, i) {
            var num = row.querySelector('.stg-movie-number');
            if (num) num.textContent = i + 1;
        });

        var noText = document.getElementById('noMovieText');
        if (noText) {
            noText.style.display = rows.length === 0 ? '' : 'none';
        }
    }

    addBtn.addEventListener('click', function () {
        var idx = getNextIndex();
        var html =
            '<div class="stg-movie-row mb-3" data-index="' + idx + '">' +
                '<div class="d-flex align-items-center gap-2 mb-2">' +
                    '<span class="stg-movie-number"></span>' +
                    '<input type="text" name="movies[' + idx + '][title]" class="stg-input flex-grow-1" placeholder="Film adı *" required>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger stg-movie-remove rounded-circle" title="Kaldır"><i class="bi bi-trash"></i></button>' +
                '</div>' +
                '<div class="d-flex gap-2 ms-4">' +
                    '<input type="text" name="movies[' + idx + '][year]" class="stg-input" placeholder="Yıl" maxlength="4">' +
                    '<input type="text" name="movies[' + idx + '][director]" class="stg-input" placeholder="Yönetmen">' +
                    '<input type="url" name="movies[' + idx + '][link]" class="stg-input flex-grow-1" placeholder="Link (opsiyonel)">' +
                '</div>' +
            '</div>';

        var noText = document.getElementById('noMovieText');
        if (noText) noText.style.display = 'none';

        list.insertAdjacentHTML('beforeend', html);
        renumberRows();
        list.lastElementChild.querySelector('input').focus();
    });

    list.addEventListener('click', function (e) {
        var btn = e.target.closest('.stg-movie-remove');
        if (!btn) return;
        btn.closest('.stg-movie-row').remove();
        renumberRows();
    });
})();

</script>
@endpush
