/* ============================================================
   USER FORM — Admin User Create/Edit JS
   ============================================================ */

/* -- Toggle Password Visibility -- */
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

/* -- Password Strength Indicator -- */
function checkPasswordStrength(password) {
    var strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    return strength;
}

/* -- Golden Pen Section Visibility -- */
function toggleGoldenPenSection(isYazar) {
    var section = document.getElementById('section-golden-pen');
    var navItem = document.getElementById('goldenPenNavItem');
    var mobileOption = document.getElementById('goldenPenMobileOption');

    if (section) section.style.display = isYazar ? '' : 'none';
    if (navItem) navItem.style.display = isYazar ? '' : 'none';
    if (mobileOption) mobileOption.style.display = isYazar ? '' : 'none';
}

/* -- Golden Pen Periods Management -- */
var goldenPenPeriodIndex = 0;

function initGoldenPenPeriodIndex() {
    var rows = document.querySelectorAll('.uf-period-row');
    goldenPenPeriodIndex = rows.length;
}

function addGoldenPenPeriod() {
    var container = document.getElementById('goldenPenPeriodsContainer');
    if (!container) return;

    var row = document.createElement('div');
    row.className = 'uf-period-row';
    row.setAttribute('data-period-index', goldenPenPeriodIndex);
    row.innerHTML =
        '<div class="row g-3 align-items-end">' +
            '<div class="col-md-4">' +
                '<label class="stg-label">Başlangıç <span class="text-neon-red">*</span></label>' +
                '<div class="stg-input-group">' +
                    '<span class="stg-input-prefix"><i class="bi bi-calendar-event"></i></span>' +
                    '<input type="date" class="stg-input" name="golden_pen_periods[' + goldenPenPeriodIndex + '][starts_at]" value="">' +
                '</div>' +
            '</div>' +
            '<div class="col-md-4">' +
                '<label class="stg-label">Bitiş <span class="text-neon-red">*</span></label>' +
                '<div class="stg-input-group">' +
                    '<span class="stg-input-prefix"><i class="bi bi-calendar-check"></i></span>' +
                    '<input type="date" class="stg-input" name="golden_pen_periods[' + goldenPenPeriodIndex + '][ends_at]" value="">' +
                '</div>' +
            '</div>' +
            '<div class="col-md-3">' +
                '<label class="stg-label">Not</label>' +
                '<input type="text" class="stg-input" name="golden_pen_periods[' + goldenPenPeriodIndex + '][note]" value="" placeholder="Opsiyonel not...">' +
            '</div>' +
            '<div class="col-md-1 d-flex align-items-end">' +
                '<button type="button" class="btn-glass btn-sm text-neon-red" onclick="removeGoldenPenPeriod(this)" title="Dönemi sil">' +
                    '<i class="bi bi-trash"></i>' +
                '</button>' +
            '</div>' +
        '</div>';

    container.appendChild(row);
    goldenPenPeriodIndex++;
}

function removeGoldenPenPeriod(btn) {
    var row = btn.closest('.uf-period-row');
    if (row) row.remove();
}

/* -- Role Card Click -- */
function initRoleCards() {
    var roleCards = document.querySelectorAll('.uf-role-card[data-role-id]');
    var roleSelect = document.getElementById('userRole');
    var yazarRoleId = window.yazarRoleId || '';

    function onRoleChange(roleId) {
        roleCards.forEach(function (c) { c.classList.remove('active'); });
        var selected = document.querySelector('.uf-role-card[data-role-id="' + roleId + '"]');
        if (selected) selected.classList.add('active');
        toggleGoldenPenSection(String(roleId) === String(yazarRoleId));
    }

    roleCards.forEach(function (card) {
        card.addEventListener('click', function () {
            var roleId = this.getAttribute('data-role-id');
            if (roleSelect) roleSelect.value = roleId;
            onRoleChange(roleId);
        });
    });

    if (roleSelect) {
        if (roleSelect.value) {
            var activeCard = document.querySelector('.uf-role-card[data-role-id="' + roleSelect.value + '"]');
            if (activeCard) activeCard.classList.add('active');
        }

        roleSelect.addEventListener('change', function () {
            onRoleChange(this.value);
        });
    }
}

/* -- Init -- */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    initRoleCards();
    initGoldenPenPeriodIndex();

    /* Password strength */
    var passwordInput = document.getElementById('password');
    var strengthContainer = document.getElementById('passwordStrength');
    var strengthText = document.getElementById('strengthText');

    if (passwordInput && strengthContainer) {
        passwordInput.addEventListener('input', function () {
            var val = this.value;
            if (val.length === 0) {
                strengthContainer.classList.add('d-none');
                return;
            }

            strengthContainer.classList.remove('d-none');
            var strength = checkPasswordStrength(val);
            var bars = strengthContainer.querySelectorAll('.uf-strength-bar');
            var labels = ['Çok Zayıf', 'Zayıf', 'Orta', 'Güçlü'];
            var colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];

            bars.forEach(function (bar, i) {
                if (i < strength) {
                    bar.style.background = colors[strength - 1];
                } else {
                    bar.style.background = '';
                }
            });

            if (strengthText) {
                strengthText.textContent = labels[strength - 1] || 'Çok Zayıf';
                strengthText.style.color = colors[strength - 1] || colors[0];
            }
        });
    }
});
