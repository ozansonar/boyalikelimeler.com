@extends('layouts.front')

@section('title', 'Profili Düzenle — Boyalı Kelimeler')
@section('meta_description', 'Boyalı Kelimeler profil bilgilerinizi düzenleyin.')
@section('canonical', route('profile.edit'))
@section('robots', 'noindex, nofollow')

@section('content')

    <section class="pedit-section" aria-label="Profil düzenleme">
        <div class="container">

            {{-- Page Header --}}
            <div class="pedit-header">
                <div class="pedit-header__left">
                    @if($user->username)
                        <a href="{{ route('profile.show', $user->username) }}" class="pedit-header__back">
                            <i class="fa-solid fa-arrow-left me-2"></i>Profile Dön
                        </a>
                    @endif
                    <h1 class="pedit-header__title">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Profili Düzenle
                    </h1>
                </div>
                <div class="pedit-header__actions">
                    @if($user->username)
                        <a href="{{ route('profile.show', $user->username) }}" class="pedit-btn pedit-btn--ghost">
                            <i class="fa-solid fa-xmark me-1"></i>İptal
                        </a>
                    @endif
                    <button type="submit" form="profileEditForm" class="pedit-btn pedit-btn--primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i>Değişiklikleri Kaydet
                    </button>
                </div>
            </div>

            <form id="profileEditForm" action="{{ route('profile.update') }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- LEFT: Nav Tabs --}}
                    <div class="col-lg-3">
                        <nav class="pedit-nav" aria-label="Düzenleme bölümleri">
                            <button type="button" class="pedit-nav__item pedit-nav__item--active" data-target="section-photos">
                                <i class="fa-solid fa-camera pedit-nav__icon"></i>
                                <span>Fotoğraflar</span>
                                <i class="fa-solid fa-chevron-right pedit-nav__chevron"></i>
                            </button>
                            <button type="button" class="pedit-nav__item" data-target="section-info">
                                <i class="fa-solid fa-user pedit-nav__icon"></i>
                                <span>Kişisel Bilgiler</span>
                                <i class="fa-solid fa-chevron-right pedit-nav__chevron"></i>
                            </button>
                            <button type="button" class="pedit-nav__item" data-target="section-social">
                                <i class="fa-solid fa-share-nodes pedit-nav__icon"></i>
                                <span>Sosyal Medya</span>
                                <i class="fa-solid fa-chevron-right pedit-nav__chevron"></i>
                            </button>
                            <button type="button" class="pedit-nav__item" data-target="section-privacy">
                                <i class="fa-solid fa-lock pedit-nav__icon"></i>
                                <span>Gizlilik & Güvenlik</span>
                                <i class="fa-solid fa-chevron-right pedit-nav__chevron"></i>
                            </button>
                            <button type="button" class="pedit-nav__item" data-target="section-notifications">
                                <i class="fa-solid fa-bell pedit-nav__icon"></i>
                                <span>Bildirimler</span>
                                <i class="fa-solid fa-chevron-right pedit-nav__chevron"></i>
                            </button>
                            <button type="button" class="pedit-nav__item pedit-nav__item--danger" data-target="section-danger">
                                <i class="fa-solid fa-triangle-exclamation pedit-nav__icon"></i>
                                <span>Tehlikeli Bölge</span>
                                <i class="fa-solid fa-chevron-right pedit-nav__chevron"></i>
                            </button>
                        </nav>

                        {{-- Writer Application CTA (non-writers only) --}}
                        @if(!$user->isYazar() && !$user->isAdmin() && !$user->isSuperAdmin())
                            <div class="writer-cta-card mt-3">
                                <div class="writer-cta-card__glow"></div>
                                <div class="writer-cta-card__icon">
                                    <i class="fa-solid fa-feather-pointed"></i>
                                </div>
                                <h4 class="writer-cta-card__title">Yazar Olmak İstiyor musunuz?</h4>
                                <p class="writer-cta-card__text">
                                    Eserlerinizi platformumuzda yayınlamak, topluluğumuzla buluşmak ve yarışmalara katılmak için yazar başvurusu yapın.
                                </p>
                                <button type="button"
                                        class="writer-cta-card__btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#writerApplicationModal"
                                        aria-haspopup="dialog">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Yazar Olma İsteğinde Bulun
                                </button>
                                <div class="writer-cta-card__note">
                                    <i class="fa-solid fa-shield-halved me-1"></i>Başvurunuz 3–5 iş günü içinde değerlendirilir
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- RIGHT: Sections --}}
                    <div class="col-lg-9">

                        {{-- Section: Photos --}}
                        <div class="pedit-section-panel" id="section-photos">

                            {{-- Cover Photo --}}
                            <div class="pedit-card">
                                <h3 class="pedit-card__title">
                                    <i class="fa-solid fa-image me-2"></i>Kapak Fotoğrafı
                                </h3>
                                <div class="pedit-cover-upload">
                                    <div class="pedit-cover-upload__preview" id="coverPreviewWrap">
                                        @if($user->cover_image_url)
                                            <img src="{{ $user->cover_image_url }}"
                                                 alt="Kapak fotoğrafı önizleme"
                                                 class="pedit-cover-upload__img"
                                                 id="coverPreviewImg"
                                                 loading="lazy">
                                        @else
                                            <img src="https://picsum.photos/1200/300?random={{ $user->id }}"
                                                 alt="Kapak fotoğrafı önizleme"
                                                 class="pedit-cover-upload__img"
                                                 id="coverPreviewImg"
                                                 loading="lazy">
                                        @endif
                                        <div class="pedit-cover-upload__overlay">
                                            <label for="coverInput" class="pedit-cover-upload__btn" aria-label="Kapak fotoğrafı değiştir">
                                                <i class="fa-solid fa-camera me-2"></i>Kapak Fotoğrafını Değiştir
                                            </label>
                                        </div>
                                    </div>
                                    <input type="file"
                                           class="pedit-file-input"
                                           id="coverInput"
                                           name="cover_photo"
                                           accept="image/*"
                                           aria-label="Kapak fotoğrafı seç">
                                    <p class="pedit-file-hint">
                                        <i class="fa-solid fa-circle-info me-1"></i>Önerilen boyut: 1920×400px. Maks. 5MB. JPG, PNG veya WebP.
                                    </p>
                                </div>
                            </div>

                            {{-- Avatar Photo --}}
                            <div class="pedit-card">
                                <h3 class="pedit-card__title">
                                    <i class="fa-solid fa-circle-user me-2"></i>Profil Fotoğrafı
                                </h3>
                                <div class="pedit-avatar-upload">
                                    <div class="pedit-avatar-upload__current">
                                        <div class="pedit-avatar-upload__ring">
                                            @if($user->avatar_url)
                                                <img src="{{ $user->avatar_url }}"
                                                     alt="Profil fotoğrafı önizleme"
                                                     class="pedit-avatar-upload__img"
                                                     id="avatarPreviewImg"
                                                     loading="lazy">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=200&background=D4AF37&color=1A1A1E&bold=true"
                                                     alt="Profil fotoğrafı önizleme"
                                                     class="pedit-avatar-upload__img"
                                                     id="avatarPreviewImg"
                                                     loading="lazy">
                                            @endif
                                            <label for="avatarInput" class="pedit-avatar-upload__overlay" aria-label="Profil fotoğrafını değiştir">
                                                <i class="fa-solid fa-camera"></i>
                                            </label>
                                        </div>
                                        <div class="pedit-avatar-upload__info">
                                            <p class="pedit-avatar-upload__name">{{ $user->name }}</p>
                                            <p class="pedit-avatar-upload__username">{{ $user->username ? '@' . $user->username : '' }}</p>
                                            <label for="avatarInput" class="pedit-btn pedit-btn--sm pedit-btn--outline">
                                                <i class="fa-solid fa-upload me-1"></i>Fotoğraf Yükle
                                            </label>
                                        </div>
                                    </div>
                                    <input type="file"
                                           class="pedit-file-input"
                                           id="avatarInput"
                                           name="avatar"
                                           accept="image/*"
                                           aria-label="Profil fotoğrafı seç">
                                    <p class="pedit-file-hint">
                                        <i class="fa-solid fa-circle-info me-1"></i>Önerilen boyut: 400×400px. Maks. 2MB. JPG, PNG veya WebP.
                                    </p>
                                </div>
                            </div>

                        </div>

                        {{-- Section: Personal Info --}}
                        <div class="pedit-section-panel d-none" id="section-info">

                            <div class="pedit-card">
                                <h3 class="pedit-card__title">
                                    <i class="fa-solid fa-user me-2"></i>Kişisel Bilgiler
                                </h3>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_name">
                                                Ad Soyad <span class="pedit-form__required">*</span>
                                            </label>
                                            <input type="text" class="pedit-form__input" id="pf_name"
                                                   name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_username">
                                                Kullanıcı Adı <span class="pedit-form__required">*</span>
                                            </label>
                                            <div class="pedit-form__input-prefix-wrap">
                                                <span class="pedit-form__prefix">@</span>
                                                <input type="text" class="pedit-form__input pedit-form__input--prefixed"
                                                       id="pf_username" name="username" value="{{ old('username', $user->username) }}"
                                                       required minlength="3" maxlength="30"
                                                       pattern="[a-zA-Z0-9_]+">
                                            </div>
                                            <span class="pedit-form__hint">
                                                <i class="fa-solid fa-circle-info me-1"></i>Yalnızca harf, rakam ve alt çizgi. 3–30 karakter.
                                            </span>
                                            <span class="pedit-form__username-status d-none" id="usernameStatus"></span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_bio">
                                                Hakkımda <span class="pedit-form__char-info">
                                                    <span id="bioCharCount">0</span>/300
                                                </span>
                                            </label>
                                            <textarea class="pedit-form__input pedit-form__textarea"
                                                      id="pf_bio"
                                                      name="bio"
                                                      rows="4"
                                                      maxlength="300"
                                                      placeholder="Kendinizi kısaca tanıtın...">{{ old('bio', $user->bio) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_about">
                                                Uzun Hakkında Yazısı
                                            </label>
                                            <textarea class="pedit-form__input pedit-form__textarea"
                                                      id="pf_about"
                                                      name="about"
                                                      rows="5"
                                                      placeholder="Profilinizde görünecek detaylı hakkında yazısı...">{{ old('about', $user->about) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_location">
                                                <i class="fa-solid fa-location-dot me-1"></i>Konum
                                            </label>
                                            <input type="text" class="pedit-form__input" id="pf_location"
                                                   name="location" value="{{ old('location', $user->location) }}"
                                                   placeholder="Şehir, Ülke" autocomplete="address-level2">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_website">
                                                <i class="fa-solid fa-globe me-1"></i>Website
                                            </label>
                                            <input type="url" class="pedit-form__input" id="pf_website"
                                                   name="website" value="{{ old('website', $user->website) }}"
                                                   placeholder="https://blogunuz.com">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_email">
                                                <i class="fa-solid fa-envelope me-1"></i>E-posta <span class="pedit-form__required">*</span>
                                            </label>
                                            <input type="email" class="pedit-form__input" id="pf_email"
                                                   name="email" value="{{ old('email', $user->email) }}"
                                                   required autocomplete="email">
                                            <span class="pedit-form__hint">
                                                <i class="fa-solid fa-circle-info me-1"></i>E-postanız değiştiğinde doğrulama e-postası gönderilecek.
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_birthdate">
                                                <i class="fa-solid fa-cake-candles me-1"></i>Doğum Tarihi
                                            </label>
                                            <input type="date" class="pedit-form__input" id="pf_birthdate"
                                                   name="birthdate" value="{{ old('birthdate', $user->birthdate?->format('Y-m-d')) }}">
                                            <span class="pedit-form__hint">
                                                <i class="fa-solid fa-circle-info me-1"></i>Profilinizde gösterilmez, hesap doğrulama için kullanılır.
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_gender">
                                                <i class="fa-solid fa-venus-mars me-1"></i>Cinsiyet
                                            </label>
                                            <select class="pedit-form__select" id="pf_gender" name="gender">
                                                <option value="">Belirtmek istemiyorum</option>
                                                @foreach(\App\Enums\Gender::cases() as $genderOption)
                                                    <option value="{{ $genderOption->value }}" @selected(old('gender', $user->gender?->value) === $genderOption->value)>{{ $genderOption->label() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Interest Tags --}}
                            <div class="pedit-card">
                                <h3 class="pedit-card__title">
                                    <i class="fa-solid fa-tags me-2"></i>İlgi Alanları
                                </h3>
                                <p class="pedit-card__desc">Profilinizde görünmesini istediğiniz alanları seçin.</p>
                                @php $userInterests = $user->interests ?? []; @endphp
                                <div class="pedit-interest-grid">
                                    <label class="pedit-interest__item {{ in_array('Şiir', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Şiir" @checked(in_array('Şiir', $userInterests))>
                                        <i class="fa-solid fa-feather-pointed"></i>Şiir
                                    </label>
                                    <label class="pedit-interest__item {{ in_array('Kısa Hikaye', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Kısa Hikaye" @checked(in_array('Kısa Hikaye', $userInterests))>
                                        <i class="fa-solid fa-book"></i>Kısa Hikaye
                                    </label>
                                    <label class="pedit-interest__item {{ in_array('Deneme', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Deneme" @checked(in_array('Deneme', $userInterests))>
                                        <i class="fa-solid fa-scroll"></i>Deneme
                                    </label>
                                    <label class="pedit-interest__item {{ in_array('Sulu Boya', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Sulu Boya" @checked(in_array('Sulu Boya', $userInterests))>
                                        <i class="fa-solid fa-paintbrush"></i>Sulu Boya
                                    </label>
                                    <label class="pedit-interest__item {{ in_array('Roman', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Roman" @checked(in_array('Roman', $userInterests))>
                                        <i class="fa-solid fa-book-open"></i>Roman
                                    </label>
                                    <label class="pedit-interest__item {{ in_array('Felsefe', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Felsefe" @checked(in_array('Felsefe', $userInterests))>
                                        <i class="fa-solid fa-brain"></i>Felsefe
                                    </label>
                                    <label class="pedit-interest__item {{ in_array('Romantizm', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Romantizm" @checked(in_array('Romantizm', $userInterests))>
                                        <i class="fa-solid fa-heart"></i>Romantizm
                                    </label>
                                    <label class="pedit-interest__item {{ in_array('Eleştiri', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Eleştiri" @checked(in_array('Eleştiri', $userInterests))>
                                        <i class="fa-solid fa-magnifying-glass"></i>Eleştiri
                                    </label>
                                    <label class="pedit-interest__item {{ in_array('Mitoloji', $userInterests) ? 'pedit-interest__item--active' : '' }}">
                                        <input type="checkbox" name="interests[]" value="Mitoloji" @checked(in_array('Mitoloji', $userInterests))>
                                        <i class="fa-solid fa-star"></i>Mitoloji
                                    </label>
                                </div>
                            </div>

                        </div>

                        {{-- Section: Social Media --}}
                        <div class="pedit-section-panel d-none" id="section-social">

                            <div class="pedit-card">
                                <h3 class="pedit-card__title">
                                    <i class="fa-solid fa-share-nodes me-2"></i>Sosyal Medya Hesapları
                                </h3>
                                <p class="pedit-card__desc">Profilinizde görünecek sosyal medya bağlantılarınızı girin.</p>

                                <div class="row g-3">
                                    {{-- Instagram --}}
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_instagram">
                                                <i class="fa-brands fa-instagram pedit-form__social-icon pedit-form__social-icon--instagram me-1"></i>Instagram
                                            </label>
                                            <div class="pedit-form__input-prefix-wrap">
                                                <span class="pedit-form__prefix">instagram.com/</span>
                                                <input type="text" class="pedit-form__input pedit-form__input--prefixed"
                                                       id="pf_instagram" name="instagram" value="{{ old('instagram', $user->instagram) }}"
                                                       placeholder="kullaniciadi">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Twitter / X --}}
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_twitter">
                                                <i class="fa-brands fa-x-twitter pedit-form__social-icon pedit-form__social-icon--twitter me-1"></i>Twitter / X
                                            </label>
                                            <div class="pedit-form__input-prefix-wrap">
                                                <span class="pedit-form__prefix">x.com/</span>
                                                <input type="text" class="pedit-form__input pedit-form__input--prefixed"
                                                       id="pf_twitter" name="twitter" value="{{ old('twitter', $user->twitter) }}"
                                                       placeholder="kullaniciadi">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- YouTube --}}
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_youtube">
                                                <i class="fa-brands fa-youtube pedit-form__social-icon pedit-form__social-icon--youtube me-1"></i>YouTube
                                            </label>
                                            <div class="pedit-form__input-prefix-wrap">
                                                <span class="pedit-form__prefix">youtube.com/@</span>
                                                <input type="text" class="pedit-form__input pedit-form__input--prefixed"
                                                       id="pf_youtube" name="youtube" value="{{ old('youtube', $user->youtube) }}"
                                                       placeholder="kanal-adi">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- TikTok --}}
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_tiktok">
                                                <i class="fa-brands fa-tiktok pedit-form__social-icon me-1"></i>TikTok
                                            </label>
                                            <div class="pedit-form__input-prefix-wrap">
                                                <span class="pedit-form__prefix">tiktok.com/@</span>
                                                <input type="text" class="pedit-form__input pedit-form__input--prefixed"
                                                       id="pf_tiktok" name="tiktok" value="{{ old('tiktok', $user->tiktok) }}"
                                                       placeholder="kullaniciadi">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Spotify --}}
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_spotify">
                                                <i class="fa-brands fa-spotify pedit-form__social-icon pedit-form__social-icon--spotify me-1"></i>Spotify (Podcast)
                                            </label>
                                            <div class="pedit-form__input-prefix-wrap">
                                                <span class="pedit-form__prefix">open.spotify.com/user/</span>
                                                <input type="text" class="pedit-form__input pedit-form__input--prefixed"
                                                       id="pf_spotify" name="spotify" value="{{ old('spotify', $user->spotify) }}"
                                                       placeholder="kullanici-id">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Section: Privacy & Security --}}
                        <div class="pedit-section-panel d-none" id="section-privacy">

                            {{-- Change Password --}}
                            <div class="pedit-card">
                                <h3 class="pedit-card__title">
                                    <i class="fa-solid fa-key me-2"></i>Şifre Değiştir
                                </h3>
                                <p class="pedit-card__desc">Şifre değişikliği ayrı form ile gönderilir.</p>
                            </div>
                            <form action="{{ route('profile.password') }}" method="POST" class="pedit-card">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_oldPassword">Mevcut Şifre</label>
                                            <div class="pedit-form__password-wrap">
                                                <input type="password" class="pedit-form__input" id="pf_oldPassword"
                                                       name="old_password" placeholder="Mevcut şifreniz"
                                                       autocomplete="current-password">
                                                <button type="button" class="pedit-form__eye" data-target="pf_oldPassword" aria-label="Şifreyi göster/gizle">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_newPassword">Yeni Şifre</label>
                                            <div class="pedit-form__password-wrap">
                                                <input type="password" class="pedit-form__input" id="pf_newPassword"
                                                       name="password" placeholder="En az 8 karakter"
                                                       minlength="8" autocomplete="new-password">
                                                <button type="button" class="pedit-form__eye" data-target="pf_newPassword" aria-label="Şifreyi göster/gizle">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pedit-form__group">
                                            <label class="pedit-form__label" for="pf_confirmPassword">Yeni Şifre Tekrar</label>
                                            <div class="pedit-form__password-wrap">
                                                <input type="password" class="pedit-form__input" id="pf_confirmPassword"
                                                       name="password_confirmation" placeholder="Şifreyi tekrar girin"
                                                       minlength="8" autocomplete="new-password">
                                                <button type="button" class="pedit-form__eye" data-target="pf_confirmPassword" aria-label="Şifreyi göster/gizle">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="pedit-btn pedit-btn--primary">
                                            <i class="fa-solid fa-key me-1"></i>Şifreyi Değiştir
                                        </button>
                                    </div>
                                </div>
                            </form>

                            {{-- Privacy Settings --}}
                            <div class="pedit-card">
                                <h3 class="pedit-card__title">
                                    <i class="fa-solid fa-shield-halved me-2"></i>Gizlilik Ayarları
                                </h3>
                                <div class="pedit-toggle-list">
                                    <div class="pedit-toggle__item">
                                        <div class="pedit-toggle__info">
                                            <span class="pedit-toggle__label">Profilimi herkese açık göster</span>
                                            <span class="pedit-toggle__desc">Kapalıysa yalnızca takipçileriniz profilinizi görebilir</span>
                                        </div>
                                        <label class="pedit-toggle__switch" for="toggle_public">
                                            <input type="checkbox" id="toggle_public" name="is_public" value="1" @checked(old('is_public', $user->is_public))>
                                            <span class="pedit-toggle__track"></span>
                                        </label>
                                    </div>
                                    <div class="pedit-toggle__item">
                                        <div class="pedit-toggle__info">
                                            <span class="pedit-toggle__label">E-posta adresimi göster</span>
                                            <span class="pedit-toggle__desc">E-postanız profilinizde görünür</span>
                                        </div>
                                        <label class="pedit-toggle__switch" for="toggle_email">
                                            <input type="checkbox" id="toggle_email" name="show_email" value="1" @checked(old('show_email', $user->show_email))>
                                            <span class="pedit-toggle__track"></span>
                                        </label>
                                    </div>
                                    <div class="pedit-toggle__item">
                                        <div class="pedit-toggle__info">
                                            <span class="pedit-toggle__label">Son görülmeyi göster</span>
                                            <span class="pedit-toggle__desc">Çevrimiçi durumunuz diğer üyelere görünür</span>
                                        </div>
                                        <label class="pedit-toggle__switch" for="toggle_lastseen">
                                            <input type="checkbox" id="toggle_lastseen" name="show_last_seen" value="1" @checked(old('show_last_seen', $user->show_last_seen))>
                                            <span class="pedit-toggle__track"></span>
                                        </label>
                                    </div>
                                    <div class="pedit-toggle__item">
                                        <div class="pedit-toggle__info">
                                            <span class="pedit-toggle__label">Özel mesaj almayı kabul et</span>
                                            <span class="pedit-toggle__desc">Kapalıysa sadece takipçileriniz mesaj gönderebilir</span>
                                        </div>
                                        <label class="pedit-toggle__switch" for="toggle_msg">
                                            <input type="checkbox" id="toggle_msg" name="allow_messages" value="1" @checked(old('allow_messages', $user->allow_messages))>
                                            <span class="pedit-toggle__track"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Section: Notifications --}}
                        <div class="pedit-section-panel d-none" id="section-notifications">

                            <div class="pedit-card">
                                <h3 class="pedit-card__title">
                                    <i class="fa-solid fa-bell me-2"></i>Bildirim Tercihleri
                                </h3>
                                <p class="pedit-card__desc">Hangi durumlarda e-posta bildirimi almak istediğinizi seçin.</p>
                                <div class="pedit-toggle-list">
                                    <div class="pedit-toggle__item">
                                        <div class="pedit-toggle__info">
                                            <span class="pedit-toggle__label"><i class="fa-solid fa-comment me-1 text-gold"></i>Yorum onay bildirimi</span>
                                            <span class="pedit-toggle__desc">İçeriğinize yapılan bir yorum onaylandığında e-posta alın</span>
                                        </div>
                                        <label class="pedit-toggle__switch" for="notif_comment_approved">
                                            <input type="checkbox" id="notif_comment_approved" name="notify_comment_approved" value="1" @checked(old('notify_comment_approved', $user->notify_comment_approved))>
                                            <span class="pedit-toggle__track"></span>
                                        </label>
                                    </div>
                                    <div class="pedit-toggle__item">
                                        <div class="pedit-toggle__info">
                                            <span class="pedit-toggle__label"><i class="fa-solid fa-feather-pointed me-1 text-gold"></i>Eser durum bildirimi</span>
                                            <span class="pedit-toggle__desc">Eseriniz onaylandığında, reddedildiğinde veya revize istendiğinde e-posta alın</span>
                                        </div>
                                        <label class="pedit-toggle__switch" for="notif_work_status">
                                            <input type="checkbox" id="notif_work_status" name="notify_work_status" value="1" @checked(old('notify_work_status', $user->notify_work_status))>
                                            <span class="pedit-toggle__track"></span>
                                        </label>
                                    </div>
                                    <div class="pedit-toggle__item">
                                        <div class="pedit-toggle__info">
                                            <span class="pedit-toggle__label"><i class="fa-solid fa-envelope me-1 text-gold"></i>Yeni yorum bildirimi</span>
                                            <span class="pedit-toggle__desc">Yeni yorum geldiğinde e-posta alın (admin)</span>
                                        </div>
                                        <label class="pedit-toggle__switch" for="notif_new_comment">
                                            <input type="checkbox" id="notif_new_comment" name="notify_new_comment" value="1" @checked(old('notify_new_comment', $user->notify_new_comment))>
                                            <span class="pedit-toggle__track"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Section: Danger Zone --}}
                        <div class="pedit-section-panel d-none" id="section-danger">

                            <div class="pedit-card pedit-card--danger">
                                <h3 class="pedit-card__title pedit-card__title--danger">
                                    <i class="fa-solid fa-triangle-exclamation me-2"></i>Tehlikeli Bölge
                                </h3>

                                <div class="pedit-danger-list">
                                    <div class="pedit-danger__item">
                                        <div class="pedit-danger__info">
                                            <span class="pedit-danger__label">Hesabı Devre Dışı Bırak</span>
                                            <span class="pedit-danger__desc">Hesabınız geçici olarak gizlenir. İstediğinizde tekrar aktif edebilirsiniz.</span>
                                        </div>
                                        <button type="button" class="pedit-danger__btn pedit-danger__btn--warn">
                                            <i class="fa-solid fa-eye-slash me-1"></i>Devre Dışı Bırak
                                        </button>
                                    </div>
                                    <div class="pedit-danger__item">
                                        <div class="pedit-danger__info">
                                            <span class="pedit-danger__label">Hesabı Kalıcı Olarak Sil</span>
                                            <span class="pedit-danger__desc">Tüm yazılarınız, resimleriniz ve verileriniz kalıcı olarak silinir. Bu işlem geri alınamaz.</span>
                                        </div>
                                        <button type="button" class="pedit-danger__btn pedit-danger__btn--delete">
                                            <i class="fa-solid fa-trash me-1"></i>Hesabı Sil
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Save Button (bottom) --}}
                        <div class="pedit-form__save-bar" id="saveBar">
                            <p class="pedit-form__save-info">
                                <i class="fa-solid fa-circle-info me-1"></i>Değişiklikler kaydedilmedi
                            </p>
                            <div class="d-flex gap-2">
                                @if($user->username)
                                    <a href="{{ route('profile.show', $user->username) }}" class="pedit-btn pedit-btn--ghost">
                                        <i class="fa-solid fa-xmark me-1"></i>İptal
                                    </a>
                                @endif
                                <button type="submit" class="pedit-btn pedit-btn--primary">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>Kaydet
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </section>

    {{-- Writer Application Modal (non-writers only) --}}
    @if(!$user->isYazar() && !$user->isAdmin() && !$user->isSuperAdmin())
        @include('front.profile._writer-modal')
    @endif

@endsection

@push('scripts')
    <script src="{{ asset('js/profile-edit.js') }}"></script>
    @if(!$user->isYazar() && !$user->isAdmin() && !$user->isSuperAdmin())
        <script src="{{ asset('js/profile.js') }}"></script>
    @endif
@endpush
