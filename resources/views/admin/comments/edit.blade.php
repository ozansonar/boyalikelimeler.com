@extends('layouts.admin')

@section('title', 'Yorum Düzenle — Admin')

@section('content')

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-reset fs-13">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link"><i class="bi bi-house me-1"></i>Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.comments.index') }}" class="breadcrumb-link">Yorumlar</a></li>
            <li class="breadcrumb-item active text-teal">Düzenle</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header d-flex align-items-start align-items-sm-center justify-content-between flex-column flex-sm-row gap-3 mb-4" data-aos="fade-down">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.comments.show', $comment) }}" class="btn-glass" title="Geri Dön"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 class="page-title mb-0">Yorum Düzenle</h1>
                <p class="page-subtitle mb-0">{{ $comment->fullName() }} tarafından yapılan yorum</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-teal" onclick="document.getElementById('commentEditForm').submit()">
                <i class="bi bi-check2 me-1"></i>Kaydet
            </button>
        </div>
    </div>

    <form id="commentEditForm" method="POST" action="{{ route('admin.comments.update', $comment) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Sol: Form Alanları -->
            <div class="col-lg-8">
                <div class="card-dark mb-4" data-aos="fade-up">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-teal"><i class="bi bi-pencil-square"></i></div>
                            <div>
                                <h6 class="mb-0">Yorum Bilgileri</h6>
                                <small class="text-muted">Yorum detaylarını düzenleyin</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Ad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                       id="first_name" name="first_name"
                                       value="{{ old('first_name', $comment->first_name) }}"
                                       required maxlength="100">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Soyad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                       id="last_name" name="last_name"
                                       value="{{ old('last_name', $comment->last_name) }}"
                                       required maxlength="100">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email', $comment->email) }}"
                                       required maxlength="255">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Puan <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}"
                                                   value="{{ $i }}" {{ old('rating', $comment->rating) == $i ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rating{{ $i }}">
                                                {{ $i }} <i class="bi bi-star-fill text-warning"></i>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                @error('rating')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="body" class="form-label">Yorum <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('body') is-invalid @enderror"
                                          id="body" name="body" rows="6"
                                          required minlength="10" maxlength="3000">{{ old('body', $comment->body) }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">En az 10, en fazla 3000 karakter</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ: İçerik Bilgisi -->
            <div class="col-lg-4">
                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="50">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-purple"><i class="bi bi-journal-text"></i></div>
                            <div>
                                <h6 class="mb-0">İlgili İçerik</h6>
                                <small class="text-muted">{{ $comment->contentTypeLabel() }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        @if($comment->commentable)
                            <strong>{{ $comment->commentable->title }}</strong>
                            <div class="mt-2">
                                <span class="cl-category-badge tech">{{ $comment->contentTypeLabel() }}</span>
                            </div>
                            @php
                                $contentUrl = $comment->contentType() === 'icerik'
                                    ? route('literary-works.show', $comment->commentable->slug)
                                    : route('blog.show', $comment->commentable->slug);
                            @endphp
                            <a href="{{ $contentUrl }}" target="_blank" class="text-teal small d-inline-block mt-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i>İçeriği görüntüle
                            </a>
                        @else
                            <p class="text-muted mb-0">İçerik bulunamadı.</p>
                        @endif
                    </div>
                </div>

                <div class="card-dark mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header-custom">
                        <div class="form-section-header mb-0">
                            <div class="form-section-icon bg-icon-blue"><i class="bi bi-info-circle"></i></div>
                            <div>
                                <h6 class="mb-0">Durum Bilgisi</h6>
                                <small class="text-muted">Yorum meta bilgileri</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        <div class="d-flex flex-column gap-2">
                            <div class="text-muted small">
                                <i class="bi bi-calendar me-1"></i>Oluşturulma: {{ $comment->created_at->format('d.m.Y H:i') }}
                            </div>
                            <div class="text-muted small">
                                @if($comment->is_approved)
                                    <i class="bi bi-check-circle me-1 text-neon-green"></i>Durum: <strong class="text-neon-green">Onaylı</strong>
                                @else
                                    <i class="bi bi-hourglass me-1 text-neon-orange"></i>Durum: <strong class="text-neon-orange">Onay Bekliyor</strong>
                                @endif
                            </div>
                            @if($comment->ip_address)
                                <div class="text-muted small">
                                    <i class="bi bi-globe me-1"></i>IP: {{ $comment->ip_address }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
