/**
 * Authors Page Management — Admin JS
 * Rich text editor + SEO preview + word counter
 */

document.addEventListener('DOMContentLoaded', function () {
    var editor = document.getElementById('contentEditor');
    var hidden = document.getElementById('bodyHidden');

    if (editor && hidden) {
        /* Sync editor content to hidden textarea on input */
        editor.addEventListener('input', function () {
            hidden.value = editor.innerHTML;
            updateWordCount();
        });

        /* Sync before form submit */
        var form = document.getElementById('authorsPageForm');
        if (form) {
            form.addEventListener('submit', function () {
                hidden.value = editor.innerHTML;
            });
        }

        /* Initial word count */
        updateWordCount();
    }

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

/* ==================== Word Count ==================== */
function updateWordCount() {
    var editor = document.getElementById('contentEditor');
    var wordEl = document.getElementById('wordCount');
    var readEl = document.getElementById('readTime');

    if (!editor || !wordEl) return;

    var text = (editor.innerText || '').trim();
    var words = text ? text.split(/\s+/).length : 0;

    wordEl.textContent = words;
    if (readEl) {
        readEl.textContent = Math.max(1, Math.ceil(words / 200));
    }
}

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
