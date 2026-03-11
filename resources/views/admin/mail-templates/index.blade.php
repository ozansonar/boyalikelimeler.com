@extends('layouts.admin')

@section('title', 'Mail Şablonları — Boyalı Kelimeler Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item active text-teal">Mail Şablonları</li>
        </ol>
    </nav>

    <x-admin.page-header title="Mail Şablonları" subtitle="E-posta konu ve gövde içeriklerini yönetin, değişkenleri istediğiniz yere yerleştirin">
        <a href="javascript:void(0)" class="btn-glass" onclick="openConfirmModal({
            title: 'Tüm Şablonları Sıfırla',
            message: 'Tüm şablonlar varsayılan değerlere sıfırlanacak. Devam etmek istiyor musunuz?',
            iconClass: 'bi-arrow-counterclockwise',
            type: 'warning',
            btnHtml: '<i class=\'bi bi-arrow-counterclockwise\'></i> Evet, Tümünü Sıfırla',
            onConfirm: function() { window.location.href = '{{ route('admin.mail-templates.reset-all') }}'; }
        })">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Tümünü Sıfırla
        </a>
    </x-admin.page-header>

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-envelope-paper-fill" label="Toplam Şablon" :count="$stats['total']" :delay="0" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Aktif" :count="$stats['active']" :delay="100" />
        <x-admin.stat-card color="orange" icon="bi-pencil-fill" label="Özelleştirilmiş" :count="$stats['customized']" :delay="200" />
    </div>

    <!-- Templates Table -->
    <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card-body-custom p-0">
            <div class="table-responsive">
                <table class="table table-hover cl-table mb-0">
                    <thead>
                        <tr>
                            <th>Şablon</th>
                            <th>Konu</th>
                            <th>Durum</th>
                            <th>Özelleştirilmiş</th>
                            <th class="cl-th-actions">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $template->description }}</div>
                                    <small class="text-muted">{{ $template->key }}</small>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" title="{{ $template->subject }}">
                                        {{ \Illuminate\Support\Str::limit($template->subject, 50) }}
                                    </span>
                                </td>
                                <td>
                                    @if($template->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    @if($template->hasCustomSubject() || $template->hasCustomBody())
                                        <span class="badge bg-warning text-dark">Düzenlenmiş</span>
                                    @else
                                        <span class="badge bg-dark">Varsayılan</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="usr-actions">
                                        <a class="usr-action-btn" title="Düzenle" href="{{ route('admin.mail-templates.edit', $template) }}"><i class="bi bi-pencil"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-admin.table-empty :colspan="5" icon="bi-envelope-x" message="Henüz mail şablonu bulunamadı." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
