/**
 * Boyalı Kelimeler - PWA Install Manager
 *
 * - Registers the service worker.
 * - Handles Android/Desktop Chrome install via beforeinstallprompt.
 * - Handles iOS Safari "Add to Home Screen" via a themed instruction modal.
 * - Handles iOS non-Safari (Chrome/Firefox/Edge/in-app) with a 3-layer fallback:
 *     1) Web Share API -> iOS native share sheet -> "Open in Safari"
 *     2) Clipboard API -> copy link with visual feedback
 *     3) Browser-specific manual instructions
 * - Hides the prompt if already installed or the user dismissed it recently.
 */
(function () {
    'use strict';

    // ---- Config ---------------------------------------------------------
    var DISMISS_KEY = 'bk-pwa-dismissed-at';
    var DISMISS_TTL_DAYS = 7;
    var SHOW_DELAY_MS = 2500; // Small delay so the prompt does not fight the loader

    // ---- Environment checks --------------------------------------------
    var ua = (navigator.userAgent || '').toLowerCase();

    var isIos = (function () {
        // iPad on iPadOS 13+ reports as MacIntel with touch support
        var iPadOS = navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1;
        return /iphone|ipad|ipod/.test(ua) || iPadOS;
    })();

    var isIosSafari = (function () {
        if (!isIos) return false;
        // Exclude in-app browsers and non-Safari iOS browsers
        var nonSafari = /crios|fxios|edgios|opios|yabrowser|ucbrowser|fbav|fban|instagram|line|twitter|wv|bytedance|musical_ly|tiktok/.test(ua);
        return !nonSafari;
    })();

    // Precise browser/in-app detection for iOS non-Safari flow
    function detectIosBrowser() {
        if (/crios/.test(ua)) return 'chrome';
        if (/fxios/.test(ua)) return 'firefox';
        if (/edgios/.test(ua)) return 'edge';
        if (/instagram/.test(ua)) return 'instagram';
        if (/fbav|fban|fb_iab/.test(ua)) return 'facebook';
        if (/bytedance|musical_ly|tiktok/.test(ua)) return 'tiktok';
        if (/twitter/.test(ua)) return 'twitter';
        return 'generic';
    }

    var isStandalone =
        (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) ||
        window.navigator.standalone === true ||
        document.referrer.indexOf('android-app://') === 0;

    function dismissedRecently() {
        try {
            var raw = localStorage.getItem(DISMISS_KEY);
            if (!raw) return false;
            var ts = parseInt(raw, 10);
            if (!ts) return false;
            var ageMs = Date.now() - ts;
            return ageMs < DISMISS_TTL_DAYS * 24 * 60 * 60 * 1000;
        } catch (e) {
            return false;
        }
    }

    function markDismissed() {
        try {
            localStorage.setItem(DISMISS_KEY, String(Date.now()));
        } catch (e) {
            /* ignore storage errors (Safari private mode etc.) */
        }
    }

    // ---- Early exit -----------------------------------------------------
    if (isStandalone) return; // already installed
    if (dismissedRecently()) return;

    // ---- Service Worker registration (early, before window.load) --------
    // Register as soon as possible so Chrome can fire beforeinstallprompt
    // during the first page load rather than requiring a refresh.
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function (err) {
            if (window.console && console.warn) {
                console.warn('[PWA] Service worker registration failed:', err);
            }
        });
    }

    // ---- Capture beforeinstallprompt immediately -----------------------
    // Attach at the top level so we never miss the event, even if it fires
    // before DOMContentLoaded. Store the event and show card once DOM is ready.
    var deferredPrompt = null;
    var domReady = false;
    var pendingShowCard = false;

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        if (domReady) {
            // DOM is already ready — show the card now
            window.setTimeout(showCard, SHOW_DELAY_MS);
        } else {
            // DOM not ready yet — flag it so onReady callback shows it
            pendingShowCard = true;
        }
    });

    // ---- DOM ready ------------------------------------------------------
    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    // ---- showCard / hideCard (need DOM refs, defined inside onReady) ----
    var cardEl = null;

    function showCard() {
        if (!cardEl) return;
        cardEl.hidden = false;
        void cardEl.offsetHeight;
        cardEl.classList.add('pwa-install--visible');
    }

    onReady(function () {
        domReady = true;

        // Install card + iOS Safari instruction modal
        var card = document.getElementById('pwaInstall');
        cardEl = card;
        var installBtn = document.getElementById('pwaInstallBtn');
        var installBtnText = document.getElementById('pwaInstallBtnText');
        var installTitle = document.getElementById('pwaInstallTitle');
        var installDesc = document.getElementById('pwaInstallDesc');
        var closeBtn = document.getElementById('pwaInstallClose');

        var iosModal = document.getElementById('pwaIosModal');
        var iosClose = document.getElementById('pwaIosClose');
        var iosDismiss = document.getElementById('pwaIosDismiss');
        var iosBackdrop = document.getElementById('pwaIosBackdrop');

        // iOS non-Safari "Open in Safari" modal
        var safariModal = document.getElementById('pwaOpenSafariModal');
        var safariClose = document.getElementById('pwaSafariClose');
        var safariDismiss = document.getElementById('pwaSafariDismiss');
        var safariBackdrop = document.getElementById('pwaSafariBackdrop');
        var safariOpenBtn = document.getElementById('pwaSafariOpenBtn');
        var safariCopyBtn = document.getElementById('pwaSafariCopyBtn');
        var safariCopyText = document.getElementById('pwaSafariCopyText');
        var safariInfoBtn = document.getElementById('pwaSafariInfoBtn');
        var safariInfo = document.getElementById('pwaSafariInfo');

        if (!card || !installBtn) return;

        function hideCard(persistDismiss) {
            card.classList.remove('pwa-install--visible');
            window.setTimeout(function () {
                card.hidden = true;
            }, 400);
            if (persistDismiss) markDismissed();
        }

        function showIosModal() {
            if (!iosModal) return;
            iosModal.hidden = false;
            void iosModal.offsetHeight;
            iosModal.classList.add('pwa-ios--visible');
            document.body.style.overflow = 'hidden';
        }

        function hideIosModal() {
            if (!iosModal) return;
            iosModal.classList.remove('pwa-ios--visible');
            document.body.style.overflow = '';
            window.setTimeout(function () {
                iosModal.hidden = true;
            }, 300);
        }

        // ---- iOS non-Safari flow -------------------------------------
        function showSafariModal() {
            if (!safariModal) return;

            // Reveal only the instruction block matching the current browser
            var browser = detectIosBrowser();
            var howtos = safariModal.querySelectorAll('.pwa-safari__howto');
            for (var i = 0; i < howtos.length; i++) {
                howtos[i].hidden = howtos[i].getAttribute('data-browser') !== browser;
            }

            safariModal.hidden = false;
            void safariModal.offsetHeight;
            safariModal.classList.add('pwa-safari--visible');
            document.body.style.overflow = 'hidden';
        }

        function hideSafariModal() {
            if (!safariModal) return;
            safariModal.classList.remove('pwa-safari--visible');
            document.body.style.overflow = '';
            window.setTimeout(function () {
                safariModal.hidden = true;
            }, 300);
        }

        // Try Web Share API -> iOS share sheet -> "Open in Safari"
        function tryWebShare() {
            if (!navigator.share) return Promise.reject(new Error('no-share'));
            return navigator.share({
                title: document.title || 'Boyalı Kelimeler',
                text: 'Boyalı Kelimeler',
                url: window.location.href
            });
        }

        // Clipboard copy with feedback
        function copyLink() {
            var url = window.location.origin + '/';

            var done = function () {
                if (safariCopyText) safariCopyText.textContent = 'Kopyalandı!';
                if (safariCopyBtn) safariCopyBtn.classList.add('pwa-safari__copy--success');
                if (navigator.vibrate) {
                    try { navigator.vibrate(30); } catch (e) { /* ignore */ }
                }
                window.setTimeout(function () {
                    if (safariCopyText) safariCopyText.textContent = 'Linki Kopyala';
                    if (safariCopyBtn) safariCopyBtn.classList.remove('pwa-safari__copy--success');
                }, 2200);
            };

            var fail = function () {
                if (safariCopyText) safariCopyText.textContent = 'Kopyalanamadı';
                window.setTimeout(function () {
                    if (safariCopyText) safariCopyText.textContent = 'Linki Kopyala';
                }, 2200);
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(done).catch(function () {
                    legacyCopy(url) ? done() : fail();
                });
            } else {
                legacyCopy(url) ? done() : fail();
            }
        }

        function legacyCopy(text) {
            try {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.top = '-1000px';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                ta.setSelectionRange(0, text.length);
                var ok = document.execCommand('copy');
                document.body.removeChild(ta);
                return ok;
            } catch (e) {
                return false;
            }
        }

        // ---- If beforeinstallprompt already fired before DOM was ready ---
        if (pendingShowCard && deferredPrompt) {
            if (installBtnText) installBtnText.textContent = 'Yükle';
            window.setTimeout(showCard, SHOW_DELAY_MS);
        }

        // ---- iOS Safari: show install card with "Nasıl?" action -------
        if (isIos && isIosSafari) {
            if (installBtnText) installBtnText.textContent = 'Nasıl?';
            window.setTimeout(showCard, SHOW_DELAY_MS);
        }

        // ---- iOS non-Safari: show install card with "Safari'de Aç" ----
        if (isIos && !isIosSafari) {
            if (installTitle) installTitle.textContent = 'Uygulamayı Yükle';
            if (installDesc) installDesc.textContent = "Tek tıkla Safari'de aç, uygulamayı yükle.";
            if (installBtnText) installBtnText.textContent = "Safari'de Aç";
            window.setTimeout(showCard, SHOW_DELAY_MS);
        }

        // ---- Install button click ------------------------------------
        installBtn.addEventListener('click', function () {
            // Android / Desktop flow
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function (choice) {
                    if (choice && choice.outcome === 'accepted') {
                        hideCard(false);
                    } else {
                        hideCard(true);
                    }
                    deferredPrompt = null;
                }).catch(function () {
                    deferredPrompt = null;
                });
                return;
            }

            // iOS Safari -> show A2HS instructions
            if (isIos && isIosSafari) {
                showIosModal();
                return;
            }

            // iOS non-Safari -> show "Open in Safari" modal
            if (isIos && !isIosSafari) {
                showSafariModal();
                return;
            }

            // Fallback: nothing we can do programmatically
            hideCard(true);
        });

        // ---- Close button --------------------------------------------
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                hideCard(true);
            });
        }

        // ---- iOS A2HS modal controls ---------------------------------
        if (iosClose) iosClose.addEventListener('click', hideIosModal);
        if (iosDismiss) iosDismiss.addEventListener('click', function () {
            hideIosModal();
            hideCard(true);
        });
        if (iosBackdrop) iosBackdrop.addEventListener('click', hideIosModal);

        // ---- iOS "Open in Safari" modal controls ---------------------
        if (safariClose) safariClose.addEventListener('click', hideSafariModal);
        if (safariBackdrop) safariBackdrop.addEventListener('click', hideSafariModal);
        if (safariDismiss) safariDismiss.addEventListener('click', function () {
            hideSafariModal();
            hideCard(true);
        });

        if (safariOpenBtn) {
            safariOpenBtn.addEventListener('click', function () {
                tryWebShare().catch(function () {
                    // User cancelled or share not supported
                });
            });
        }

        if (safariCopyBtn) {
            safariCopyBtn.addEventListener('click', copyLink);
        }

        if (safariInfoBtn && safariInfo) {
            safariInfoBtn.addEventListener('click', function () {
                safariInfo.hidden = !safariInfo.hidden;
            });
        }

        // Escape key closes whichever modal is open
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            if (iosModal && !iosModal.hidden) hideIosModal();
            if (safariModal && !safariModal.hidden) hideSafariModal();
        });

        // ---- appinstalled event --------------------------------------
        window.addEventListener('appinstalled', function () {
            hideCard(false);
            hideIosModal();
            hideSafariModal();
            deferredPrompt = null;
            markDismissed();
        });
    });
})();
