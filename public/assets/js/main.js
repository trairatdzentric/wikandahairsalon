/**
 * Main Application JavaScript
 * UX helpers, API wrapper, form validation, date guard, and layout controls.
 */

async function api(endpoint, method = 'GET', body = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    };

    if (body && ['POST', 'PUT', 'PATCH'].includes(method)) {
        options.body = JSON.stringify(body);
    }

    const response = await fetch(endpoint, options);
    const payload = await response.json().catch(() => ({
        success: false,
        message: 'ไม่สามารถอ่านผลลัพธ์จากเซิร์ฟเวอร์ได้'
    }));

    if (!response.ok && payload.success !== false) {
        payload.success = false;
        payload.message = payload.message || 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
    }

    return payload;
}

function flash(message, type = 'success') {
    const alertType = type === 'error' ? 'danger' : type;
    const container = document.getElementById('flash-container') || document.body;
    const alert = document.createElement('div');
    alert.className = `alert alert-${alertType} alert-dismissible fade show position-fixed shadow`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: min(360px, calc(100vw - 32px));';
    alert.innerHTML = `
        <div class="pe-3">${message}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    container.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}

function confirmAction(message = 'ยืนยันการทำรายการนี้?') {
    return window.confirm(message);
}

function confirmDelete(message = 'ยืนยันการลบรายการนี้?') {
    return confirmAction(message);
}

function todayIso() {
    const now = new Date();
    const local = new Date(now.getTime() - (now.getTimezoneOffset() * 60000));
    return local.toISOString().slice(0, 10);
}

function setBookingDateDefaults() {
    document.querySelectorAll('input[type="date"]').forEach((input) => {
        if (!input.min) {
            input.min = todayIso();
        }
    });
}

function validateBookingTime(form) {
    const timeInput = form.querySelector('input[name="booking_time"], input[type="time"]');
    if (!timeInput || !timeInput.value) return true;

    if (timeInput.value < '10:00' || timeInput.value > '20:00') {
        timeInput.setCustomValidity('กรุณาเลือกเวลาระหว่าง 10:00 - 20:00');
        timeInput.reportValidity();
        flash('กรุณาเลือกเวลาจองระหว่าง 10:00 - 20:00', 'warning');
        return false;
    }

    timeInput.setCustomValidity('');
    return true;
}

function wireFormValidation() {
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!validateBookingTime(form) || !form.checkValidity()) {
                event.preventDefault();
                event.stopImmediatePropagation();
                form.classList.add('was-validated');
                return;
            }

            form.classList.add('was-validated');
        }, true);

        form.querySelectorAll('input, select, textarea').forEach((field) => {
            field.addEventListener('input', () => {
                field.setCustomValidity('');
            });
        });
    });
}

function wireDropdownClose() {
    document.addEventListener('click', (event) => {
        document.querySelectorAll('.dropdown-menu.show').forEach((menu) => {
            if (!menu.closest('.dropdown')?.contains(event.target)) {
                menu.classList.remove('show');
            }
        });
    });
}

function applySidebarState(isCollapsed) {
    const sidebar = document.getElementById('sidebar');
    const pageWrapper = document.querySelector('.page-wrapper');
    const navbar = document.querySelector('.navbar-glass');
    const footer = document.querySelector('.footer');

    if (!sidebar) return;

    sidebar.classList.toggle('collapsed', isCollapsed);
    if (pageWrapper) pageWrapper.classList.toggle('collapsed', isCollapsed);
    if (navbar) navbar.classList.toggle('collapsed', isCollapsed);
    if (footer) footer.classList.toggle('collapsed', isCollapsed);
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebar && overlay) {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }
}

function toggleSidebarCollapse() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    const isCollapsed = !sidebar.classList.contains('collapsed');
    applySidebarState(isCollapsed);
    localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
}

document.addEventListener('DOMContentLoaded', () => {
    setBookingDateDefaults();
    wireFormValidation();
    wireDropdownClose();
    applySidebarState(localStorage.getItem('sidebarCollapsed') === '1');
});
