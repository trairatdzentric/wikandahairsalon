<!-- Sidebar Menu / เมนูด้านซ้าย -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="<?= basePath() ?>/" class="sidebar-brand-link d-flex align-items-center text-decoration-none">
            <span class="brand-gradient fs-4 fw-bold sidebar-brand-text">W</span>
            <span class="ms-1 fs-5 fw-semibold text-white sidebar-brand-text">Hair Salon</span>
        </a>
        <div class="sidebar-brand-actions">
            <button class="sidebar-collapse-btn" onclick="toggleSidebarCollapse()" title="ย่อ/ขยายเมนู">
                <i class="bi bi-chevron-left" id="collapseIcon"></i>
            </button>
            <button class="sidebar-close d-lg-none" onclick="toggleSidebar()"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>

    <div class="sidebar-menu">
        <!-- เมนูหลัก / Main Menu -->
        <div class="sidebar-section">เมนูหลัก</div>
        <a href="<?= basePath() ?>/" class="sidebar-link <?= isActive('/') ? 'active' : '' ?>">
            <i class="bi bi-house-door"></i>
            <span>หน้าแรก</span>
        </a>
        <a href="<?= basePath() ?>/services" class="sidebar-link <?= isActive('/services') ? 'active' : '' ?>">
            <i class="bi bi-scissors"></i>
            <span>บริการ</span>
        </a>
        <a href="<?= basePath() ?>/about" class="sidebar-link <?= isActive('/about') ? 'active' : '' ?>">
            <i class="bi bi-info-circle"></i>
            <span>เกี่ยวกับเรา</span>
        </a>

        <?php if (\App\Core\Session::has('user_id')): ?>
            <?php $role = \App\Core\Session::get('user_role'); ?>
            <?php $uid = \App\Core\Session::get('user_id'); ?>

            <!-- เมนูสมาชิก / Member Menu -->
            <?php if ($role === 'member'): ?>
                <div class="sidebar-section">สมาชิก</div>
                <a href="<?= basePath() ?>/member" class="sidebar-link <?= isActive('/member') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>แดชบอร์ด</span>
                </a>
                <a href="<?= basePath() ?>/member/bookings" class="sidebar-link <?= isActive('/member/bookings') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>การจองของฉัน</span>
                </a>
                <a href="<?= basePath() ?>/member/profile" class="sidebar-link <?= isActive('/member/profile') ? 'active' : '' ?>">
                    <i class="bi bi-person"></i>
                    <span>โปรไฟล์</span>
                </a>
            <?php endif; ?>

            <!-- เมนูพนักงาน / Staff Menu -->
            <?php if ($role === 'staff'): ?>
                <div class="sidebar-section">พนักงาน</div>
                <a href="<?= basePath() ?>/staff" class="sidebar-link <?= isActive('/staff') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>แดชบอร์ด</span>
                </a>
                <a href="<?= basePath() ?>/staff/bookings" class="sidebar-link <?= isActive('/staff/bookings') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>การจองของฉัน</span>
                </a>
            <?php endif; ?>

            <!-- เมนูผู้ดูแล / Admin Menu -->
            <?php if (in_array($role, ['admin', 'owner'], true)): ?>
                <div class="sidebar-section">ผู้ดูแลระบบ</div>
                <a href="<?= basePath() ?>/admin" class="sidebar-link <?= isActive('/admin') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>แดชบอร์ด</span>
                </a>
                <a href="<?= basePath() ?>/admin/bookings" class="sidebar-link <?= isActive('/admin/bookings') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check"></i>
                    <span>การจอง</span>
                </a>
                <a href="<?= basePath() ?>/admin/payments" class="sidebar-link <?= isActive('/admin/payments') ? 'active' : '' ?>">
                    <i class="bi bi-cash-coin"></i>
                    <span>การชำระเงิน</span>
                </a>
                <a href="<?= basePath() ?>/admin/report" class="sidebar-link <?= isActive('/admin/report') ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart"></i>
                    <span>รายงาน</span>
                </a>
                <a href="<?= basePath() ?>/admin/users" class="sidebar-link <?= isActive('/admin/users') ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>ผู้ใช้งาน</span>
                </a>
                <a href="<?= basePath() ?>/admin/services" class="sidebar-link <?= isActive('/admin/services') ? 'active' : '' ?>">
                    <i class="bi bi-scissors"></i>
                    <span>บริการ</span>
                </a>
                <a href="<?= basePath() ?>/admin/staff" class="sidebar-link <?= isActive('/admin/staff') ? 'active' : '' ?>">
                    <i class="bi bi-person-badge"></i>
                    <span>ช่าง/พนักงาน</span>
                </a>
                <a href="<?= basePath() ?>/admin/settings" class="sidebar-link <?= isActive('/admin/settings') ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i>
                    <span>ตั้งค่าระบบ</span>
                </a>            <?php endif; ?>

            <!-- เมนูบัญชี / Account Menu -->
            <div class="sidebar-section">บัญชี</div>
            <a href="<?= basePath() ?>/logout" class="sidebar-link text-danger">
                <i class="bi bi-box-arrow-right"></i>
                <span>ออกจากระบบ</span>
            </a>
        <?php else: ?>
            <!-- เมนูผู้เยี่ยมชม / Guest Menu -->
            <div class="sidebar-section">บัญชี</div>
            <a href="<?= basePath() ?>/login" class="sidebar-link <?= isActive('/login') ? 'active' : '' ?>">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>เข้าสู่ระบบ</span>
            </a>
            <a href="<?= basePath() ?>/register" class="sidebar-link <?= isActive('/register') ? 'active' : '' ?>">
                <i class="bi bi-person-plus"></i>
                <span>ลงทะเบียน</span>
            </a>
        <?php endif; ?>
    </div>
</nav>

<!-- Overlay สำหรับมือถือ / Mobile overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>


