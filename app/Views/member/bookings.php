<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">My Bookings</span>
            <div class="d-lg-flex justify-content-between align-items-end gap-4">
                <div>
                    <h1 class="page-title">การจองของฉัน</h1>
                    <p class="page-subtitle">ดูรายละเอียดคิว ราคา เวลา และยกเลิกคิวที่ยังอยู่ในช่วงอนุญาต</p>
                </div>
                <div class="page-actions mt-3 mt-lg-0">
                    <a href="<?= basePath() ?>/booking/new" class="btn btn-gradient">
                        <i class="bi bi-calendar-plus me-2"></i>จองคิวใหม่
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($bookings)): ?>
            <div class="row g-4">
                <?php foreach ($bookings as $b): ?>
                    <div class="col-lg-6">
                        <div class="card card-soft booking-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($b['booking_code'] ?? '-') ?></h5>
                                    <span class="badge <?= statusBadge($b['status'] ?? '') ?>"><?= htmlspecialchars($b['status'] ?? '-') ?></span>
                                </div>
                                <div class="detail-list">
                                    <div class="detail-row"><span class="detail-label">วันที่</span><span class="detail-value"><?= htmlspecialchars($b['booking_date'] ?? '-') ?></span></div>
                                    <div class="detail-row"><span class="detail-label">เวลา</span><span class="detail-value"><?= htmlspecialchars($b['start_time'] ?? '-') ?> - <?= htmlspecialchars($b['end_time'] ?? '-') ?></span></div>
                                    <div class="detail-row"><span class="detail-label">ราคา</span><span class="detail-value"><?= number_format($b['total_price'] ?? 0) ?> บาท</span></div>
                                </div>
                                <?php if (!empty($b['note'])): ?>
                                    <p class="text-muted small mt-3 mb-0"><?= htmlspecialchars($b['note']) ?></p>
                                <?php endif; ?>

                                <?php if (in_array($b['status'] ?? '', ['pending', 'confirmed'], true)): ?>
                                    <button class="btn btn-outline-danger btn-sm mt-3" onclick="cancelBooking(<?= $b['id'] ?>)">
                                        <i class="bi bi-x-circle me-1"></i>ยกเลิกการจอง
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <h3 class="fw-bold">ยังไม่มีการจอง</h3>
                <p class="text-muted">เลือกบริการและเวลาที่สะดวกเพื่อเริ่มจองคิว</p>
                <a href="<?= basePath() ?>/booking/new" class="btn btn-gradient">จองคิวเลย</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
async function cancelBooking(id) {
    if (!confirm('ยืนยันการยกเลิกการจอง?')) return;

    const res = await fetch(`<?= apiPath() ?>/v1/bookings/${id}/cancel`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reason: 'ยกเลิกโดยผู้ใช้' })
    });

    const data = await res.json();
    if (data.success) {
        window.location.reload();
    } else {
        alert(data.message);
    }
}
</script>


