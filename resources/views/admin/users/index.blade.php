@extends('layouts.admin')

@section('title', 'Kullanıcı Yönetimi — Admin')

@section('content')

    <x-admin.page-header title="Kullanıcı Yönetimi" subtitle="Sistemdeki tüm kullanıcıları yönetin, roller atayın ve erişimleri kontrol edin">
        <a href="{{ route('admin.users.create') }}" class="btn-teal">
            <i class="bi bi-person-plus me-1"></i> Yeni Kullanıcı
        </a>
    </x-admin.page-header>


    <!-- ==================== SECTION 1: STATS ==================== -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-people-fill" label="Toplam Kullanıcı" :count="$stats['total']" :delay="0" />
        <x-admin.stat-card color="green" icon="bi-person-check-fill" label="E-posta Doğrulanmış" :count="$stats['verified']" :delay="100" />
        <x-admin.stat-card color="orange" icon="bi-person-plus-fill" label="Bu Ay Yeni" :count="$stats['this_month']" :delay="200" />
        <x-admin.stat-card color="pink" icon="bi-person-dash-fill" label="Doğrulanmamış" :count="$stats['unverified']" :delay="300" />
    </div>


    <!-- ==================== SECTION 2: FILTERS & TOOLBAR ==================== -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-body-custom">
            <form method="GET" action="{{ route('admin.users.index') }}" class="usr-toolbar">
                <div class="usr-search">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ad, e-posta ile ara...">
                </div>

                <div class="usr-filters">
                    <select class="usr-filter-select" name="role" onchange="this.form.submit()">
                        <option value="">Tüm Roller</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->slug }}" {{ ($filters['role'] ?? '') === $role->slug ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>

                    <select class="usr-filter-select" name="status" onchange="this.form.submit()">
                        <option value="">Tüm Durumlar</option>
                        <option value="verified" {{ ($filters['status'] ?? '') === 'verified' ? 'selected' : '' }}>Doğrulanmış</option>
                        <option value="unverified" {{ ($filters['status'] ?? '') === 'unverified' ? 'selected' : '' }}>Doğrulanmamış</option>
                    </select>

                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                </div>

                <div class="usr-toolbar-actions">
                    <button type="submit" class="btn-glass"><i class="bi bi-funnel me-1"></i>Filtrele</button>
                    @if(!empty($filters['search']) || !empty($filters['role']) || !empty($filters['status']))
                        <a href="{{ route('admin.users.index') }}" class="btn-glass"><i class="bi bi-x-lg me-1"></i>Temizle</a>
                    @endif
                </div>
            </form>
        </div>
    </div>


    <!-- ==================== SECTION 3: TABLE VIEW ==================== -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="usr-table">
                    <thead>
                        <tr>
                            <th class="usr-th-sortable">
                                <a href="{{ route('admin.users.index', array_merge($filters, ['sort' => 'name', 'dir' => ($filters['sort'] ?? '') === 'name' && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Kullanıcı <i class="bi bi-arrow-down-up"></i>
                                </a>
                            </th>
                            <th>Rol</th>
                            <th>E-posta Durumu</th>
                            <th class="d-none d-xl-table-cell">
                                <a href="{{ route('admin.users.index', array_merge($filters, ['sort' => 'created_at', 'dir' => ($filters['sort'] ?? '') === 'created_at' && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc', 'per_page' => $perPage])) }}" class="text-decoration-none text-clr-secondary">
                                    Kayıt Tarihi <i class="bi bi-arrow-down-up"></i>
                                </a>
                            </th>
                            <th class="usr-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="usr-user-cell">
                                        <div class="usr-avatar">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=14b8a6&color=fff&size=40" alt="{{ $user->name }}">
                                            @if($user->hasVerifiedEmail())
                                                <span class="usr-status-dot online"></span>
                                            @else
                                                <span class="usr-status-dot pending"></span>
                                            @endif
                                        </div>
                                        <div class="usr-user-info">
                                            <span class="usr-user-name">
                                                {{ $user->name }}
                                                @if($user->isSuperAdmin())
                                                    <i class="bi bi-patch-check-fill text-teal-xs"></i>
                                                @endif
                                                @if($user->hasActiveGoldenPen())
                                                    @php $activePeriod = $user->activeGoldenPenPeriod; @endphp
                                                    <span class="usr-golden-pen-badge" title="Altın Kalem — {{ $activePeriod?->starts_at?->format('d.m.Y') }} / {{ $activePeriod?->ends_at?->format('d.m.Y') }}">
                                                        <i class="bi bi-pen-fill"></i>
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="usr-user-email">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $roleBadgeMap = [
                                            'super_admin' => 'admin',
                                            'admin'       => 'admin',
                                            'yazar'       => 'editor',
                                            'kullanici'   => 'user',
                                        ];
                                        $roleIconMap = [
                                            'super_admin' => 'bi-shield-fill',
                                            'admin'       => 'bi-shield-fill',
                                            'yazar'       => 'bi-pencil-fill',
                                            'kullanici'   => 'bi-person-fill',
                                        ];
                                        $slug = $user->role?->slug ?? 'kullanici';
                                    @endphp
                                    <span class="usr-role-badge {{ $roleBadgeMap[$slug] ?? 'user' }}">
                                        <i class="bi {{ $roleIconMap[$slug] ?? 'bi-person-fill' }}"></i>
                                        {{ $user->role?->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->hasVerifiedEmail())
                                        <span class="usr-status-badge active">Doğrulanmış</span>
                                    @else
                                        <span class="usr-status-badge pending">Beklemede</span>
                                    @endif
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <span class="usr-meta">{{ $user->created_at->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Detay" href="{{ route('admin.users.show', $user) }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.users.edit', $user) }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <button class="usr-action-btn danger" title="Sil" onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="5" icon="bi-people" message="Kullanıcı bulunamadı." />
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="usr-pagination">
                    <div class="usr-pagination-info">
                        <span>Toplam <strong>{{ number_format($users->total()) }}</strong> kullanıcıdan <strong>{{ $users->firstItem() }}-{{ $users->lastItem() }}</strong> gösteriliyor</span>
                    </div>
                    <div class="usr-pagination-controls">
                        <select class="usr-filter-select w-auto" onchange="changePerPage(this.value)">
                            @foreach([10, 25, 50, 100] as $pp)
                                <option value="{{ $pp }}" {{ $perPage === $pp ? 'selected' : '' }}>{{ $pp }} / sayfa</option>
                            @endforeach
                        </select>
                        <div class="usr-page-btns">
                            @if($users->onFirstPage())
                                <button class="usr-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" class="usr-page-btn"><i class="bi bi-chevron-left"></i></a>
                            @endif

                            @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}" class="usr-page-btn {{ $page === $users->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                            @endforeach

                            @if($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" class="usr-page-btn"><i class="bi bi-chevron-right"></i></a>
                            @else
                                <button class="usr-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-custom">
                <div class="modal-body text-center py-4">
                    <div class="delete-modal-icon mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-neon-red fs-1"></i>
                    </div>
                    <h5 class="mb-2">Kullanıcıyı Sil</h5>
                    <p class="text-clr-muted mb-3"><strong id="deleteUserName"></strong> adlı kullanıcıyı silmek istediğinize emin misiniz?</p>
                    <p class="text-clr-muted small">Bu işlem geri alınabilir (soft delete).</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button class="btn-glass" data-bs-dismiss="modal">İptal</button>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-teal btn-danger-gradient">
                                <i class="bi bi-trash me-1"></i>Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/users.js') }}"></script>
@endpush
