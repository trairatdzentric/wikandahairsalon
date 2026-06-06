<!-- แดชบอร์ดผู้ดูแล / Admin Dashboard -->
<section class="py-4">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">แดชบอร์ดสรุป</h4>
                <p class="text-muted mb-0">ภาพรวมการทำงานของร้านวันนี้</p>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i> เปิดให้บริการ
                </span>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h6 class="stat-label">การจองวันนี้</h6>
                        <h3 class="stat-value"><?= $summary['bookings']['total'] ?? 0 ?></h3>
                        <span class="stat-badge text-success">
                            <i class="bi bi-arrow-up"></i> จองใหม่
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-info">
                        <h6 class="stat-label">รอดำเนินการ</h6>
                        <h3 class="stat-value"><?= $summary['bookings']['pending'] ?? 0 ?></h3>
                        <span class="stat-badge text-warning">
                            <i class="bi bi-hourglass"></i> รอยืนยัน
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="stat-info">
                        <h6 class="stat-label">รายได้วันนี้</h6>
                        <h3 class="stat-value"><?= number_format($summary['revenue'] ?? 0) ?> <small>บาท</small></h3>
                        <span class="stat-badge text-success">
                            <i class="bi bi-graph-up"></i> รายได้
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <div class="stat-info">
                        <h6 class="stat-label">การชำระเงิน</h6>
                        <h3 class="stat-value"><?= $summary['payments'] ?? 0 ?></h3>
                        <span class="stat-badge text-info">
                            <i class="bi bi-check2-all"></i> รอตรวจสอบ
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card card-soft">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">เมนูด่วน / Quick Links</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="<?= basePath() ?>/admin/users" class="quick-link-card">
                                    <div class="quick-icon bg-indigo bg-opacity-10 text-indigo">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <span>จัดการผู้ใช้งาน</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= basePath() ?>/admin/services" class="quick-link-card">
                                    <div class="quick-icon bg-pink bg-opacity-10 text-pink">
                                        <i class="bi bi-scissors"></i>
                                    </div>
                                    <span>จัดการบริการ</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= basePath() ?>/admin/staff" class="quick-link-card">
                                    <div class="quick-icon bg-purple bg-opacity-10 text-purple">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <span>จัดการช่าง</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= basePath() ?>/admin/bookings" class="quick-link-card">
                                    <div class="quick-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-calendar3"></i>
                                    </div>
                                    <span>จัดการการจอง</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= basePath() ?>/admin/payments" class="quick-link-card">
                                    <div class="quick-icon bg-success bg-opacity-10 text-success">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <span>จัดการการชำระเงิน</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= basePath() ?>/admin/report" class="quick-link-card">
                                    <div class="quick-icon bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-bar-chart-line"></i>
                                    </div>
                                    <span>รายงาน</span>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?= basePath() ?>/admin/settings" class="quick-link-card">
                                    <div class="quick-icon bg-info bg-opacity-10 text-info">
                                        <i class="bi bi-gear"></i>
                                    </div>
                                    <span>ตั้งค่าการเชื่อมต่อ</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-soft">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">สถานะระบบ</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="system-status-item">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="status-dot bg-success"></div>
                                <div>
                                    <div class="fw-medium">ระบบทำงานปกติ</div>
                                    <small class="text-muted">Server Online</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="status-dot bg-primary"></div>
                                <div>
                                    <div class="fw-medium">ฐานข้อมูล JSON</div>
                                    <small class="text-muted">6 ตารางพร้อมใช้งาน</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="status-dot bg-warning"></div>
                                <div>
                                    <div class="fw-medium">LINE Notify</div>
                                    <small class="text-muted">รอการตั้งค่า Token</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


