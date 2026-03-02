/* ============================================================
   USERS LIST — Admin User Management JS
   ============================================================ */

function openDeleteModal(userId, userName) {
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteForm').action = '/admin/users/' + userId;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function changePerPage(value) {
    var url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

