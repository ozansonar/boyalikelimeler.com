@php
    $recaptchaService = app(\App\Services\RecaptchaService::class);
    $isEnabled = $recaptchaService->isEnabled();
    $siteKey = $recaptchaService->getSiteKey();
@endphp

@if($isEnabled && $siteKey)
    <div class="g-recaptcha" data-sitekey="{{ $siteKey }}" data-theme="dark"></div>
    @error('g-recaptcha-response')
        <span class="auth-form__error-text d-block mt-1">{{ $message }}</span>
    @enderror

    @once
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js?hl=tr" async defer></script>
        @endpush
    @endonce
@endif
