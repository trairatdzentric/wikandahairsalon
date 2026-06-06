<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Staff Dashboard</span>
            <div class="d-lg-flex justify-content-between align-items-end gap-4">
                <div>
                    <h1 class="page-title">ยินดีต้อนรับ, <?= htmlspecialchars(\App\Core\Session::get('user_name', 'ช่าง')) ?></h1>
                    <p class="page-subtitle">
                        <?php if ($staff): ?>
                            ความเชี่ยวชาญ: <?= htmlspecialchars($staff['specialty'] ?? '') ?>
                        <?php else: ?>
                            ดูคิวงานของวันนี้และสถานะบริการที่เกี่ยวข้องกับคุณ
                        <?php endif; ?>
                    </p>
                </div>
                <div class="page-actions mt-3 mt-lg-0">
                    <a href="<?= basePath() ?>/staff/bookings" class="btn btn-outline-primary">ดูคิวทั้งหมด</a>
                </div>
            </div>
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <span class="metric-label">คิววันนี้</span>
                <span class="metric-value text-primary"><?= count($bookings ?? []) ?></span>
                <span class="metric-note">รายการที่ต้องดูแล</span>
            </div>
        </div>

        <div class="content-panel">
            <div class="panel-header">
                <h2 class="panel-title">Today's Bookings</h2>
                <span class="text-muted small">คิวประจำวัน</span>
            </div>
            <?php if (!empty($bookings)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>รหัสจอง</th>
                                <th>เวลา</th>
                                <th>ราคา</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($b['booking_code'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($b['start_time'] ?? '-') ?> - <?= htmlspecialchars($b['end_time'] ?? '-') ?></td>
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
                        <i class="bi bi-calendar2-check"></i>
                        <h3 class="fw-bold">ไม่มีคิววันนี้</h3>
                        <p class="text-muted mb-0">เมื่อมีการจองใหม่ ระบบจะแสดงรายการที่นี่</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>


