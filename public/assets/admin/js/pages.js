/**
 * Pages Management — Admin JS
 * Delete modal, slug generation
 */

/* ==================== Delete Modal ==================== */
function openDeleteModal(id, title) {
    var titleEl = document.getElementById('deleteContentTitle');
    var formEl = document.getElementById('deleteForm');

    if (titleEl) titleEl.textContent = title;
    if (formEl) {
        var baseUrl = window.location.pathname.replace(/\/[^\/]*$/, '');
        formEl.action = baseUrl + '/' + id;
    }

    var modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
}

/* ==================== Slug Generation ==================== */
function generatePageSlug(value) {
    var slugEl = document.getElementById('pageSlug');
    var seoSlugEl = document.getElementById('seoPreviewSlug');

    if (!slugEl) return;

    var slug = value
        .toLowerCase()
        .replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's')
        .replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c')
        .replace(/Ğ/g, 'g').replace(/Ü/g, 'u').replace(/Ş/g, 's')
        .replace(/İ/g, 'i').replace(/Ö/g, 'o').replace(/Ç/g, 'c')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');

    slugEl.value = slug;
    if (seoSlugEl) seoSlugEl.textContent = slug || 'yeni-sayfa';
}

/* ==================== Char Counter ==================== */
function updateCharCounter(input, max) {
    var counter = document.getElementById(input.id + '-counter');
    if (counter) {
        counter.textContent = input.value.length;
    }
}

/* ==================== SEO Preview ==================== */
function updateSeoPreview() {
    var titleInput = document.getElementById('metaTitle');
    var descInput = document.getElementById('metaDescription');
    var pageTitleInput = document.getElementById('pageTitle');

    var previewTitle = document.getElementById('seoPreviewTitle');
    var previewDesc = document.getElementById('seoPreviewDesc');

    if (previewTitle) {
        previewTitle.textContent = (titleInput && titleInput.value) ? titleInput.value : (pageTitleInput ? pageTitleInput.value : 'Sayfa Başlığı');
    }
    if (previewDesc) {
        previewDesc.textContent = (descInput && descInput.value) ? descInput.value : 'Meta açıklama burada görünecek.';
    }
}

