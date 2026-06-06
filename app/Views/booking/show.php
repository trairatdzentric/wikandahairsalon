<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Booking Details</span>
            <h1 class="page-title">รายละเอียดการจอง</h1>
            <p class="page-subtitle">ตรวจสอบข้อมูลวัน เวลา ราคา และสถานะล่าสุดของคิว</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="content-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">ข้อมูลคิว</h2>
                        <?php if ($booking): ?>
                            <span class="badge <?= statusBadge($booking['status'] ?? '') ?>"><?= htmlspecialchars($booking['status'] ?? '-') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="panel-body">
                        <?php if ($booking): ?>
                            <div class="detail-list">
                                <div class="detail-row">
                                    <span class="detail-label">รหัสจอง</span>
                                    <span class="detail-value"><?= htmlspecialchars($booking['booking_code'] ?? '-') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">วันที่</span>
                                    <span class="detail-value"><?= htmlspecialchars($booking['booking_date'] ?? '-') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">เวลา</span>
                                    <span class="detail-value"><?= htmlspecialchars($booking['start_time'] ?? '-') ?> - <?= htmlspecialchars($booking['end_time'] ?? '-') ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">ราคา</span>
                                    <span class="detail-value"><?= number_format($booking['total_price'] ?? 0) ?> บาท</span>
                                </div>
                                <?php if (!empty($booking['note'])): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">หมายเหตุ</span>
                                        <span class="detail-value"><?= htmlspecialchars($booking['note']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a href="<?= basePath() ?>/member/bookings" class="btn btn-outline-primary mt-4">
                                <i class="bi bi-arrow-left me-2"></i>กลับ
                            </a>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-calendar-x"></i>
                                <h3 class="fw-bold">ไม่พบการจอง</h3>
                                <p class="text-muted mb-0">รายการนี้อาจถูกลบหรือไม่มีสิทธิ์เข้าถึง</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


