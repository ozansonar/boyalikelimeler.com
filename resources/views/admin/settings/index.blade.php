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
                <a href="#stg-general" class="stg-nav-item {{ ($tab ?? 'general') === 'general' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-general')">
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
                <a href="#stg-maintenance" class="stg-nav-item {{ ($tab ?? '') === 'maintenance' ? 'active' : '' }}" onclick="switchSettingsTab(this,'stg-maintenance')">
                    <i class="bi bi-tools"></i>
                    <div><span>Bakım Modu</span><small>Planlı bakım & sistem durumu</small></div>
                </a>
            </div>
        </div>

        <!-- Settings Nav (Mobile) -->
        <div class="d-lg-none mb-3">
            <select class="stg-select" onchange="switchSettingsTab(this.value, null)">
                <option value="stg-general" {{ ($tab ?? 'general') === 'general' ? 'selected' : '' }}>Genel</option>
                <option value="stg-contact" {{ ($tab ?? '') === 'contact' ? 'selected' : '' }}>İletişim</option>
                <option value="stg-social" {{ ($tab ?? '') === 'social' ? 'selected' : '' }}>Sosyal Medya</option>
                <option value="stg-seo" {{ ($tab ?? '') === 'seo' ? 'selected' : '' }}>SEO</option>
                <option value="stg-email" {{ ($tab ?? '') === 'smtp' ? 'selected' : '' }}>E-posta (SMTP)</option>
                <option value="stg-maintenance" {{ ($tab ?? '') === 'maintenance' ? 'selected' : '' }}>Bakım Modu</option>
            </select>
        </div>

        <!-- Settings Content -->
        <div class="stg-content">

            {{-- ==================== 1. GENEL AYARLAR ==================== --}}
            <div class="stg-panel {{ ($tab ?? 'general') === 'general' ? 'active' : '' }}" id="stg-general">
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
                                        <a href="{{ route('admin.settings.remove-logo') }}" class="stg-btn stg-btn-sm stg-btn-ghost" id="logoRemoveBtn" onclick="return confirm('Logo kaldırılsın mı?')"><i class="bi bi-trash3"></i> Kaldır</a>
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
                                        <a href="{{ route('admin.settings.remove-favicon') }}" class="stg-btn stg-btn-sm stg-btn-ghost" onclick="return confirm('Favicon kaldırılsın mı?')"><i class="bi bi-trash3"></i> Kaldır</a>
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
                <form action="{{ route('admin.settings.update.smtp') }}" method="POST">
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
                                <input type="password" name="password" class="stg-input" value="{{ old('password', $smtp['password'] ?? '') }}" placeholder="SMTP şifresi">
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
                            <input type="text" name="test_subject" class="stg-input" value="{{ old('test_subject', 'SMTP Test — Boyalı Kelimeler') }}" placeholder="Test maili konusu" required>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label">Mesaj</label>
                            <textarea name="test_body" class="stg-textarea" rows="4" placeholder="Test mesajınızı yazın..." required>{{ old('test_body', 'Bu bir test e-postasıdır. SMTP ayarlarınız doğru çalışıyor.') }}</textarea>
                        </div>

                        <button type="submit" class="stg-btn"><i class="bi bi-send"></i> Test Maili Gönder</button>
                    </div>
                </form>
            </div>

            {{-- ==================== 6. BAKIM MODU ==================== --}}
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
                                <a href="{{ route('admin.settings.clear-cache') }}" class="stg-btn stg-btn-sm stg-btn-warning" onclick="return confirm('Önbellek temizlensin mi?')"><i class="bi bi-trash3"></i> Temizle</a>
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
@endpush
