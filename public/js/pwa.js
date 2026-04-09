/**
 * Boyalı Kelimeler - PWA Install Manager
 *
 * - Registers the service worker.
 * - Handles Android/Desktop Chrome install via beforeinstallprompt.
 * - Handles iOS Safari "Add to Home Screen" via a themed instruction modal.
 * - Hides the prompt if already installed or the user dismissed it recently.
 */
(function () {
    'use strict';

    // ---- Config ---------------------------------------------------------
    var DISMISS_KEY = 'bk-pwa-dismissed-at';
    var DISMISS_TTL_DAYS = 7;
    var SHOW_DELAY_MS = 2500; // Small delay so the prompt does not fight the loader

    // ---- Service Worker registration -----------------------------------
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function (err) {
                // Silent fail - PWA is a progressive enhancement
                if (window.console && console.warn) {
                    console.warn('[PWA] Service worker registration failed:', err);
                }
            });
        });
    }

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
        var nonSafari = /crios|fxios|edgios|opios|yabrowser|ucbrowser|fbav|fban|instagram|line|twitter|wv/.test(ua);
        return !nonSafari;
    })();

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

    // ---- DOM ready ------------------------------------------------------
    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    onReady(function () {
        var card = document.getElementById('pwaInstall');
        var installBtn = document.getElementById('pwaInstallBtn');
        var installBtnText = document.getElementById('pwaInstallBtnText');
        var closeBtn = document.getElementById('pwaInstallClose');

        var iosModal = document.getElementById('pwaIosModal');
        var iosClose = document.getElementById('pwaIosClose');
        var iosDismiss = document.getElementById('pwaIosDismiss');
        var iosBackdrop = document.getElementById('pwaIosBackdrop');

        if (!card || !installBtn) return;

        var deferredPrompt = null;

        function showCard() {
            card.hidden = false;
            // Force reflow so the transition kicks in
            void card.offsetHeight;
            card.classList.add('pwa-install--visible');
        }

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

        // ---- Android / Desktop Chrome ---------------------------------
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            if (installBtnText) installBtnText.textContent = 'Yükle';
            window.setTimeout(showCard, SHOW_DELAY_MS);
        });

        // ---- iOS Safari: no beforeinstallprompt, show card with guidance
        if (isIos && isIosSafari) {
            if (installBtnText) installBtnText.textContent = 'Nasıl?';
            window.setTimeout(showCard, SHOW_DELAY_MS);
        }

        // ---- Install button click ------------------------------------
        installBtn.addEventListener('click', function () {
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

            if (isIos && isIosSafari) {
                showIosModal();
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

        // ---- iOS modal controls --------------------------------------
        if (iosClose) iosClose.addEventListener('click', hideIosModal);
        if (iosDismiss) iosDismiss.addEventListener('click', function () {
            hideIosModal();
            hideCard(true);
        });
        if (iosBackdrop) iosBackdrop.addEventListener('click', hideIosModal);

        // Escape key closes iOS modal
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && iosModal && !iosModal.hidden) {
                hideIosModal();
            }
        });

        // ---- appinstalled event --------------------------------------
        window.addEventListener('appinstalled', function () {
            hideCard(false);
            deferredPrompt = null;
            markDismissed(); // Avoid re-showing immediately
        });
    });
})();
