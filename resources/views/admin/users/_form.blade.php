{{-- Shared form partial for create & edit --}}
@php
    $isEdit = isset($user);
    $nameParts = $isEdit ? explode(' ', $user->name, 2) : ['', ''];
    $yazarRoleId = $roles->firstWhere('slug', 'yazar')?->id;
    $currentRoleId = old('role_id', $user->role_id ?? '');
    $isYazar = (string) $currentRoleId === (string) $yazarRoleId;
@endphp

<!-- ==================== FORM LAYOUT ==================== -->
<div class="stg-layout">

    <!-- Left Navigation -->
    <div class="stg-nav d-none d-lg-block" data-aos="fade-right" data-aos-delay="100">
        <a href="#section-personal" class="stg-nav-item active" onclick="scrollToSection('section-personal', this)">
            <i class="bi bi-person"></i> Kişisel Bilgiler
        </a>
        <a href="#section-account" class="stg-nav-item" onclick="scrollToSection('section-account', this)">
            <i class="bi bi-key"></i> Hesap Bilgileri
        </a>
        <a href="#section-role" class="stg-nav-item" onclick="scrollToSection('section-role', this)">
            <i class="bi bi-shield"></i> Rol & Yetki
        </a>
        <a href="#section-golden-pen" class="stg-nav-item" id="goldenPenNavItem" onclick="scrollToSection('section-golden-pen', this)" {!! !$isYazar ? 'style="display:none"' : '' !!}>
            <i class="bi bi-pen"></i> Altın Kalem
        </a>
    </div>

    <!-- Mobile Nav -->
    <div class="d-lg-none mb-3">
        <select class="stg-select" onchange="scrollToSection(this.value, null)" id="mobileNavSelect">
            <option value="section-personal">Kişisel Bilgiler</option>
            <option value="section-account">Hesap Bilgileri</option>
            <option value="section-role">Rol & Yetki</option>
            <option value="section-golden-pen" id="goldenPenMobileOption" {!! !$isYazar ? 'style="display:none"' : '' !!}>Altın Kalem</option>
        </select>
    </div>

    <!-- Form Content -->
    <div class="stg-content">

        <!-- ==================== SECTION 1: PERSONAL INFO ==================== -->
        <div class="card-dark mb-4" id="section-personal" data-aos="fade-up" data-aos-delay="0">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon"><i class="bi bi-person-fill"></i></div>
                    <div>
                        <h6 class="mb-0">Kişisel Bilgiler</h6>
                        <small class="text-muted">Kullanıcının temel kişisel bilgilerini girin</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="stg-label">Ad <span class="text-neon-red">*</span></label>
                        <input type="text" class="stg-input @error('first_name') is-invalid @enderror"
                               name="first_name"
                               value="{{ old('first_name', $nameParts[0] ?? '') }}"
                               placeholder="Kullanıcının adı" required>
                        @error('first_name')
                            <small class="text-neon-red">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="stg-label">Soyad <span class="text-neon-red">*</span></label>
                        <input type="text" class="stg-input @error('last_name') is-invalid @enderror"
                               name="last_name"
                               value="{{ old('last_name', $nameParts[1] ?? '') }}"
                               placeholder="Kullanıcının soyadı" required>
                        @error('last_name')
                            <small class="text-neon-red">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="stg-label">E-posta Adresi <span class="text-neon-red">*</span></label>
                        <div class="stg-input-group">
                            <span class="stg-input-prefix"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="stg-input @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email', $user->email ?? '') }}"
                                   placeholder="ornek@mail.com" required>
                        </div>
                        @error('email')
                            <small class="text-neon-red">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 2: ACCOUNT INFO ==================== -->
        <div class="card-dark mb-4" id="section-account" data-aos="fade-up" data-aos-delay="50">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon"><i class="bi bi-key-fill"></i></div>
                    <div>
                        <h6 class="mb-0">Hesap Bilgileri</h6>
                        <small class="text-muted">Giriş ve güvenlik ile ilgili ayarlar</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="stg-label">Şifre @unless($isEdit)<span class="text-neon-red">*</span>@endunless</label>
                        <div class="stg-input-group">
                            <span class="stg-input-prefix"><i class="bi bi-lock"></i></span>
                            <input type="password" class="stg-input @error('password') is-invalid @enderror"
                                   name="password" id="password"
                                   placeholder="Güçlü bir şifre girin" {{ $isEdit ? '' : 'required' }}>
                            <button type="button" class="stg-input-prefix btn-unstyled" onclick="togglePassword('password', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="uf-password-strength mt-2 d-none" id="passwordStrength">
                            <div class="uf-strength-bars">
                                <div class="uf-strength-bar"></div>
                                <div class="uf-strength-bar"></div>
                                <div class="uf-strength-bar"></div>
                                <div class="uf-strength-bar"></div>
                            </div>
                            <small id="strengthText">Şifre gücü</small>
                        </div>
                        @error('password')
                            <small class="text-neon-red">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="stg-label">Şifre Tekrar @unless($isEdit)<span class="text-neon-red">*</span>@endunless</label>
                        <div class="stg-input-group">
                            <span class="stg-input-prefix"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="stg-input"
                                   name="password_confirmation" id="passwordConfirm"
                                   placeholder="Şifreyi tekrar girin" {{ $isEdit ? '' : 'required' }}>
                            <button type="button" class="stg-input-prefix btn-unstyled" onclick="togglePassword('passwordConfirm', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    @if($isEdit)
                        <div class="col-12">
                            <div class="uf-info-box">
                                <i class="bi bi-info-circle-fill text-neon-blue"></i>
                                <span>Şifre alanlarını boş bırakırsanız mevcut şifre korunur. Yalnızca değiştirmek istiyorsanız doldurun.</span>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="stg-label">E-posta Doğrulaması</label>
                        <select class="stg-select" name="email_verified">
                            <option value="0" {{ old('email_verified', ($isEdit && $user->hasVerifiedEmail()) ? '1' : '0') === '0' ? 'selected' : '' }}>Doğrulanmamış</option>
                            <option value="1" {{ old('email_verified', ($isEdit && $user->hasVerifiedEmail()) ? '1' : '0') === '1' ? 'selected' : '' }}>Doğrulanmış</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 3: ROLE ==================== -->
        <div class="card-dark mb-4" id="section-role" data-aos="fade-up" data-aos-delay="50">
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <div>
                        <h6 class="mb-0">Rol & Yetki</h6>
                        <small class="text-muted">Kullanıcının rolünü belirleyin</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="stg-label">Rol <span class="text-neon-red">*</span></label>
                        <select class="stg-select @error('role_id') is-invalid @enderror" name="role_id" id="userRole" required>
                            <option value="">Rol Seçin</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <small class="text-neon-red">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="stg-label">Rol Açıklamaları</label>
                        <div class="uf-role-cards">
                            @php
                                $roleDescriptions = [
                                    'super_admin' => ['icon' => 'bi-shield-fill', 'accent' => 'accent-teal', 'title' => 'Süper Admin', 'desc' => 'Tam erişim. Tüm modüller ve sistem ayarlarına erişebilir.'],
                                    'admin' => ['icon' => 'bi-shield-fill', 'accent' => 'accent-blue', 'title' => 'Admin', 'desc' => 'Kullanıcı yönetimi, içerik yönetimi ve ayarlara erişebilir.'],
                                    'yazar' => ['icon' => 'bi-pencil-fill', 'accent' => 'accent-purple', 'title' => 'Yazar', 'desc' => 'İçerik oluşturma ve düzenleme yetkisi. Yönetim yapamaz.'],
                                    'kullanici' => ['icon' => 'bi-person-fill', 'accent' => 'accent-green', 'title' => 'Kullanıcı', 'desc' => 'Temel kullanım hakları. Kendi profilini düzenleyebilir.'],
                                ];
                            @endphp
                            @foreach($roles as $role)
                                @php $rd = $roleDescriptions[$role->slug] ?? ['icon' => 'bi-person-fill', 'accent' => 'accent-green', 'title' => $role->name, 'desc' => '']; @endphp
                                <div class="uf-role-card" data-role-id="{{ $role->id }}">
                                    <div class="uf-role-card-icon {{ $rd['accent'] }}"><i class="bi {{ $rd['icon'] }}"></i></div>
                                    <div class="uf-role-card-info">
                                        <strong>{{ $rd['title'] }}</strong>
                                        <small>{{ $rd['desc'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== SECTION 4: GOLDEN PEN ==================== -->
        <div class="card-dark mb-4" id="section-golden-pen" data-aos="fade-up" data-aos-delay="50" {!! !$isYazar ? 'style="display:none"' : '' !!}>
            <div class="card-header-custom">
                <div class="form-section-header mb-0">
                    <div class="form-section-icon"><i class="bi bi-pen-fill"></i></div>
                    <div>
                        <h6 class="mb-0">Altın Kalem Unvanı</h6>
                        <small class="text-muted">Yazara özel altın kalem unvanı verin</small>
                    </div>
                </div>
            </div>
            <div class="card-body-custom">
                <div class="stg-toggle-list">
                    <div class="stg-toggle-item">
                        <div class="stg-toggle-info">
                            <span>Altın Kalem Unvanı Verilsin mi?</span>
                            <small>Etkinleştirildiğinde yazar, belirlenen tarihler arasında altın kalem unvanına sahip olur.</small>
                        </div>
                        <label class="stg-switch">
                            <input type="checkbox" name="is_golden_pen" value="1" id="goldenPenToggle"
                                   {{ old('is_golden_pen', ($isEdit && $user->is_golden_pen) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <span class="stg-switch-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="row g-3 mt-2" id="goldenPenDates"
                     {!! old('is_golden_pen', ($isEdit && $user->is_golden_pen) ? '1' : '0') != '1' ? 'style="display:none"' : '' !!}>
                    <div class="col-md-6">
                        <label class="stg-label">Başlangıç Tarihi <span class="text-neon-red">*</span></label>
                        <div class="stg-input-group">
                            <span class="stg-input-prefix"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" class="stg-input @error('golden_pen_starts_at') is-invalid @enderror"
                                   name="golden_pen_starts_at" id="goldenPenStartsAt"
                                   value="{{ old('golden_pen_starts_at', ($isEdit && $user->golden_pen_starts_at) ? $user->golden_pen_starts_at->format('Y-m-d') : '') }}">
                        </div>
                        @error('golden_pen_starts_at')
                            <small class="text-neon-red">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="stg-label">Bitiş Tarihi <span class="text-neon-red">*</span></label>
                        <div class="stg-input-group">
                            <span class="stg-input-prefix"><i class="bi bi-calendar-check"></i></span>
                            <input type="date" class="stg-input @error('golden_pen_ends_at') is-invalid @enderror"
                                   name="golden_pen_ends_at" id="goldenPenEndsAt"
                                   value="{{ old('golden_pen_ends_at', ($isEdit && $user->golden_pen_ends_at) ? $user->golden_pen_ends_at->format('Y-m-d') : '') }}">
                        </div>
                        @error('golden_pen_ends_at')
                            <small class="text-neon-red">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-12">
                        <div class="uf-info-box">
                            <i class="bi bi-info-circle-fill text-neon-blue"></i>
                            <span>Yazar, belirlenen başlangıç ve bitiş tarihleri arasında altın kalem unvanına sahip olacaktır.</span>
                        </div>
                    </div>
                    @if($isEdit && $user->is_golden_pen)
                        <div class="col-12">
                            @if($user->hasActiveGoldenPen())
                                <div class="uf-golden-pen-status uf-golden-pen-active">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Altın Kalem unvanı şu an <strong>aktif</strong>.</span>
                                </div>
                            @else
                                <div class="uf-golden-pen-status uf-golden-pen-expired">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                    <span>Altın Kalem unvanı <strong>tarih aralığı dışında</strong>.</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <!-- Bottom Actions -->
        <div class="uf-bottom-actions" data-aos="fade-up">
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn-glass"><i class="bi bi-x-lg me-1"></i>İptal</a>
            </div>
            <div>
                <button type="submit" class="btn-teal">
                    <i class="bi bi-check2 me-1"></i>
                    {{ $isEdit ? 'Değişiklikleri Kaydet' : 'Kullanıcı Oluştur' }}
                </button>
            </div>
        </div>

    </div>
</div>
