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

    /* ==================== Featured Authors Repeater ==================== */
    var container = document.getElementById('featuredAuthorsContainer');
    var addBtn = document.getElementById('addFeaturedAuthor');

    if (container && addBtn) {
        var templateRow = container.querySelector('.featured-author-row');

        addBtn.addEventListener('click', function () {
            var clone = templateRow.cloneNode(true);
            clone.querySelector('select').value = '';
            container.appendChild(clone);
            bindRemoveButtons();
        });

        bindRemoveButtons();
    }

    function bindRemoveButtons() {
        var btns = document.querySelectorAll('.js-remove-featured');
        btns.forEach(function (btn) {
            btn.onclick = function () {
                var rows = container.querySelectorAll('.featured-author-row');
                if (rows.length > 1) {
                    btn.closest('.featured-author-row').remove();
                } else {
                    btn.closest('.featured-author-row').querySelector('select').value = '';
                }
            };
        });
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
