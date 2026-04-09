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
