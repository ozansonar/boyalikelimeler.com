<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Bakım Modu — Boyalı Kelimeler</title>
    <link href="{{ asset('vendor/fonts/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/5.3.8/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" rel="stylesheet">
</head>
<body>
    <section class="error-page error-page--fullscreen">
        <div class="container">
            <div class="error-page__content">
                <span class="error-page__code">503</span>
                <h1 class="error-page__title">Bakım Çalışması</h1>
                <p class="error-page__text">{{ $exception->getMessage() ?: 'Sitemiz şu anda bakım çalışması nedeniyle geçici olarak kullanılamıyor. Kısa süre içinde tekrar hizmetinizdeyiz.' }}</p>
            </div>
        </div>
    </section>
</body>
</html>
