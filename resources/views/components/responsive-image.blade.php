@props([
    'path' => null,
    'alt' => '',
    'size' => 'md',
    'class' => 'img-fluid',
    'width' => null,
    'height' => null,
])

@if($path)
    <img src="{{ upload_url($path, $size) }}"
         srcset="{{ upload_url($path, 'sm') }} 480w, {{ upload_url($path, 'md') }} 768w, {{ upload_url($path, 'lg') }} 1200w"
         sizes="(max-width: 480px) 480px, (max-width: 768px) 768px, 1200px"
         alt="{{ $alt }}"
         class="{{ $class }}"
         @if($width) width="{{ $width }}" @endif
         @if($height) height="{{ $height }}" @endif
         loading="lazy"
         {{ $attributes }}>
@endif
