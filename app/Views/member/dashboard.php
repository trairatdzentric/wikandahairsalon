<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Member Dashboard</span>
            <div class="d-lg-flex justify-content-between align-items-end gap-4">
                <div>
                    <h1 class="page-title">ยินดีต้อนรับ, <?= htmlspecialchars(\App\Core\Session::get('user_name', 'สมาชิก')) ?></h1>
                    <p class="page-subtitle">ดูภาพรวมการจองของคุณและเริ่มจองคิวครั้งต่อไปได้ทันที</p>
                </div>
                <div class="page-actions mt-3 mt-lg-0">
                    <a href="<?= basePath() ?>/booking/new" class="btn btn-gradient">
                        <i class="bi bi-calendar-plus me-2"></i>จองคิวใหม่
                    </a>
                </div>
            </div>
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <span class="metric-label">การจองทั้งหมด</span>
                <span class="metric-value text-primary"><?= count($bookings ?? []) ?></span>
                <span class="metric-note">รายการทั้งหมด</span>
            </div>
            <div class="metric-card">
                <span class="metric-label">รอดำเนินการ</span>
                <span class="metric-value text-warning"><?= count(array_filter($bookings ?? [], fn($b) => ($b['status'] ?? '') === 'pending')) ?></span>
                <span class="metric-note">รอยืนยันจากร้าน</span>
            </div>
            <div class="metric-card">
                <span class="metric-label">เสร็จสิ้น</span>
                <span class="metric-value text-success"><?= count(array_filter($bookings ?? [], fn($b) => ($b['status'] ?? '') === 'completed')) ?></span>
                <span class="metric-note">บริการสำเร็จแล้ว</span>
            </div>
        </div>

        <div class="content-panel">
            <div class="panel-header">
                <h2 class="panel-title">การจองล่าสุด</h2>
                <a href="<?= basePath() ?>/member/bookings" class="btn btn-outline-primary btn-sm">ดูทั้งหมด</a>
            </div>
            <?php if (!empty($bookings)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>รหัสจอง</th>
                                <th>วันที่</th>
                                <th>เวลา</th>
                                <th>ราคา</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($bookings, 0, 5) as $b): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($b['booking_code'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($b['booking_date'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($b['start_time'] ?? '-') ?></td>
                                    <td><?= number_format($b['total_price'] ?? 0) ?> บาท</td>
                                    <td><span class="badge <?= statusBadge($b['status'] ?? '') ?>"><?= htmlspecialchars($b['status'] ?? '-') ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="panel-body">
                    <div class="empty-state">
                        <i class="bi bi-calendar-plus"></i>
                        <h3 class="fw-bold">ยังไม่มีการจอง</h3>
                        <p class="text-muted">เริ่มจองคิวแรกของคุณกับ Wikanda Hair Salon</p>
                        <a href="<?= basePath() ?>/booking/new" class="btn btn-gradient">จองคิวเลย</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>


