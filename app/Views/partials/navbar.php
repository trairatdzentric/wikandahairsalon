<nav class="navbar navbar-expand-lg navbar-glass d-lg-none">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= url() ?>">
            <span class="brand-gradient">Wikanda</span> Hair Salon
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url() ?>">หน้าแรก</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('services') ?>">บริการ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('about') ?>">เกี่ยวกับเรา</a>
                </li>

                <?php if (\App\Core\Session::has('user_id')): ?>
                    <?php $role = \App\Core\Session::get('user_role'); ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <?= htmlspecialchars(\App\Core\Session::get('user_name', 'ผู้ใช้')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($role === 'member'): ?>
                                <li><a class="dropdown-item" href="<?= url('member') ?>">แดชบอร์ด</a></li>
                                <li><a class="dropdown-item" href="<?= url('member/bookings') ?>">การจองของฉัน</a></li>
                                <li><a class="dropdown-item" href="<?= url('member/profile') ?>">โปรไฟล์</a></li>
                            <?php elseif ($role === 'staff'): ?>
                                <li><a class="dropdown-item" href="<?= url('staff') ?>">แดชบอร์ด</a></li>
                                <li><a class="dropdown-item" href="<?= url('staff/bookings') ?>">การจองของฉัน</a></li>
                            <?php elseif (in_array($role, ['admin', 'owner'], true)): ?>
                                <li><a class="dropdown-item" href="<?= url('admin') ?>">แดชบอร์ด</a></li>
                                <li><a class="dropdown-item" href="<?= url('admin/bookings') ?>">การจอง</a></li>
                                <li><a class="dropdown-item" href="<?= url('admin/payments') ?>">การชำระเงิน</a></li>
                                <li><a class="dropdown-item" href="<?= url('admin/report') ?>">รายงาน</a></li>
                            <?php endif; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= url('logout') ?>">ออกจากระบบ</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('login') ?>">เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-gradient ms-2" href="<?= url('register') ?>">ลงทะเบียน</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>