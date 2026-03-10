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

// ── Mail Theme Preview ──
var _mtPreviewTimer = null;

function syncColorInput(textInput, colorId) {
    var val = textInput.value.trim();
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        var colorInput = document.getElementById(colorId);
        if (colorInput) {
            colorInput.value = val;
        }
        updateMailThemePreview();
    }
}

function getMailThemeFormData() {
    var form = document.getElementById('mailThemeForm');
    if (!form) return null;

    var data = {};
    data.primary_color = form.querySelector('[name="primary_color"][type="color"]').value;
    data.primary_dark = form.querySelector('[name="primary_dark"][type="color"]').value;
    data.bg_color = form.querySelector('[name="bg_color"][type="color"]').value;
    data.card_bg = form.querySelector('[name="card_bg"][type="color"]').value;
    data.text_color = form.querySelector('[name="text_color"][type="color"]').value;
    data.text_muted = form.querySelector('[name="text_muted"][type="color"]').value;
    data.footer_text = form.querySelector('[name="footer_text"]').value;

    // show_social checkbox
    var checkbox = form.querySelector('input[name="show_social"][type="checkbox"]');
    data.show_social = checkbox && checkbox.checked ? '1' : '0';

    // Sync text inputs with color inputs
    form.querySelectorAll('.stg-color-field').forEach(function (field) {
        var colorInp = field.querySelector('input[type="color"]');
        var textInp = field.querySelector('input[type="text"]');
        if (colorInp && textInp) {
            textInp.value = colorInp.value.toUpperCase();
        }
    });

    return data;
}

function updateMailThemePreview() {
    clearTimeout(_mtPreviewTimer);
    _mtPreviewTimer = setTimeout(function () {
        var data = getMailThemeFormData();
        if (!data) return;

        var token = document.querySelector('meta[name="csrf-token"]');
        if (!token) return;

        fetch('/admin/settings/mail-theme/preview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            var iframe = document.getElementById('mailThemePreviewFrame');
            if (iframe && json.html) {
                var doc = iframe.contentDocument || iframe.contentWindow.document;
                doc.open();
                doc.write(json.html);
                doc.close();
            }
        })
        .catch(function (err) {
            console.warn('Mail theme preview failed:', err);
        });
    }, 300);
}

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {
    initImagePreview('logoInput', 'logoImg', 'logoDefault', 2048);
    initImagePreview('faviconInput', 'faviconImg', 'faviconDefault', 512);
    initImagePreview('mailLogoInput', 'mailLogoImg', 'mailLogoDefault', 1024);

    // Load initial mail theme preview if on that tab
    var mailThemePanel = document.getElementById('stg-mail-theme');
    if (mailThemePanel && mailThemePanel.classList.contains('active')) {
        updateMailThemePreview();
    }

    // Load preview when switching to mail theme tab
    var originalSwitch = window.switchSettingsTab;
    window.switchSettingsTab = function (elOrId, panelId) {
        originalSwitch(elOrId, panelId);
        var targetId = panelId || elOrId;
        if (targetId === 'stg-mail-theme') {
            updateMailThemePreview();
        }
    };
});
