/**
 * Painters Page Management — Admin JS
 * SEO preview + featured painters repeater
 */

document.addEventListener('DOMContentLoaded', function () {
    /* SEO live preview */
    var metaTitleInput = document.getElementById('ppMetaTitle');
    var metaDescInput = document.getElementById('ppMetaDesc');
    var pageTitleInput = document.getElementById('ppTitle');

    if (metaTitleInput) {
        metaTitleInput.addEventListener('input', updateSeoPreview);
    }
    if (metaDescInput) {
        metaDescInput.addEventListener('input', updateSeoPreview);
    }
    if (pageTitleInput) {
        pageTitleInput.addEventListener('input', updateSeoPreview);
    }

    /* ==================== Featured Painters Repeater ==================== */
    var container = document.getElementById('featuredPaintersContainer');
    var addBtn = document.getElementById('addFeaturedPainter');

    if (container && addBtn) {
        var templateRow = container.querySelector('.featured-painter-row');

        addBtn.addEventListener('click', function () {
            var clone = templateRow.cloneNode(true);
            clone.querySelector('select').value = '';
            var labelInput = clone.querySelector('input[name="featured_painter_labels[]"]');
            if (labelInput) { labelInput.value = ''; }
            container.appendChild(clone);
            bindRemoveButtons();
        });

        bindRemoveButtons();
    }

    function bindRemoveButtons() {
        var btns = document.querySelectorAll('.js-remove-featured-painter');
        btns.forEach(function (btn) {
            btn.onclick = function () {
                var rows = container.querySelectorAll('.featured-painter-row');
                var row = btn.closest('.featured-painter-row');
                if (rows.length > 1) {
                    row.remove();
                } else {
                    row.querySelector('select').value = '';
                    var labelInput = row.querySelector('input[name="featured_painter_labels[]"]');
                    if (labelInput) { labelInput.value = ''; }
                }
            };
        });
    }
});

/* ==================== SEO Preview ==================== */
function updateSeoPreview() {
    var metaTitle = document.getElementById('ppMetaTitle');
    var metaDesc = document.getElementById('ppMetaDesc');
    var pageTitle = document.getElementById('ppTitle');

    var previewTitle = document.getElementById('seoPreviewTitle');
    var previewDesc = document.getElementById('seoPreviewDesc');

    if (previewTitle) {
        previewTitle.textContent = (metaTitle && metaTitle.value)
            ? metaTitle.value
            : (pageTitle ? pageTitle.value || 'Ressamlarımız — Boyalı Kelimeler' : 'Ressamlarımız — Boyalı Kelimeler');
    }
    if (previewDesc) {
        previewDesc.textContent = (metaDesc && metaDesc.value)
            ? metaDesc.value
            : 'Boyalı Kelimeler ressamları ile tanışın.';
    }
}
