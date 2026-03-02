/**
 * Pages Management — Admin JS
 * SEO preview
 */

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
