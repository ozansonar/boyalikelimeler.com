/**
 * Pages Management — Admin JS
 * SEO preview + cover image remove handler
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

/* ==================== Cover Image Remove ==================== */
document.addEventListener('DOMContentLoaded', function () {
    var removeBtn = document.getElementById('coverRemoveBtn');
    var previewWrap = document.getElementById('coverPreviewWrap');
    var removedNotice = document.getElementById('coverRemovedNotice');
    var removeFlag = document.getElementById('removeCoverFlag');
    var coverInput = document.getElementById('coverInput');

    if (!removeBtn || !previewWrap || !removeFlag) return;

    removeBtn.addEventListener('click', function () {
        if (!window.confirm('Kapak görselini kaldırmak istediğine emin misin? Kaydet butonuna bastığında silinecek.')) {
            return;
        }
        previewWrap.classList.add('d-none');
        if (removedNotice) removedNotice.classList.remove('d-none');
        removeFlag.value = '1';
        // Clear any newly selected file so the removal takes effect
        if (coverInput) coverInput.value = '';
    });

    // If user picks a new file after clicking remove, undo the removal flag
    if (coverInput) {
        coverInput.addEventListener('change', function () {
            if (coverInput.files && coverInput.files.length > 0) {
                removeFlag.value = '0';
                if (removedNotice) removedNotice.classList.add('d-none');
            }
        });
    }
});
