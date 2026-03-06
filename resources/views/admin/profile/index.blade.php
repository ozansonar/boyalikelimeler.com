@extends('layouts.admin')

@section('title', 'Profilim — Boyalı Kelimeler Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house"></i> Ana Sayfa</a></li>
            <li class="breadcrumb-item active text-teal">Profilim</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">Profilim</h1>
            <p class="page-subtitle">Kişisel bilgilerinizi, şifrenizi ve profil fotoğrafınızı yönetin</p>
        </div>
    </div>

    <!-- ==================== SETTINGS LAYOUT ==================== -->
    <div class="stg-layout">

        <!-- Settings Nav (Desktop) -->
        <div class="stg-nav d-none d-lg-block" data-aos="fade-right" data-aos-delay="100">
            <div class="stg-nav-inner">
                <a href="#ap-info" class="stg-nav-item active" onclick="switchProfilePanel(this, 'ap-info')">
                    <i class="bi bi-person"></i>
                    <div><span>Profil Bilgileri</span><small>Ad, e-posta, bio, sosyal medya</small></div>
                </a>
                <a href="#ap-avatar" class="stg-nav-item" onclick="switchProfilePanel(this, 'ap-avatar')">
                    <i class="bi bi-image"></i>
                    <div><span>Profil Fotoğrafı</span><small>Avatar yükle veya değiştir</small></div>
                </a>
                <a href="#ap-password" class="stg-nav-item" onclick="switchProfilePanel(this, 'ap-password')">
                    <i class="bi bi-shield-lock"></i>
                    <div><span>Şifre Değiştir</span><small>Hesap güvenliği</small></div>
                </a>
            </div>
        </div>

        <!-- Mobile Section Jumper -->
        <div class="d-lg-none mb-3">
            <select class="stg-select" onchange="switchProfilePanel(null, this.value)">
                <option value="ap-info" selected>Profil Bilgileri</option>
                <option value="ap-avatar">Profil Fotoğrafı</option>
                <option value="ap-password">Şifre Değiştir</option>
            </select>
        </div>

        <!-- Settings Content -->
        <div class="stg-content">

            <!-- ==================== PANEL 1: PROFİL BİLGİLERİ ==================== -->
            <div class="stg-panel active" id="ap-info">
                <div class="stg-panel-header">
                    <div>
                        <h5><i class="bi bi-person-fill"></i> Profil Bilgileri</h5>
                        <p>Kişisel bilgilerinizi güncelleyin</p>
                    </div>
                </div>

                <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm">
                    @csrf
                    @method('PUT')

                    <!-- Temel Bilgiler -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Temel Bilgiler</h6>
                            <p>Adınız, e-posta adresiniz ve kullanıcı adınız</p>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label" for="apName">Ad Soyad</label>
                                <input type="text" class="stg-input @error('name') is-invalid @enderror"
                                       id="apName" name="name"
                                       value="{{ old('name', $user->name) }}"
                                       placeholder="Ad Soyad">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label" for="apUsername">Kullanıcı Adı</label>
                                <div class="stg-input-group">
                                    <span class="stg-input-prefix">@</span>
                                    <input type="text" class="stg-input @error('username') is-invalid @enderror"
                                           id="apUsername" name="username"
                                           value="{{ old('username', $user->username) }}"
                                           placeholder="kullaniciadi">
                                </div>
                                @error('username')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label" for="apEmail">E-posta Adresi</label>
                            <input type="email" class="stg-input @error('email') is-invalid @enderror"
                                   id="apEmail" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="ornek@email.com">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="stg-field">
                            <label class="stg-label" for="apBio">Biyografi</label>
                            <textarea class="stg-textarea @error('bio') is-invalid @enderror"
                                      id="apBio" name="bio" rows="3"
                                      placeholder="Kendinizden kısaca bahsedin..."
                                      oninput="updateCharCounter(this, 300)">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div class="d-flex justify-content-between">
                                <small class="stg-hint">Profilinizde görünecek kısa açıklama</small>
                                <small class="stg-hint"><span id="apBio-counter">{{ mb_strlen(old('bio', $user->bio ?? '')) }}</span>/300</small>
                            </div>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label" for="apLocation">Konum</label>
                                <input type="text" class="stg-input @error('location') is-invalid @enderror"
                                       id="apLocation" name="location"
                                       value="{{ old('location', $user->location) }}"
                                       placeholder="Şehir, Ülke">
                                @error('location')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label" for="apWebsite">Website</label>
                                <input type="url" class="stg-input @error('website') is-invalid @enderror"
                                       id="apWebsite" name="website"
                                       value="{{ old('website', $user->website) }}"
                                       placeholder="https://siteadresiniz.com">
                                @error('website')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Sosyal Medya -->
                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Sosyal Medya</h6>
                            <p>Sosyal medya hesaplarınızı bağlayın</p>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label" for="apInstagram"><i class="bi bi-instagram me-1"></i> Instagram</label>
                                <input type="text" class="stg-input @error('instagram') is-invalid @enderror"
                                       id="apInstagram" name="instagram"
                                       value="{{ old('instagram', $user->instagram) }}"
                                       placeholder="kullaniciadi">
                                @error('instagram')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label" for="apTwitter"><i class="bi bi-twitter-x me-1"></i> Twitter / X</label>
                                <input type="text" class="stg-input @error('twitter') is-invalid @enderror"
                                       id="apTwitter" name="twitter"
                                       value="{{ old('twitter', $user->twitter) }}"
                                       placeholder="kullaniciadi">
                                @error('twitter')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="stg-row">
                            <div class="stg-field stg-half">
                                <label class="stg-label" for="apYoutube"><i class="bi bi-youtube me-1"></i> YouTube</label>
                                <input type="text" class="stg-input @error('youtube') is-invalid @enderror"
                                       id="apYoutube" name="youtube"
                                       value="{{ old('youtube', $user->youtube) }}"
                                       placeholder="Kanal URL'si veya @kullaniciadi">
                                @error('youtube')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="stg-field stg-half">
                                <label class="stg-label" for="apTiktok"><i class="bi bi-tiktok me-1"></i> TikTok</label>
                                <input type="text" class="stg-input @error('tiktok') is-invalid @enderror"
                                       id="apTiktok" name="tiktok"
                                       value="{{ old('tiktok', $user->tiktok) }}"
                                       placeholder="kullaniciadi">
                                @error('tiktok')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label" for="apSpotify"><i class="bi bi-spotify me-1"></i> Spotify</label>
                            <input type="text" class="stg-input @error('spotify') is-invalid @enderror"
                                   id="apSpotify" name="spotify"
                                   value="{{ old('spotify', $user->spotify) }}"
                                   placeholder="Profil URL'si">
                            @error('spotify')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="stg-save-btn"><i class="bi bi-check-lg"></i> Değişiklikleri Kaydet</button>
                    </div>
                </form>
            </div>

            <!-- ==================== PANEL 2: PROFİL FOTOĞRAFI ==================== -->
            <div class="stg-panel" id="ap-avatar">
                <div class="stg-panel-header">
                    <div>
                        <h5><i class="bi bi-image"></i> Profil Fotoğrafı</h5>
                        <p>Profil fotoğrafınızı yükleyin veya değiştirin</p>
                    </div>
                </div>

                <div class="stg-section">
                    <div class="ap-avatar-area">
                        <div class="ap-avatar-preview" id="avatarPreview">
                            @if($user->avatar)
                                <img src="{{ upload_url($user->avatar, 'md') }}" alt="{{ $user->name }}" class="ap-avatar-img" id="avatarImg" loading="lazy">
                            @else
                                <span class="ap-avatar-initials" id="avatarInitials">{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="ap-avatar-actions">
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <p class="text-muted mb-3" style="font-size: 13px">JPEG, PNG veya WebP. Maksimum 2 MB.</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/webp" hidden>
                                <button type="button" class="stg-save-btn" onclick="document.getElementById('avatarInput').click()">
                                    <i class="bi bi-upload me-1"></i> Fotoğraf Yükle
                                </button>
                                @if($user->avatar)
                                    <button type="button" class="stg-btn stg-btn-danger" id="removeAvatarBtn">
                                        <i class="bi bi-trash3 me-1"></i> Kaldır
                                    </button>
                                @endif
                            </div>
                            <div class="ap-avatar-feedback mt-2" id="avatarFeedback"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== PANEL 3: ŞİFRE DEĞİŞTİR ==================== -->
            <div class="stg-panel" id="ap-password">
                <div class="stg-panel-header">
                    <div>
                        <h5><i class="bi bi-shield-lock-fill"></i> Şifre Değiştir</h5>
                        <p>Hesap güvenliğiniz için şifrenizi düzenli olarak güncelleyin</p>
                    </div>
                </div>

                <form action="{{ route('admin.profile.password') }}" method="POST" id="passwordForm">
                    @csrf
                    @method('PUT')

                    <div class="stg-section">
                        <div class="stg-section-title">
                            <h6>Şifre Güncelleme</h6>
                            <p>Yeni şifreniz en az 8 karakter olmalı, büyük/küçük harf, rakam ve özel karakter içermelidir</p>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label" for="apCurrentPassword">Mevcut Şifre</label>
                            <div class="stg-input-group">
                                <input type="password" class="stg-input @error('current_password') is-invalid @enderror"
                                       id="apCurrentPassword" name="current_password"
                                       placeholder="Mevcut şifrenizi girin"
                                       autocomplete="current-password">
                                <button type="button" class="stg-btn stg-btn-sm ap-toggle-pw" data-target="apCurrentPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="stg-field">
                            <label class="stg-label" for="apNewPassword">Yeni Şifre</label>
                            <div class="stg-input-group">
                                <input type="password" class="stg-input @error('password') is-invalid @enderror"
                                       id="apNewPassword" name="password"
                                       placeholder="Yeni şifrenizi girin"
                                       autocomplete="new-password">
                                <button type="button" class="stg-btn stg-btn-sm ap-toggle-pw" data-target="apNewPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                            <!-- Password Strength Meter -->
                            <div class="ap-pw-strength mt-2" id="pwStrengthWrap">
                                <div class="ap-pw-bars">
                                    <div class="ap-pw-bar" id="pwBar1"></div>
                                    <div class="ap-pw-bar" id="pwBar2"></div>
                                    <div class="ap-pw-bar" id="pwBar3"></div>
                                    <div class="ap-pw-bar" id="pwBar4"></div>
                                </div>
                                <small class="ap-pw-text" id="pwStrengthText"></small>
                            </div>

                            <!-- Password Requirements -->
                            <div class="ap-pw-requirements mt-2">
                                <div class="ap-pw-req" id="reqLength"><i class="bi bi-circle"></i> En az 8 karakter</div>
                                <div class="ap-pw-req" id="reqLower"><i class="bi bi-circle"></i> Küçük harf (a-z)</div>
                                <div class="ap-pw-req" id="reqUpper"><i class="bi bi-circle"></i> Büyük harf (A-Z)</div>
                                <div class="ap-pw-req" id="reqNumber"><i class="bi bi-circle"></i> Rakam (0-9)</div>
                                <div class="ap-pw-req" id="reqSpecial"><i class="bi bi-circle"></i> Özel karakter (@$!%*?&#._-)</div>
                            </div>
                        </div>

                        <div class="stg-field">
                            <label class="stg-label" for="apConfirmPassword">Yeni Şifre Tekrar</label>
                            <div class="stg-input-group">
                                <input type="password" class="stg-input"
                                       id="apConfirmPassword" name="password_confirmation"
                                       placeholder="Yeni şifrenizi tekrar girin"
                                       autocomplete="new-password">
                                <button type="button" class="stg-btn stg-btn-sm ap-toggle-pw" data-target="apConfirmPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="ap-pw-match mt-1" id="pwMatchStatus"></div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="stg-save-btn" id="passwordSubmitBtn" disabled>
                            <i class="bi bi-shield-check me-1"></i> Şifreyi Güncelle
                        </button>
                    </div>
                </form>
            </div>

        </div><!-- stg-content -->

    </div><!-- stg-layout -->

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/admin-profile.js') }}?v={{ filemtime(public_path('assets/admin/js/admin-profile.js')) }}"></script>
@endpush
