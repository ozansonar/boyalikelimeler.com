@extends('layouts.admin')

@section('title', 'Mail Şablonları — Boyalı Kelimeler Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house"></i> Ana Sayfa</a></li>
            <li class="breadcrumb-item active text-teal">Mail Şablonları</li>
        </ol>
    </nav>

    <x-admin.page-header title="Mail Şablonları" subtitle="E-posta konu ve gövde içeriklerini yönetin, değişkenleri istediğiniz yere yerleştirin" />

    <!-- Stat Cards -->
    <div class="row g-4 mb-4">
        <x-admin.stat-card color="blue" icon="bi-envelope-paper-fill" label="Toplam Şablon" :count="$stats['total']" :delay="0" />
        <x-admin.stat-card color="green" icon="bi-check-circle-fill" label="Aktif" :count="$stats['active']" :delay="100" />
        <x-admin.stat-card color="orange" icon="bi-pencil-fill" label="Özelleştirilmiş" :count="$stats['customized']" :delay="200" />
    </div>

    <!-- Toolbar -->
    <div class="cl-toolbar mb-4" data-aos="fade-up" data-aos-delay="50">
        <div class="d-flex justify-content-end w-100">
            <a href="{{ route('admin.mail-templates.reset-all') }}" class="btn btn-outline-warning btn-sm"
               onclick="return confirm('Tüm şablonlar varsayılan değerlere sıfırlanacak. Emin misiniz?')">
                <i class="bi bi-arrow-counterclockwise"></i> Tümünü Sıfırla
            </a>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="cl-table-card" data-aos="fade-up" data-aos-delay="100">
        <div class="table-responsive">
            <table class="table cl-table mb-0">
                <thead>
                    <tr>
                        <th>Şablon</th>
                        <th>Konu</th>
                        <th>Durum</th>
                        <th>Özelleştirilmiş</th>
                        <th class="text-end">İşlem</th>
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
                            <td class="text-end">
                                <a href="{{ route('admin.mail-templates.edit', $template) }}" class="btn btn-sm btn-outline-info" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-envelope-x fs-1 d-block mb-2"></i>
                                Henüz mail şablonu bulunamadı. Lütfen seeder'ı çalıştırın.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
