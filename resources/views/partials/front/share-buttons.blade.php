{{--
    Social share buttons partial.
    Required: $shareUrl, $shareTitle
    Optional: $btnClass (default: 'blogd-share__btn')
--}}
@php
    $btnClass = $btnClass ?? 'blogd-share__btn';
@endphp

<span class="blogd-share__label">
    <i class="fa-solid fa-share-nodes me-2"></i>Paylas:
</span>
<div class="blogd-share__btns">
    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}"
       target="_blank" rel="noopener noreferrer"
       class="{{ $btnClass }} {{ $btnClass }}--twitter" aria-label="Twitter'da paylas">
        <i class="fa-brands fa-x-twitter"></i>
    </a>
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
       target="_blank" rel="noopener noreferrer"
       class="{{ $btnClass }} {{ $btnClass }}--facebook" aria-label="Facebook'ta paylas">
        <i class="fa-brands fa-facebook-f"></i>
    </a>
    <a href="https://api.whatsapp.com/send?text={{ urlencode($shareTitle . ' ' . $shareUrl) }}"
       target="_blank" rel="noopener noreferrer"
       class="{{ $btnClass }} {{ $btnClass }}--whatsapp" aria-label="WhatsApp'ta paylas">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    <button type="button" class="{{ $btnClass }} {{ $btnClass }}--instagram"
            aria-label="Instagram'da paylas"
            data-url="{{ $shareUrl }}">
        <i class="fa-brands fa-instagram"></i>
    </button>
    <button type="button" class="{{ $btnClass }} {{ $btnClass }}--copy"
            aria-label="Baglantiyi kopyala"
            data-url="{{ $shareUrl }}">
        <i class="fa-solid fa-link"></i>
    </button>
</div>
