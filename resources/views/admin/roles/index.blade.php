@extends('layouts.admin')

@section('title', 'Roller & İzinler — Admin')

@section('content')

    <!-- Page Header -->
    <x-admin.page-header title="Roller & İzinler" subtitle="Sistem rollerini yönetin, izinleri yapılandırın ve erişim kontrolünü sağlayın">
        @if(auth()->user()->hasPermission('roles.create'))
            <button class="btn-teal" data-bs-toggle="modal" data-bs-target="#roleModal"><i class="bi bi-plus-lg me-1"></i> Yeni Rol Oluştur</button>
        @endif
    </x-admin.page-header>


    <!-- ==================== SECTION 1: STATS ==================== -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="0">
            <div class="rp-stat-card">
                <div class="rp-stat-icon accent-teal"><i class="bi bi-shield-fill"></i></div>
                <div class="rp-stat-info">
                    <span class="rp-stat-label">Toplam Rol</span>
                    <h3 class="rp-stat-value">{{ $stats['total_roles'] }}</h3>
                    <span class="rp-stat-sub">{{ $roles->count() }} aktif rol</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="50">
            <div class="rp-stat-card">
                <div class="rp-stat-icon accent-purple"><i class="bi bi-key-fill"></i></div>
                <div class="rp-stat-info">
                    <span class="rp-stat-label">Toplam İzin</span>
                    <h3 class="rp-stat-value">{{ $stats['total_permissions'] }}</h3>
                    <span class="rp-stat-sub">{{ $stats['permission_groups'] }} kategori</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
            <div class="rp-stat-card">
                <div class="rp-stat-icon accent-blue"><i class="bi bi-people-fill"></i></div>
                <div class="rp-stat-info">
                    <span class="rp-stat-label">Atanmış Kullanıcı</span>
                    <h3 class="rp-stat-value">{{ number_format($stats['total_users']) }}</h3>
                    <span class="rp-stat-sub">tüm roller dahil</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6" data-aos="fade-up" data-aos-delay="150">
            <div class="rp-stat-card">
                <div class="rp-stat-icon accent-orange"><i class="bi bi-clock-history"></i></div>
                <div class="rp-stat-info">
                    <span class="rp-stat-label">Son Güncelleme</span>
                    <h3 class="rp-stat-value fs-18">
                        @if($roles->max('updated_at'))
                            {{ $roles->max('updated_at')->diffForHumans() }}
                        @else
                            —
                        @endif
                    </h3>
                    <span class="rp-stat-sub">son rol güncellemesi</span>
                </div>
            </div>
        </div>
    </div>


    <!-- ==================== SECTION 2: ROLE CARDS ==================== -->
    <div class="form-section-header" data-aos="fade-up" data-aos-delay="100">
        <div class="form-section-icon"><i class="bi bi-shield-fill-check"></i></div>
        <div>
            <h6 class="mb-0">Sistem Rolleri</h6>
            <small class="text-muted">Rollere tıklayarak detaylarını görüntüleyin ve düzenleyin</small>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @php
            $roleColors = [
                'super_admin' => ['icon' => 'bi-shield-fill', 'accent' => 'accent-teal'],
                'admin'       => ['icon' => 'bi-shield-fill-check', 'accent' => 'accent-blue'],
                'yazar'       => ['icon' => 'bi-pencil-fill', 'accent' => 'accent-purple'],
                'kullanici'   => ['icon' => 'bi-person-fill', 'accent' => 'accent-green'],
            ];
            $defaultColor = ['icon' => 'bi-shield-fill', 'accent' => 'accent-orange'];
            $totalPermissions = $stats['total_permissions'];
        @endphp

        @foreach($roles as $role)
            @php
                $color = $roleColors[$role->slug] ?? $defaultColor;
                $permCount = $role->permissions->count();
                $isSystem = in_array($role->slug, ['super_admin', 'admin']);
                $roleUsers = $role->users_count;
            @endphp
            <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 + 100 }}">
                <div class="rp-role-card{{ $loop->first ? ' active' : '' }}" onclick="selectRole({{ $role->id }}, this)" data-role="{{ $role->slug }}">
                    <div class="rp-role-header">
                        <div class="rp-role-icon {{ $color['accent'] }}"><i class="bi {{ $color['icon'] }}"></i></div>
                        <div class="rp-role-title">
                            <h5>{{ $role->name }}</h5>
                            <span class="rp-role-type {{ $isSystem ? 'system' : 'custom' }}">{{ $isSystem ? 'Sistem Rolü' : 'Özel Rol' }}</span>
                        </div>
                        <div class="rp-role-menu">
                            @if(auth()->user()->hasPermission('roles.edit'))
                                <button class="usr-action-btn" onclick="event.stopPropagation(); openEditModal({{ $role->id }}, '{{ e($role->name) }}')" title="Düzenle"><i class="bi bi-pencil"></i></button>
                            @endif
                        </div>
                    </div>
                    <p class="rp-role-desc">
                        @if($role->slug === 'super_admin')
                            Tam sistem erişimi. Tüm modülleri yönetebilir, rol oluşturabilir ve silebilir.
                        @elseif($role->slug === 'admin')
                            Geniş yönetim yetkisi. Kullanıcı ve içerik yönetimi yapabilir.
                        @elseif($role->slug === 'yazar')
                            İçerik oluşturma ve düzenleme yetkisi. Eser gönderebilir.
                        @elseif($role->slug === 'kullanici')
                            Temel erişim hakları. Kendi profilini düzenleyebilir ve içerik görüntüleyebilir.
                        @else
                            {{ $role->name }} rolü
                        @endif
                    </p>
                    <div class="rp-role-meta">
                        <div class="rp-role-meta-item"><i class="bi bi-people"></i><span>{{ number_format($roleUsers) }} kullanıcı</span></div>
                        <div class="rp-role-meta-item"><i class="bi bi-key"></i><span>{{ $permCount }}/{{ $totalPermissions }} izin</span></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <!-- ==================== SECTION 3: PERMISSION MATRIX ==================== -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-header-custom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <h6 class="mb-0"><i class="bi bi-grid-3x3 me-2 text-teal"></i>İzin Matrisi</h6>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm rp-matrix-filter" onchange="filterMatrix(this.value)">
                    <option value="all">Tüm Kategoriler</option>
                    @foreach($permissionGroups->keys() as $group)
                        <option value="{{ Str::slug($group) }}">{{ $group }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="rp-matrix-table" id="permissionMatrix">
                    <thead>
                        <tr>
                            <th class="rp-matrix-perm-col">İzin</th>
                            @foreach($roles as $role)
                                @php $color = $roleColors[$role->slug] ?? $defaultColor; @endphp
                                <th class="rp-matrix-role-col">
                                    <div class="rp-matrix-role-head {{ $color['accent'] }}">
                                        <i class="bi {{ $color['icon'] }}"></i>
                                        <span>{{ $role->name }}</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissionGroups as $group => $permissions)
                            <tr class="rp-matrix-group" data-cat="{{ Str::slug($group) }}">
                                <td colspan="{{ $roles->count() + 1 }}"><i class="bi bi-folder me-2"></i>{{ $group }}</td>
                            </tr>
                            @foreach($permissions as $permission)
                                <tr data-cat="{{ Str::slug($group) }}">
                                    <td class="rp-perm-name">
                                        <span>{{ $permission->name }}</span>
                                        <small>{{ $permission->description ?? $permission->slug }}</small>
                                    </td>
                                    @foreach($roles as $role)
                                        @php
                                            $hasPermission = $role->permissions->contains('id', $permission->id);
                                            $isSuperAdmin = $role->slug === 'super_admin';
                                        @endphp
                                        <td>
                                            <label class="rp-check{{ $hasPermission || $isSuperAdmin ? ' granted' : '' }}">
                                                <input type="checkbox"
                                                       data-role-id="{{ $role->id }}"
                                                       data-permission-id="{{ $permission->id }}"
                                                       {{ $hasPermission || $isSuperAdmin ? 'checked' : '' }}
                                                       {{ $isSuperAdmin ? 'disabled' : '' }}>
                                                <span></span>
                                            </label>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-body-custom pt-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3 text-xs-muted">
                    <span class="d-flex align-items-center gap-1"><span class="rp-legend-dot granted"></span> Verildi</span>
                    <span class="d-flex align-items-center gap-1"><span class="rp-legend-dot denied"></span> Reddedildi</span>
                    <span class="d-flex align-items-center gap-1"><span class="rp-legend-dot locked"></span> Kilitli (Sistem)</span>
                </div>
                @if(auth()->user()->hasPermission('roles.edit'))
                    <button class="btn-teal" onclick="savePermissions()"><i class="bi bi-check2 me-1"></i> Değişiklikleri Kaydet</button>
                @endif
            </div>
        </div>
    </div>


    <!-- ==================== SECTION 4: ROLE COMPARISON ==================== -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
            <div class="card-dark">
                <div class="card-header-custom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0"><i class="bi bi-bar-chart-fill me-2 text-neon-purple"></i>Rol Dağılımı</h6>
                </div>
                <div class="card-body-custom">
                    <div class="rp-bar-chart">
                        @php
                            $barColors = [
                                'super_admin' => 'var(--teal-primary)',
                                'admin'       => 'var(--neon-blue)',
                                'yazar'       => 'var(--neon-purple)',
                                'kullanici'   => 'var(--neon-green)',
                            ];
                            $defaultBarColor = 'var(--neon-orange)';
                        @endphp
                        @foreach($distribution as $dist)
                            <div class="rp-bar-item">
                                <div class="rp-bar-label"><span class="rp-bar-dot" style="--c:{{ $barColors[$dist['slug']] ?? $defaultBarColor }}"></span>{{ $dist['name'] }}</div>
                                <div class="rp-bar-track">
                                    <div class="rp-bar-fill" style="width:{{ max($dist['percentage'], 0.5) }}%;{{ $dist['percentage'] < 2 ? 'min-width:32px;' : '' }}--c:{{ $barColors[$dist['slug']] ?? $defaultBarColor }}">
                                        <span>{{ number_format($dist['count']) }}</span>
                                    </div>
                                </div>
                                <span class="rp-bar-pct">%{{ $dist['percentage'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Info -->
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="150">
            <div class="card-dark">
                <div class="card-header-custom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2 text-teal"></i>Roller Hakkında</h6>
                </div>
                <div class="card-body-custom p-0">
                    <div class="rp-activity-list">
                        @foreach($roles as $role)
                            @php $color = $roleColors[$role->slug] ?? $defaultColor; @endphp
                            <div class="rp-activity-item">
                                <div class="rp-activity-icon" style="--c:{{ $barColors[$role->slug] ?? $defaultBarColor }}"><i class="bi {{ $color['icon'] }}"></i></div>
                                <div class="rp-activity-info">
                                    <span><strong>{{ $role->name }}</strong> — {{ $role->permissions->count() }} izin</span>
                                    <small>{{ number_format($role->users_count) }} kullanıcı atanmış</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- ==================== SECTION 5: QUICK ROLE ASSIGN ==================== -->
    @if(auth()->user()->hasPermission('roles.assign'))
        <div class="row g-4 mb-4">
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card-dark">
                    <div class="card-header-custom">
                        <h6 class="mb-0"><i class="bi bi-person-gear me-2 text-neon-blue"></i>Hızlı Rol Atama</h6>
                    </div>
                    <div class="card-body-custom">
                        <p class="text-muted mb-3 fs-13">Kullanıcı seçerek hızlıca rol atayın veya mevcut rolünü değiştirin.</p>
                        <form action="{{ route('admin.roles.assign') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="stg-label">Kullanıcı</label>
                                    <select class="stg-select" name="user_id" required>
                                        <option value="">Kullanıcı seçin...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role?->name ?? '—' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="stg-label">Yeni Rol</label>
                                    <select class="stg-select" name="role_id" required>
                                        <option value="">Rol seçin...</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-teal"><i class="bi bi-check2 me-1"></i> Rolü Ata</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="150">
                <div class="card-dark">
                    <div class="card-header-custom">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2 text-teal"></i>Rol Politikaları</h6>
                    </div>
                    <div class="card-body-custom">
                        <div class="stg-toggle-list">
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span>Otomatik Rol Atama</span>
                                    <small>Yeni kayıt olan kullanıcılara otomatik "Kullanıcı" rolü ata</small>
                                </div>
                                <label class="stg-switch"><input type="checkbox" checked disabled><span class="stg-switch-slider"></span></label>
                            </div>
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span>Super Admin Koruması</span>
                                    <small>Super Admin rolü silinemez ve izinleri değiştirilemez</small>
                                </div>
                                <label class="stg-switch"><input type="checkbox" checked disabled><span class="stg-switch-slider"></span></label>
                            </div>
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span>Rol Değişikliği Bildirimi</span>
                                    <small>Rol değiştiğinde kullanıcıya e-posta bildirimi gönder</small>
                                </div>
                                <label class="stg-switch"><input type="checkbox" disabled><span class="stg-switch-slider"></span></label>
                            </div>
                            <div class="stg-toggle-item">
                                <div class="stg-toggle-info">
                                    <span>Rol Geçmişi Kaydet</span>
                                    <small>Tüm rol değişikliklerini logla</small>
                                </div>
                                <label class="stg-switch"><input type="checkbox" checked disabled><span class="stg-switch-slider"></span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- ==================== NEW ROLE MODAL ==================== -->
    @if(auth()->user()->hasPermission('roles.create'))
        <div class="modal fade" id="roleModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-card-bordered">
                    <div class="modal-header border-theme">
                        <h5 class="modal-title"><i class="bi bi-shield-plus me-2 text-teal"></i>Yeni Rol Oluştur</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="stg-label">Rol Adı <span class="text-neon-red">*</span></label>
                                    <input type="text" class="stg-input" name="name" placeholder="Örn: İçerik Yöneticisi" required>
                                </div>
                                <div class="col-12">
                                    <label class="stg-label">Kopyalanacak Rol</label>
                                    <select class="stg-select" id="copyFromRole">
                                        <option value="">Sıfırdan oluştur</option>
                                        @foreach($roles as $role)
                                            @if($role->slug !== 'super_admin')
                                                <option value="{{ $role->id }}">{{ $role->name }} izinlerini kopyala</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="stg-label">İzinler</label>
                                    <div class="rp-modal-permissions">
                                        @foreach($permissionGroups as $group => $permissions)
                                            <div class="rp-modal-perm-group">
                                                <label class="rp-modal-perm-group-title">
                                                    <input type="checkbox" class="rp-group-toggle" data-group="{{ Str::slug($group) }}">
                                                    <span>{{ $group }}</span>
                                                </label>
                                                @foreach($permissions as $permission)
                                                    <label class="rp-modal-perm-item">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="rp-perm-checkbox" data-group="{{ Str::slug($group) }}">
                                                        <span>{{ $permission->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-theme">
                            <button type="button" class="stg-btn" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn-teal"><i class="bi bi-plus-lg me-1"></i> Rol Oluştur</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


    <!-- ==================== EDIT ROLE MODAL ==================== -->
    @if(auth()->user()->hasPermission('roles.edit'))
        <div class="modal fade" id="editRoleModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-card-bordered">
                    <div class="modal-header border-theme">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2 text-teal"></i>Rolü Düzenle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editRoleForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="stg-label">Rol Adı <span class="text-neon-red">*</span></label>
                                    <input type="text" class="stg-input" name="name" id="editRoleName" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-theme">
                            <button type="button" class="stg-btn" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn-teal"><i class="bi bi-check2 me-1"></i> Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif


    <!-- ==================== DELETE ROLE MODAL ==================== -->
    @if(auth()->user()->hasPermission('roles.delete'))
        <div class="modal fade" id="deleteRoleModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content bg-card-bordered">
                    <div class="modal-header border-theme">
                        <h5 class="modal-title"><i class="bi bi-trash me-2 text-danger"></i>Rolü Sil</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Bu rolü silmek istediğinize emin misiniz?</p>
                        <p class="text-danger fs-13 mt-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Role atanmış kullanıcılar varsa silinemez.</p>
                    </div>
                    <div class="modal-footer border-theme">
                        <button type="button" class="stg-btn" data-bs-dismiss="modal">İptal</button>
                        <form id="deleteRoleForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="stg-btn stg-btn-danger"><i class="bi bi-trash me-1"></i> Sil</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Toast -->
    <div class="position-fixed top-0 end-0 p-3 z-toast">
        <div class="toast align-items-center border-0 bg-card-bordered" id="rpToast" role="alert">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2" id="rpToastBody">
                    <i class="bi bi-check-circle-fill text-neon-green"></i>
                    <span>İşlem başarıyla tamamlandı</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const ROLES_DATA = @json($roles->mapWithKeys(fn ($role) => [$role->id => $role->permissions->pluck('id')]));
    const ROLE_UPDATE_BASE_URL = '{{ url("admin/roles") }}';
</script>
<script src="{{ asset('assets/admin/js/roles-permissions.js') }}?v={{ time() }}"></script>
@endpush
