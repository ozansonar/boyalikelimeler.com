/**
 * Menus Management — Admin JS
 * Edit modal, sortable drag-drop
 */

/* ==================== Edit Item Modal ==================== */
function openEditModal(id, title, url, icon, target, isActive, sortOrder, parentId) {
    var form = document.getElementById('editItemForm');
    if (!form) return;

    form.action = ITEM_BASE_URL + '/' + id;

    document.getElementById('editTitle').value = title;
    document.getElementById('editUrl').value = url;
    document.getElementById('editIcon').value = icon;
    document.getElementById('editTarget').value = target;
    document.getElementById('editIsActive').value = isActive ? '1' : '0';
    document.getElementById('editSortOrder').value = sortOrder;

    var parentSelect = document.getElementById('editParentId');
    if (parentSelect) {
        parentSelect.value = parentId || '';
    }

    var modal = new bootstrap.Modal(document.getElementById('editItemModal'));
    modal.show();
}

/* ==================== Sortable (drag & drop reorder) ==================== */
function initSortable() {
    var tbody = document.getElementById('sortableItems');
    if (!tbody || typeof Sortable === 'undefined') return;

    Sortable.create(tbody, {
        handle: '.sortable-handle',
        animation: 200,
        ghostClass: 'sortable-ghost',
        onEnd: function () {
            var rows = tbody.querySelectorAll('tr[data-id]');
            var ids = [];
            rows.forEach(function (row) {
                ids.push(parseInt(row.getAttribute('data-id'), 10));
            });

            fetch(REORDER_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            }).catch(function (err) {
                console.error('Reorder error:', err);
            });
        }
    });
}
