/* ============================================================
   USERS LIST — Admin User Management JS
   ============================================================ */

function changePerPage(value) {
    var url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
