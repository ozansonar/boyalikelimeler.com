@extends('layouts.admin')

@section('title', 'Anket Sonuçları — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.polls.index') }}" class="breadcrumb-link">Anket Yönetimi</a></li>
            <li class="breadcrumb-item active text-teal">Sonuçlar</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.polls.index') }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title">Anket Sonuçları</h1>
                <p class="page-subtitle">{{ $poll->question }}</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($poll->is_active)
                <span class="usr-status-badge active">Aktif</span>
            @else
                <span class="usr-status-badge inactive">Pasif</span>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-dark" data-aos="fade-up">
                <div class="card-body-custom text-center py-4">
                    <div class="fs-2 fw-bold text-teal">{{ number_format($totalVotes) }}</div>
                    <small class="text-muted">Toplam Oy</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-dark" data-aos="fade-up" data-aos-delay="50">
                <div class="card-body-custom text-center py-4">
                    <div class="fs-2 fw-bold text-teal">{{ count($results) }}</div>
                    <small class="text-muted">Şık Sayısı</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-dark" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body-custom text-center py-4">
                    <div class="fs-2 fw-bold text-teal">
                        @if($poll->ends_at)
                            {{ $poll->ends_at->format('d.m.Y') }}
                        @else
                            Süresiz
                        @endif
                    </div>
                    <small class="text-muted">Bitiş Tarihi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="card-header-custom">
            <div class="form-section-header mb-0">
                <div class="form-section-icon bg-icon-teal"><i class="bi bi-bar-chart"></i></div>
                <div>
                    <h6 class="mb-0">Oy Dağılımı</h6>
                    <small class="text-muted">Her şıkkın aldığı oy ve yüzdesi</small>
                </div>
            </div>
        </div>
        <div class="card-body-custom">
            @forelse($results as $result)
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">{{ $result['option_text'] }}</span>
                        <span class="text-muted">{{ $result['vote_count'] }} oy ({{ $result['percentage'] }}%)</span>
                    </div>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $result['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-teal" data-width="{{ $result['percentage'] }}"></div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center py-3">Henüz oy verilmemiş.</p>
            @endforelse
        </div>
    </div>

    <!-- Actions -->
    <div class="card-dark mb-4">
        <div class="card-body-custom">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.polls.index') }}" class="btn-glass">
                    <i class="bi bi-arrow-left me-1"></i>Listeye Dön
                </a>
                <a href="{{ route('admin.polls.edit', $poll) }}" class="btn-teal">
                    <i class="bi bi-pencil me-1"></i>Düzenle
                </a>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.progress-bar[data-width]').forEach(function(bar) {
        var width = bar.getAttribute('data-width');
        setTimeout(function() {
            bar.style.width = width + '%';
        }, 300);
    });
});
</script>
@endpush
