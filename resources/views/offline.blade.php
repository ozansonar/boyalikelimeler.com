<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0F0F12">
    <title>Bağlantı Yok — Boyalı Kelimeler</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <style>
        :root {
            --gold: #D4AF37;
            --gold-light: #E2CFA0;
            --dark: #0F0F12;
            --dark-surface: #1A1A1E;
            --silver: #C5C8CE;
            --white-muted: #D0CFC8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: radial-gradient(ellipse at top, #1A1A1E 0%, #0F0F12 60%);
            color: var(--white-muted);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .offline {
            width: 100%;
            max-width: 480px;
            text-align: center;
            padding: 40px 28px;
            background: linear-gradient(160deg, #1A1A1E 0%, #222226 100%);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 20px;
            box-shadow: 0 20px 60px -10px rgba(0, 0, 0, 0.6);
        }

        .offline__icon {
            width: 84px;
            height: 84px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.08);
            border: 1px solid rgba(212, 175, 55, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            color: var(--gold);
        }

        .offline__title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gold);
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .offline__text {
            font-size: 0.9375rem;
            color: var(--silver);
            margin-bottom: 26px;
        }

        .offline__btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--gold);
            color: var(--dark);
            border: none;
            border-radius: 999px;
            font-family: inherit;
            font-size: 0.9375rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: background 240ms ease, transform 240ms ease;
            box-shadow: 0 8px 20px -6px rgba(212, 175, 55, 0.5);
        }

        .offline__btn:hover,
        .offline__btn:focus-visible {
            background: var(--gold-light);
            transform: translateY(-1px);
            outline: none;
        }

        .offline__brand {
            margin-top: 28px;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 0.875rem;
            color: var(--silver);
            letter-spacing: 0.5px;
        }

        .offline__brand span {
            color: var(--gold);
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main class="offline" role="main">
        <div class="offline__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="1" y1="1" x2="23" y2="23"/>
                <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
                <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
                <path d="M10.71 5.05A16 16 0 0 1 22.58 9"/>
                <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                <line x1="12" y1="20" x2="12.01" y2="20"/>
            </svg>
        </div>

        <h1 class="offline__title">Bağlantı Yok</h1>
        <p class="offline__text">
            İnternet bağlantın şu anda mevcut değil. Bağlantını kontrol ettikten sonra tekrar dene.
        </p>

        <button type="button" class="offline__btn" onclick="window.location.reload()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"/>
                <polyline points="1 20 1 14 7 14"/>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
            </svg>
            Tekrar Dene
        </button>

        <p class="offline__brand">
            <span>Boyalı Kelimeler</span> — Sosyal Çöküntüye Sanatsal Direniş
        </p>
    </main>

    <script>
        // Auto-reload when connection is back
        window.addEventListener('online', function () {
            window.location.reload();
        });
    </script>
</body>
</html>
