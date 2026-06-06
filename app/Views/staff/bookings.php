<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Staff Queue</span>
            <h1 class="page-title">การจองของฉัน</h1>
            <p class="page-subtitle">รายการคิวที่มอบหมายให้คุณ พร้อมวัน เวลา ราคา และสถานะล่าสุด</p>
        </div>

        <div class="content-panel">
            <div class="panel-header">
                <h2 class="panel-title">Bookings</h2>
                <span class="text-muted small"><?= count($bookings ?? []) ?> รายการ</span>
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
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($b['booking_code'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($b['booking_date'] ?? '-') ?></td>
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
                        <i class="bi bi-calendar-x"></i>
                        <h3 class="fw-bold">ไม่มีการจอง</h3>
                        <p class="text-muted mb-0">ยังไม่มีคิวที่ผูกกับบัญชีพนักงานนี้</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
