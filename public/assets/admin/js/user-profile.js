'use strict';

// Profile tab switching
function switchProfileTab(btn, tabId) {
    document.querySelectorAll('.prf-tab').forEach(function (t) {
        t.classList.remove('active');
    });
    document.querySelectorAll('.prf-tab-content').forEach(function (c) {
        c.classList.remove('active');
    });
    btn.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

// Post filter (Blog / Literary)
function filterPosts(btn, type) {
    document.querySelectorAll('.prf-filter-btn').forEach(function (b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    document.querySelectorAll('#prf-posts-grid [data-type]').forEach(function (card) {
        if (type === 'all' || card.dataset.type === type) {
            card.classList.remove('d-none');
        } else {
            card.classList.add('d-none');
        }
    });
}
