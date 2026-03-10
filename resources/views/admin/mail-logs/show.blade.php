@extends('layouts.admin')

@section('title', 'Mail Detay — Boyalı Kelimeler Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3" data-aos="fade-down" data-aos-duration="400">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house"></i> Ana Sayfa</a></li>
            <li><a href="{{ route('admin.mail-logs.index') }}" class="breadcrumb-link">Mail Logları</a></li>
            <li class="breadcrumb-item active text-teal">Detay #{{ $log->id }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
        <div>
            <h1 class="page-title">{{ $log->subject ?: 'Konu yok' }}</h1>
            <p class="page-subtitle">Mail #{{ $log->id }} — {{ $log->created_at->format('d.m.Y H:i:s') }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($log->body)
                <form action="{{ route('admin.mail-logs.resend', $log) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu mail yeniden gönderilecek. Emin misiniz?')">
                    @csrf
                    <button type="submit" class="btn-teal"><i class="bi bi-arrow-repeat me-1"></i>Yeniden Gönder</button>
                </form>
            @endif
            <a href="{{ route('admin.mail-logs.index') }}" class="btn-glass"><i class="bi bi-arrow-left"></i> Geri Dön</a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Mail Info -->
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="0">
            <div class="card-dark mb-4">
                <div class="card-header-custom">
                    <h6><i class="bi bi-info-circle me-2 text-teal"></i>Mail Bilgileri</h6>
                </div>
                <div class="card-body-custom">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Durum</small>
                        @if($log->isSent())
                            <span class="badge bg-success bg-opacity-25 text-success fs-13"><i class="bi bi-check-circle-fill me-1"></i>Gönderildi</span>
                        @elseif($log->isFailed())
                            <span class="badge bg-danger bg-opacity-25 text-danger fs-13"><i class="bi bi-x-circle-fill me-1"></i>Başarısız</span>
                        @else
                            <span class="badge bg-warning bg-opacity-25 text-warning fs-13"><i class="bi bi-hourglass-split me-1"></i>Bekliyor</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Alıcı E-posta</small>
                        <span class="fw-medium">{{ $log->to_email }}</span>
                    </div>

                    @if($log->to_name)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Alıcı Adı</small>
                            <span>{{ $log->to_name }}</span>
                        </div>
                    @endif

                    @if($log->user)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Kullanıcı</small>
                            <span class="text-teal"><i class="bi bi-person-fill me-1"></i>{{ $log->user->name }}</span>
                        </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Konu</small>
                        <span>{{ $log->subject ?: '—' }}</span>
                    </div>

                    @if($log->mailable_class)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Mail Türü</small>
                            <span class="badge bg-info bg-opacity-25 text-info">{{ class_basename($log->mailable_class) }}</span>
                        </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Oluşturulma</small>
                        <span>{{ $log->created_at->format('d.m.Y H:i:s') }}</span>
                    </div>

                    @if($log->sent_at)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Gönderilme</small>
                            <span>{{ $log->sent_at->format('d.m.Y H:i:s') }}</span>
                        </div>
                    @endif

                    @if($log->error_message)
                        <div class="mb-0">
                            <small class="text-muted d-block mb-1">Hata Mesajı</small>
                            <div class="p-2 rounded bg-danger bg-opacity-10 border border-danger border-opacity-25">
                                <small class="text-danger">{{ $log->error_message }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mail Body -->
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
            <div class="card-dark">
                <div class="card-header-custom">
                    <h6><i class="bi bi-envelope-open me-2 text-teal"></i>Mail İçeriği</h6>
                </div>
                <div class="card-body-custom">
                    @if($log->body)
                        @if($isHtml)
                            <div class="mail-body-preview p-3 rounded bg-dark bg-opacity-25 overflow-auto" data-max-height="600">
                                <iframe id="mailBodyFrame" class="w-100 border-0 mail-body-iframe"></iframe>
                            </div>
                        @else
                            <div class="mail-body-preview p-3 rounded bg-dark bg-opacity-25 overflow-auto" data-max-height="600">
                                <pre class="text-light mb-0 mail-body-text">{{ $log->body }}</pre>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>
                            Mail içeriği kaydedilmemiş.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .mail-body-iframe { min-height: 400px; }
    .mail-body-text { white-space: pre-wrap; word-wrap: break-word; font-size: 0.9rem; line-height: 1.6; }
    [data-max-height] { max-height: 600px; overflow-y: auto; }
</style>
@endpush

@if(!empty($log->body) && !empty($isHtml))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var iframe = document.getElementById('mailBodyFrame');
    if (!iframe) return;

    var doc = iframe.contentDocument || iframe.contentWindow.document;
    doc.open();
    doc.write({!! json_encode($log->body) !!});
    doc.close();

    function resizeIframe() {
        try {
            var height = doc.body.scrollHeight;
            iframe.style.height = Math.max(height + 20, 400) + 'px';
        } catch(e) {}
    }

    iframe.onload = resizeIframe;
    setTimeout(resizeIframe, 500);
});
</script>
@endpush
@endif
