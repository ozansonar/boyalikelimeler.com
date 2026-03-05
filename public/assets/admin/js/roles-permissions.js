'use strict';

// ========== Role Selection ==========
function selectRole(roleId, el) {
    document.querySelectorAll('.rp-role-card').forEach(function (c) {
        c.classList.remove('active');
    });
    el.classList.add('active');
}

// ========== Permission Matrix Filter ==========
function filterMatrix(cat) {
    var rows = document.querySelectorAll('.rp-matrix-table tbody tr');
    rows.forEach(function (row) {
        if (cat === 'all') {
            row.classList.remove('d-none');
        } else {
            row.classList.toggle('d-none', row.dataset.cat !== cat);
        }
    });
}

// ========== Matrix Checkbox Toggle ==========
document.querySelectorAll('.rp-check input[type="checkbox"]:not([disabled])').forEach(function (cb) {
    cb.addEventListener('change', function () {
        var label = this.closest('.rp-check');
        if (this.checked) {
            label.classList.add('granted');
        } else {
            label.classList.remove('granted');
        }
    });
});

// ========== Save Permissions (AJAX) ==========
function savePermissions() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var rolePermissions = {};

    document.querySelectorAll('.rp-matrix-table tbody input[type="checkbox"]').forEach(function (cb) {
        var roleId = cb.dataset.roleId;
        var permId = cb.dataset.permissionId;

        if (!roleId || !permId) return;

        if (!rolePermissions[roleId]) {
            rolePermissions[roleId] = [];
        }

        if (cb.checked) {
            rolePermissions[roleId].push(parseInt(permId, 10));
        }
    });

    var roleIds = Object.keys(rolePermissions);
    var completed = 0;
    var failed = false;

    roleIds.forEach(function (roleId) {
        fetch(ROLE_UPDATE_BASE_URL + '/' + roleId + '/permissions', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ permissions: rolePermissions[roleId] })
        })
        .then(function (response) {
            if (!response.ok) throw new Error('Hata');
            return response.json();
        })
        .then(function () {
            completed++;
            if (completed === roleIds.length && !failed) {
                showRpToast('İzin matrisi başarıyla kaydedildi');
            }
        })
        .catch(function () {
            failed = true;
            showRpToast('İzinler kaydedilirken hata oluştu', 'danger');
        });
    });
}

// ========== Edit Role Modal ==========
function openEditModal(roleId, roleName) {
    var form = document.getElementById('editRoleForm');
    var nameInput = document.getElementById('editRoleName');

    form.action = ROLE_UPDATE_BASE_URL + '/' + roleId;
    nameInput.value = roleName;

    new bootstrap.Modal(document.getElementById('editRoleModal')).show();
}

// ========== Delete Role Modal ==========
function openDeleteModal(roleId) {
    var form = document.getElementById('deleteRoleForm');
    form.action = ROLE_UPDATE_BASE_URL + '/' + roleId;

    new bootstrap.Modal(document.getElementById('deleteRoleModal')).show();
}

// ========== Copy From Role (New Role Modal) ==========
(function () {
    var copySelect = document.getElementById('copyFromRole');
    if (!copySelect) return;

    copySelect.addEventListener('change', function () {
        var roleId = parseInt(this.value, 10);

        document.querySelectorAll('#roleModal .rp-perm-checkbox').forEach(function (cb) {
            cb.checked = false;
        });

        document.querySelectorAll('#roleModal .rp-group-toggle').forEach(function (cb) {
            cb.checked = false;
        });

        if (!roleId || !ROLES_DATA[roleId]) return;

        var permIds = ROLES_DATA[roleId];
        document.querySelectorAll('#roleModal .rp-perm-checkbox').forEach(function (cb) {
            if (permIds.indexOf(parseInt(cb.value, 10)) !== -1) {
                cb.checked = true;
            }
        });

        updateGroupToggles();
    });
})();

// ========== Group Toggle (select/deselect all in group) ==========
document.querySelectorAll('.rp-group-toggle').forEach(function (toggle) {
    toggle.addEventListener('change', function () {
        var group = this.dataset.group;
        var checked = this.checked;
        document.querySelectorAll('.rp-perm-checkbox[data-group="' + group + '"]').forEach(function (cb) {
            cb.checked = checked;
        });
    });
});

document.querySelectorAll('.rp-perm-checkbox').forEach(function (cb) {
    cb.addEventListener('change', function () {
        updateGroupToggles();
    });
});

function updateGroupToggles() {
    document.querySelectorAll('.rp-group-toggle').forEach(function (toggle) {
        var group = toggle.dataset.group;
        var checkboxes = document.querySelectorAll('.rp-perm-checkbox[data-group="' + group + '"]');
        var allChecked = true;
        checkboxes.forEach(function (cb) {
            if (!cb.checked) allChecked = false;
        });
        toggle.checked = allChecked;
    });
}

// ========== Toast ==========
function showRpToast(message, type) {
    var toast = document.getElementById('rpToast');
    var body = document.getElementById('rpToastBody');
    var icons = { danger: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', default: 'bi-check-circle-fill' };
    var colors = { danger: 'var(--neon-red, #ef4444)', warning: 'var(--neon-orange)', default: 'var(--neon-green)' };

    body.innerHTML = '<i class="bi ' + (icons[type] || icons.default) + '" style="color:' + (colors[type] || colors.default) + '"></i><span>' + message + '</span>';
    new bootstrap.Toast(toast, { delay: 3000 }).show();
}
