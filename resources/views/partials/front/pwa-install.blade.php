{{-- =============================================================
     PWA Install Prompt
     - Android/Desktop Chrome: native beforeinstallprompt
     - iOS Safari: "Add to Home Screen" talimat modal'i
     - Kullanici kapatirsa 7 gun tekrar gosterilmez
============================================================= --}}
<div id="pwaInstall" class="pwa-install" hidden>
    <div class="pwa-install__card" role="dialog" aria-labelledby="pwaInstallTitle" aria-describedby="pwaInstallDesc">
        <button type="button" class="pwa-install__close" id="pwaInstallClose" aria-label="Kapat">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="pwa-install__icon">
            <img src="{{ asset('icons/icon-192x192.png') }}"
                 alt="Boyalı Kelimeler"
                 width="56"
                 height="56"
                 loading="lazy">
        </div>

        <div class="pwa-install__body">
            <h3 id="pwaInstallTitle" class="pwa-install__title">Uygulamayı Yükle</h3>
            <p id="pwaInstallDesc" class="pwa-install__text">
                Boyalı Kelimeler'i telefonuna ekle, tek dokunuşla eriş.
            </p>
        </div>

        <button type="button" class="pwa-install__btn" id="pwaInstallBtn">
            <i class="fa-solid fa-arrow-down-to-line me-1"></i>
            <span id="pwaInstallBtnText">Yükle</span>
        </button>
    </div>
</div>

{{-- iOS "Ana Ekrana Ekle" talimat modal'i --}}
<div id="pwaIosModal" class="pwa-ios" hidden>
    <div class="pwa-ios__backdrop" id="pwaIosBackdrop"></div>
    <div class="pwa-ios__dialog" role="dialog" aria-labelledby="pwaIosTitle">
        <button type="button" class="pwa-ios__close" id="pwaIosClose" aria-label="Kapat">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="pwa-ios__header">
            <img src="{{ asset('icons/icon-192x192.png') }}"
                 alt="Boyalı Kelimeler"
                 width="64"
                 height="64"
                 class="pwa-ios__icon"
                 loading="lazy">
            <h3 id="pwaIosTitle" class="pwa-ios__title">Ana Ekrana Ekle</h3>
            <p class="pwa-ios__subtitle">Boyalı Kelimeler'i uygulama gibi kullan</p>
        </div>

        <ol class="pwa-ios__steps">
            <li class="pwa-ios__step">
                <span class="pwa-ios__step-num">1</span>
                <span class="pwa-ios__step-text">
                    Safari'nin alt çubuğundaki
                    <i class="fa-solid fa-arrow-up-from-bracket pwa-ios__inline-icon" aria-hidden="true"></i>
                    <strong>Paylaş</strong> butonuna dokun
                </span>
            </li>
            <li class="pwa-ios__step">
                <span class="pwa-ios__step-num">2</span>
                <span class="pwa-ios__step-text">
                    Listeyi aşağı kaydır ve
                    <i class="fa-regular fa-square-plus pwa-ios__inline-icon" aria-hidden="true"></i>
                    <strong>Ana Ekrana Ekle</strong> seçeneğini seç
                </span>
            </li>
            <li class="pwa-ios__step">
                <span class="pwa-ios__step-num">3</span>
                <span class="pwa-ios__step-text">
                    Sağ üstten <strong>Ekle</strong>'ye dokun — hazır!
                </span>
            </li>
        </ol>

        <button type="button" class="pwa-ios__dismiss" id="pwaIosDismiss">Anladım</button>
    </div>
</div>

{{-- =============================================================
     iOS non-Safari "Safari'de Aç" yönlendirme modal'i
     Chrome, Firefox, Edge, Instagram, Facebook, TikTok, Twitter
     ve diger in-app browser'larda tek tik Safari'ye gecis
============================================================= --}}
<div id="pwaOpenSafariModal" class="pwa-safari" hidden>
    <div class="pwa-safari__backdrop" id="pwaSafariBackdrop"></div>
    <div class="pwa-safari__dialog" role="dialog" aria-labelledby="pwaSafariTitle">
        <button type="button" class="pwa-safari__close" id="pwaSafariClose" aria-label="Kapat">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="pwa-safari__header">
            <img src="{{ asset('icons/icon-192x192.png') }}"
                 alt="Boyalı Kelimeler"
                 width="64"
                 height="64"
                 class="pwa-safari__icon"
                 loading="lazy">
            <h3 id="pwaSafariTitle" class="pwa-safari__title">Safari'de Aç</h3>
            <p class="pwa-safari__subtitle">
                Uygulamayı yüklemek için
                <button type="button" class="pwa-safari__info-btn" id="pwaSafariInfoBtn" aria-label="Neden Safari gerekli">
                    Safari gerekli
                    <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
                </button>
            </p>
        </div>

        {{-- "Neden Safari?" info box (hidden by default) --}}
        <div class="pwa-safari__info" id="pwaSafariInfo" hidden>
            <i class="fa-solid fa-lock pwa-safari__info-icon" aria-hidden="true"></i>
            <p class="pwa-safari__info-text">
                Apple, iOS cihazlarda PWA yüklemesini sadece Safari tarayıcısına izin verir.
                Diğer tarayıcılar (Chrome, Firefox vb.) bu işlemi gerçekleştiremez.
            </p>
        </div>

        {{-- Primary CTA: Web Share API → iOS share sheet → Safari --}}
        <button type="button" class="pwa-safari__primary" id="pwaSafariOpenBtn">
            <i class="fa-brands fa-safari pwa-safari__primary-icon" aria-hidden="true"></i>
            <span class="pwa-safari__primary-text">
                <strong>Safari'de Aç</strong>
                <small>Tek dokunuşla geç</small>
            </span>
            <i class="fa-solid fa-arrow-right pwa-safari__primary-arrow" aria-hidden="true"></i>
        </button>

        {{-- Secondary: Copy link --}}
        <button type="button" class="pwa-safari__copy" id="pwaSafariCopyBtn">
            <i class="fa-regular fa-copy" aria-hidden="true"></i>
            <span id="pwaSafariCopyText">Linki Kopyala</span>
        </button>

        {{-- Divider --}}
        <div class="pwa-safari__divider" aria-hidden="true">
            <span>veya manuel</span>
        </div>

        {{-- Browser-specific instructions. JS will show the right one. --}}
        <div class="pwa-safari__instructions">
            {{-- iOS Chrome --}}
            <div class="pwa-safari__howto" data-browser="chrome" hidden>
                <div class="pwa-safari__howto-head">
                    <i class="fa-brands fa-chrome pwa-safari__howto-icon" aria-hidden="true"></i>
                    <span>Chrome'daysan</span>
                </div>
                <ol class="pwa-safari__steps">
                    <li>
                        Adres çubuğunun yanındaki
                        <i class="fa-solid fa-ellipsis pwa-safari__inline-icon" aria-hidden="true"></i>
                        <strong>üç nokta</strong> menüsüne dokun
                    </li>
                    <li>Listeden <strong>Safari'de Aç</strong>'ı seç</li>
                </ol>
            </div>

            {{-- iOS Firefox --}}
            <div class="pwa-safari__howto" data-browser="firefox" hidden>
                <div class="pwa-safari__howto-head">
                    <i class="fa-brands fa-firefox-browser pwa-safari__howto-icon" aria-hidden="true"></i>
                    <span>Firefox'taysan</span>
                </div>
                <ol class="pwa-safari__steps">
                    <li>
                        Alttaki
                        <i class="fa-solid fa-ellipsis pwa-safari__inline-icon" aria-hidden="true"></i>
                        <strong>menü</strong> simgesine dokun
                    </li>
                    <li><strong>Sayfayı Aç</strong> → <strong>Safari</strong>'yi seç</li>
                </ol>
            </div>

            {{-- iOS Edge --}}
            <div class="pwa-safari__howto" data-browser="edge" hidden>
                <div class="pwa-safari__howto-head">
                    <i class="fa-brands fa-edge pwa-safari__howto-icon" aria-hidden="true"></i>
                    <span>Edge'desen</span>
                </div>
                <ol class="pwa-safari__steps">
                    <li>
                        Alt menüdeki
                        <i class="fa-solid fa-ellipsis pwa-safari__inline-icon" aria-hidden="true"></i>
                        <strong>üç nokta</strong>ya dokun
                    </li>
                    <li><strong>Safari'de Aç</strong>'ı seç</li>
                </ol>
            </div>

            {{-- Instagram in-app browser --}}
            <div class="pwa-safari__howto" data-browser="instagram" hidden>
                <div class="pwa-safari__howto-head">
                    <i class="fa-brands fa-instagram pwa-safari__howto-icon" aria-hidden="true"></i>
                    <span>Instagram'dan geldiysen</span>
                </div>
                <ol class="pwa-safari__steps">
                    <li>
                        Sağ üstteki
                        <i class="fa-solid fa-ellipsis pwa-safari__inline-icon" aria-hidden="true"></i>
                        <strong>üç nokta</strong>ya dokun
                    </li>
                    <li><strong>Harici tarayıcıda aç</strong>'ı seç</li>
                    <li>Açılan sayfada bu butonu tekrar kullan</li>
                </ol>
            </div>

            {{-- Facebook in-app browser --}}
            <div class="pwa-safari__howto" data-browser="facebook" hidden>
                <div class="pwa-safari__howto-head">
                    <i class="fa-brands fa-facebook pwa-safari__howto-icon" aria-hidden="true"></i>
                    <span>Facebook'tan geldiysen</span>
                </div>
                <ol class="pwa-safari__steps">
                    <li>
                        Sağ alttaki
                        <i class="fa-solid fa-ellipsis pwa-safari__inline-icon" aria-hidden="true"></i>
                        <strong>üç nokta</strong>ya dokun
                    </li>
                    <li><strong>Tarayıcıda aç</strong>'ı seç</li>
                </ol>
            </div>

            {{-- TikTok in-app browser --}}
            <div class="pwa-safari__howto" data-browser="tiktok" hidden>
                <div class="pwa-safari__howto-head">
                    <i class="fa-brands fa-tiktok pwa-safari__howto-icon" aria-hidden="true"></i>
                    <span>TikTok'tan geldiysen</span>
                </div>
                <ol class="pwa-safari__steps">
                    <li>
                        Sağ üstteki
                        <i class="fa-solid fa-ellipsis pwa-safari__inline-icon" aria-hidden="true"></i>
                        <strong>üç nokta</strong>ya dokun
                    </li>
                    <li><strong>Varsayılan tarayıcıda aç</strong>'ı seç</li>
                </ol>
            </div>

            {{-- Twitter/X in-app browser --}}
            <div class="pwa-safari__howto" data-browser="twitter" hidden>
                <div class="pwa-safari__howto-head">
                    <i class="fa-brands fa-x-twitter pwa-safari__howto-icon" aria-hidden="true"></i>
                    <span>X/Twitter'dan geldiysen</span>
                </div>
                <ol class="pwa-safari__steps">
                    <li>
                        Sağ üstteki
                        <i class="fa-solid fa-ellipsis pwa-safari__inline-icon" aria-hidden="true"></i>
                        <strong>paylaş</strong> simgesine dokun
                    </li>
                    <li><strong>Tarayıcıda aç</strong>'ı seç</li>
                </ol>
            </div>

            {{-- Generic fallback --}}
            <div class="pwa-safari__howto" data-browser="generic" hidden>
                <div class="pwa-safari__howto-head">
                    <i class="fa-solid fa-mobile-screen pwa-safari__howto-icon" aria-hidden="true"></i>
                    <span>Manuel yöntem</span>
                </div>
                <ol class="pwa-safari__steps">
                    <li>Tarayıcının menüsünü aç (genelde <strong>üç nokta</strong> veya <strong>paylaş</strong>)</li>
                    <li><strong>Safari'de Aç</strong> / <strong>Tarayıcıda Aç</strong> seçeneğini bul</li>
                    <li>Safari açıldığında bu butonu tekrar kullan</li>
                </ol>
            </div>
        </div>

        <button type="button" class="pwa-safari__dismiss" id="pwaSafariDismiss">Şimdi değil</button>
    </div>
</div>
