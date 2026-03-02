@props([
    'title',
    'subtitle' => '',
])

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-3" data-aos="fade-down">
    <div>
        <h1 class="page-title">{{ $title }}</h1>
        @if($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if($slot->isNotEmpty())
        <div class="d-flex gap-2 flex-wrap">
            {{ $slot }}
        </div>
    @endif
</div>
