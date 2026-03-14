@extends('layouts.admin')

@section('title', 'Yazar Başvuruları')

@section('content')

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Yazar Başvuruları</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4">
        <div>
            <h1 class="page-title">Yazar Başvuruları</h1>
            <p class="page-subtitle">Yazar başvurularını inceleyin, onaylayın veya reddedin</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <x-admin.stat-card color="blue" icon="bi-people" label="Toplam" :count="$statusCounts['total']" delay="0" />
        <x-admin.stat-card color="orange" icon="bi-hourglass-split" label="Bekleyen" :count="$statusCounts['pending']" delay="100" />
        <x-admin.stat-card color="green" icon="bi-check-circle" label="Onaylanan" :count="$statusCounts['approved']" delay="200" />
        <x-admin.stat-card color="red" icon="bi-x-circle" label="Reddedilen" :count="$statusCounts['rejected']" delay="300" />
    </div>

    {{-- Status Filter Tabs --}}
    <div class="cl-status-tabs mb-3">
        <a href="{{ route('admin.writer-applications.index') }}"
           class="cl-status-tab {{ !$statusFilter ? 'active' : '' }}">
            Tümü <span class="cl-tab-count">{{ $statusCounts['total'] }}</span>
        </a>
        <a href="{{ route('admin.writer-applications.index', ['status' => 'pending']) }}"
           class="cl-status-tab {{ $statusFilter === 'pending' ? 'active' : '' }}">
            Bekleyen <span class="cl-tab-count">{{ $statusCounts['pending'] }}</span>
        </a>
        <a href="{{ route('admin.writer-applications.index', ['status' => 'approved']) }}"
           class="cl-status-tab {{ $statusFilter === 'approved' ? 'active' : '' }}">
            Onaylanan <span class="cl-tab-count">{{ $statusCounts['approved'] }}</span>
        </a>
        <a href="{{ route('admin.writer-applications.index', ['status' => 'rejected']) }}"
           class="cl-status-tab {{ $statusFilter === 'rejected' ? 'active' : '' }}">
            Reddedilen <span class="cl-tab-count">{{ $statusCounts['rejected'] }}</span>
        </a>
    </div>

    {{-- Table --}}
    <div class="card-dark">
        <div class="card-body-custom p-0">
            @if($applications->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-clr-secondary" style="font-size: 2.5rem;"></i>
                    <p class="mt-2 text-clr-secondary">Henüz başvuru bulunmuyor.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover cl-table mb-0">
                        <thead>
                            <tr>
                                <th>Başvuran</th>
                                <th class="d-none d-md-table-cell">Motivasyon</th>
                                <th>Durum</th>
                                <th class="d-none d-lg-table-cell">Tarih</th>
                                <th class="cl-th-actions">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    {{-- Applicant --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="cmt-admin-avatar">{{ mb_strtoupper(mb_substr($application->user->name ?? '?', 0, 2)) }}</span>
                                            <div>
                                                <strong class="d-block text-white">{{ $application->user->name ?? '-' }}</strong>
                                                <small class="text-clr-secondary">{{ $application->user->email ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Motivation snippet --}}
                                    <td class="d-none d-md-table-cell">
                                        <span class="text-clr-secondary" title="{{ $application->motivation }}">
                                            {{ Str::limit($application->motivation, 80) }}
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @php
                                            $badgeMap = [
                                                'pending' => 'usr-status-badge-orange',
                                                'active' => 'usr-status-badge-green',
                                                'inactive' => 'usr-status-badge-red',
                                            ];
                                            $badgeClass = $badgeMap[$application->status->badgeClass()] ?? 'usr-status-badge-orange';
                                        @endphp
                                        <span class="usr-status-badge {{ $badgeClass }}">
                                            {{ $application->status->label() }}
                                        </span>
                                    </td>

                                    {{-- Date --}}
                                    <td class="d-none d-lg-table-cell">
                                        <small class="text-clr-secondary">{{ $application->created_at->format('d.m.Y H:i') }}</small>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="usr-actions">
                                            <a href="{{ route('admin.writer-applications.show', $application) }}"
                                               class="usr-action-btn" title="Detay">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($applications->hasPages())
                    <div class="cl-pagination-wrapper">
                        <div class="cl-pagination-info">
                            Toplam <strong>{{ $applications->total() }}</strong> başvurudan
                            <strong>{{ $applications->firstItem() }}-{{ $applications->lastItem() }}</strong> arası
                        </div>
                        <nav class="cl-pagination">
                            {{-- Previous --}}
                            @if($applications->onFirstPage())
                                <button class="cl-page-btn" disabled><i class="bi bi-chevron-left"></i></button>
                            @else
                                <a href="{{ $applications->previousPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-left"></i></a>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach($applications->getUrlRange(1, $applications->lastPage()) as $page => $url)
                                <a href="{{ $url }}" class="cl-page-btn {{ $page == $applications->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                            @endforeach

                            {{-- Next --}}
                            @if($applications->hasMorePages())
                                <a href="{{ $applications->nextPageUrl() }}" class="cl-page-btn"><i class="bi bi-chevron-right"></i></a>
                            @else
                                <button class="cl-page-btn" disabled><i class="bi bi-chevron-right"></i></button>
                            @endif
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>

@endsection
