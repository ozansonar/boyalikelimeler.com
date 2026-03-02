/**
 * Settings Page JS
 * Tab switching, maintenance toggle, SEO counter, send mode toggle
 */

// ── Tab Switching ──
function switchSettingsTab(elOrId, panelId) {
    // Called from both desktop nav (click) and mobile select (change)
    var targetId = panelId || elOrId;

    // Remove active from all nav items and panels
    document.querySelectorAll('.stg-nav-item').forEach(function (n) {
        n.classList.remove('active');
    });
    document.querySelectorAll('.stg-panel').forEach(function (p) {
        p.classList.remove('active');
    });

    // Activate target panel
    var panel = document.getElementById(targetId);
    if (panel) {
        panel.classList.add('active');
    }

    // Activate nav item (desktop)
    if (typeof elOrId === 'object' && elOrId !== null) {
        elOrId.classList.add('active');
    } else {
        // Find matching nav item by href
        document.querySelectorAll('.stg-nav-item').forEach(function (n) {
            if (n.getAttribute('href') === '#' + targetId) {
                n.classList.add('active');
            }
        });
    }

    // Scroll to top on mobile
    var content = document.querySelector('.stg-content');
    if (content) {
        content.scrollTop = 0;
    }
}

// ── Maintenance Toggle UI ──
function toggleMaintenance(el) {
    var label = document.getElementById('maintLabel');
    var status = document.getElementById('maintStatus');
    if (!status) return;

    var indicator = status.querySelector('.stg-maint-indicator');

    if (el.checked) {
        if (label) label.textContent = 'Bakım Modu Aktif';
        if (label && label.nextElementSibling) {
            label.nextElementSibling.textContent = 'Kullanıcılar bakım sayfasını görüyor';
        }
        if (indicator) {
            indicator.classList.remove('stg-maint-off');
            indicator.classList.add('stg-maint-on');
        }
    } else {
        if (label) label.textContent = 'Bakım Modu Kapalı';
        if (label && label.nextElementSibling) {
            label.nextElementSibling.textContent = 'Sistem normal çalışıyor';
        }
        if (indicator) {
            indicator.classList.remove('stg-maint-on');
            indicator.classList.add('stg-maint-off');
        }
    }
}

// ── SEO Character Counter ──
function updateSeoCounter(input, max) {
    var len = input.value.length;
    var name = input.getAttribute('name');

    if (name === 'meta_title') {
        var counter = document.getElementById('metaTitleCount');
        var preview = document.getElementById('seoPreviewTitle');
        if (counter) counter.textContent = len;
        if (preview) preview.textContent = input.value || 'Site Başlığı';
    } else if (name === 'meta_description') {
        var counter = document.getElementById('metaDescCount');
        var preview = document.getElementById('seoPreviewDesc');
        if (counter) counter.textContent = len;
        if (preview) preview.textContent = input.value || 'Site açıklaması burada görünecek.';
    }
}

// ── Send Mode Toggle ──
function toggleSendMode() {
    var debugField = document.getElementById('debugEmailsField');
    if (!debugField) return;

    var devRadio = document.querySelector('input[name="send_mode"][value="developer"]');
    if (devRadio && devRadio.checked) {
        debugField.classList.remove('d-none');
    } else {
        debugField.classList.add('d-none');
    }
}

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {
    initImagePreview('logoInput', 'logoImg', 'logoDefault', 2048);
    initImagePreview('faviconInput', 'faviconImg', 'faviconDefault', 512);
    initImagePreview('mailLogoInput', 'mailLogoImg', 'mailLogoDefault', 1024);
});
