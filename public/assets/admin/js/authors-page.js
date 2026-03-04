/**
 * Authors Page Management — Admin JS
 * SEO preview
 */

document.addEventListener('DOMContentLoaded', function () {
    /* SEO live preview */
    var metaTitleInput = document.getElementById('apMetaTitle');
    var metaDescInput = document.getElementById('apMetaDesc');
    var pageTitleInput = document.getElementById('apTitle');

    if (metaTitleInput) {
        metaTitleInput.addEventListener('input', updateSeoPreview);
    }
    if (metaDescInput) {
        metaDescInput.addEventListener('input', updateSeoPreview);
    }
    if (pageTitleInput) {
        pageTitleInput.addEventListener('input', updateSeoPreview);
    }
});

/* ==================== SEO Preview ==================== */
function updateSeoPreview() {
    var metaTitle = document.getElementById('apMetaTitle');
    var metaDesc = document.getElementById('apMetaDesc');
    var pageTitle = document.getElementById('apTitle');

    var previewTitle = document.getElementById('seoPreviewTitle');
    var previewDesc = document.getElementById('seoPreviewDesc');

    if (previewTitle) {
        previewTitle.textContent = (metaTitle && metaTitle.value)
            ? metaTitle.value
            : (pageTitle ? pageTitle.value || 'Yazarlarımız — Boyalı Kelimeler' : 'Yazarlarımız — Boyalı Kelimeler');
    }
    if (previewDesc) {
        previewDesc.textContent = (metaDesc && metaDesc.value)
            ? metaDesc.value
            : 'Boyalı Kelimeler yazarları ile tanışın.';
    }
}
