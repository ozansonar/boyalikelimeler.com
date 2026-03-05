@extends('layouts.admin')

@section('title', 'Dashboard — Admin')

@section('content')
<!-- Page Header -->
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h2>Dashboard</h2>
        <p>Hos geldiniz, {{ auth()->user()->name }}.</p>
    </div>
</div>

<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card teal animate-in">
            <div class="stat-card-header">
                <div class="stat-icon teal"><i class="bi bi-people-fill"></i></div>
                <span class="stat-trend {{ $stats['user_growth'] >= 0 ? 'up' : 'down' }}">
                    <i class="bi bi-arrow-{{ $stats['user_growth'] >= 0 ? 'up' : 'down' }}-short"></i>{{ abs($stats['user_growth']) }}%
                </span>
            </div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">Toplam Kullanici</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card purple animate-in anim-delay-1">
            <div class="stat-card-header">
                <div class="stat-icon purple"><i class="bi bi-journal-richtext"></i></div>
                <span class="stat-trend {{ $stats['work_growth'] >= 0 ? 'up' : 'down' }}">
                    <i class="bi bi-arrow-{{ $stats['work_growth'] >= 0 ? 'up' : 'down' }}-short"></i>{{ abs($stats['work_growth']) }}%
                </span>
            </div>
            <div class="stat-value">{{ number_format($stats['total_works']) }}</div>
            <div class="stat-label">Toplam Eser</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card blue animate-in anim-delay-2">
            <div class="stat-card-header">
                <div class="stat-icon blue"><i class="bi bi-chat-dots-fill"></i></div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_comments']) }}</div>
            <div class="stat-label">Toplam Yorum</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card orange animate-in anim-delay-3">
            <div class="stat-card-header">
                <div class="stat-icon orange"><i class="bi bi-file-earmark-text-fill"></i></div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_posts']) }}</div>
            <div class="stat-label">Toplam Yazi</div>
        </div>
    </div>
</div>

<!-- Quick Alert Cards -->
<div class="row g-3 mb-4">
    @if($stats['pending_works'] > 0)
    <div class="col-xl-4 col-md-6">
        <div class="card-dark">
            <div class="card-body-custom d-flex align-items-center gap-3 py-3">
                <div class="bg-icon-orange-strong rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;min-width:42px">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="fw-600-primary">{{ $stats['pending_works'] }} Bekleyen Eser</div>
                    <div class="text-sm-muted">Onay bekliyor</div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($stats['pending_comments'] > 0)
    <div class="col-xl-4 col-md-6">
        <div class="card-dark">
            <div class="card-body-custom d-flex align-items-center gap-3 py-3">
                <div class="bg-icon-purple-strong rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;min-width:42px">
                    <i class="bi bi-chat-left-dots"></i>
                </div>
                <div>
                    <div class="fw-600-primary">{{ $stats['pending_comments'] }} Onaysiz Yorum</div>
                    <div class="text-sm-muted">Moderasyon bekliyor</div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($stats['unread_messages'] > 0)
    <div class="col-xl-4 col-md-6">
        <div class="card-dark">
            <div class="card-body-custom d-flex align-items-center gap-3 py-3">
                <div class="bg-icon-blue-strong rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;min-width:42px">
                    <i class="bi bi-envelope"></i>
                </div>
                <div>
                    <div class="fw-600-primary">{{ $stats['unread_messages'] }} Okunmamis Mesaj</div>
                    <div class="text-sm-muted">Iletisim formu</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card-dark">
            <div class="card-header-custom">
                <h6><i class="bi bi-activity me-2 text-teal"></i>Aylik Kayit ve Eser Istatistikleri</h6>
            </div>
            <div class="card-body-custom">
                <div class="chart-container"><canvas id="trendChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card-dark">
            <div class="card-header-custom">
                <h6><i class="bi bi-pie-chart me-2 text-neon-purple"></i>Rol Dagilimi</h6>
            </div>
            <div class="card-body-custom">
                <div class="chart-container"><canvas id="roleChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- Second Charts Row + Top Authors -->
<div class="row g-3 mb-4">
    <div class="col-xl-4">
        <div class="card-dark">
            <div class="card-header-custom">
                <h6><i class="bi bi-bar-chart me-2 text-neon-blue"></i>Eser Durumlari</h6>
            </div>
            <div class="card-body-custom">
                <div class="chart-container chart-container-sm"><canvas id="workStatusChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card-dark">
            <div class="card-header-custom">
                <h6><i class="bi bi-trophy me-2 text-neon-orange"></i>En Aktif Yazarlar</h6>
            </div>
            <div class="card-body-custom pt-2">
                @forelse($topAuthors as $index => $author)
                <div class="activity-item">
                    <div class="activity-icon bg-icon-{{ ['teal', 'purple', 'blue', 'orange', 'green'][$index % 5] }}-strong">
                        {{ $index + 1 }}
                    </div>
                    <div class="activity-content">
                        <h6>{{ $author->name }}</h6>
                        <p>{{ $author->literary_works_count }} yayinlanmis eser</p>
                    </div>
                </div>
                @empty
                <div class="text-sm-muted text-center py-3">Henuz veri yok</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card-dark">
            <div class="card-header-custom">
                <h6><i class="bi bi-clock-history me-2 text-neon-green"></i>Son Yorumlar</h6>
            </div>
            <div class="card-body-custom pt-2">
                @forelse($latestComments as $comment)
                <div class="activity-item">
                    <div class="activity-icon bg-icon-{{ $comment->is_approved ? 'green' : 'orange' }}-strong">
                        <i class="bi bi-chat-{{ $comment->is_approved ? 'check' : 'dots' }}"></i>
                    </div>
                    <div class="activity-content">
                        <h6>{{ $comment->first_name }} {{ $comment->last_name }}</h6>
                        <p>{{ Str::limit($comment->body ?? $comment->content ?? '', 50) }} &middot; {{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-sm-muted text-center py-3">Henuz yorum yok</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Latest Works Table -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card-dark">
            <div class="card-header-custom">
                <h6><i class="bi bi-journal-bookmark me-2 text-teal"></i>Son Eklenen Eserler</h6>
            </div>
            <div class="card-body-custom card-body-flush">
                <table class="table-dark-custom">
                    <thead>
                        <tr>
                            <th>Eser</th>
                            <th>Yazar</th>
                            <th>Kategori</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestWorks as $work)
                        <tr>
                            <td class="fw-600-primary">{{ Str::limit($work->title, 40) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-10">
                                    <div class="user-avatar-sm avatar-gradient-teal">{{ mb_strtoupper(mb_substr($work->author->name ?? '?', 0, 1)) }}</div>
                                    <div class="fw-600-primary">{{ $work->author->name ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="text-sm-muted">{{ $work->category->name ?? '-' }}</td>
                            <td>
                                <span class="status-badge {{ $work->status->badgeClass() }}">{{ $work->status->label() }}</span>
                            </td>
                            <td class="text-sm-muted">{{ $work->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-sm-muted py-3">Henuz eser yok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Latest Users Table -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card-dark">
            <div class="card-header-custom">
                <h6><i class="bi bi-person-plus me-2 text-neon-purple"></i>Son Kayit Olan Kullanicilar</h6>
            </div>
            <div class="card-body-custom card-body-flush">
                <table class="table-dark-custom">
                    <thead>
                        <tr>
                            <th>Kullanici</th>
                            <th>E-posta</th>
                            <th>Rol</th>
                            <th>Kayit Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestUsers as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-10">
                                    <div class="user-avatar-sm avatar-gradient-purple">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                                    <div>
                                        <div class="fw-600-primary">{{ $user->name }}</div>
                                        <div class="text-sm-muted">{{ '@' . $user->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-sm-muted">{{ $user->email }}</td>
                            <td><span class="status-badge active">{{ $user->role->name ?? '-' }}</span></td>
                            <td class="text-sm-muted">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-sm-muted py-3">Henuz kullanici yok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    window.DASHBOARD_DATA = {
        monthlyUsers: @json($monthlyUsers),
        monthlyWorks: @json($monthlyWorks),
        roleDistribution: @json($roleDistribution),
        workStatus: @json($workStatus),
    };
</script>
<script src="{{ asset('assets/admin/js/dashboard.js') }}"></script>
@endpush
