<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Stylists</span>
            <h1 class="page-title">จัดการช่างและพนักงาน</h1>
            <p class="page-subtitle">ดูความเชี่ยวชาญ วันทำงาน และสถานะของทีมบริการหน้าร้าน</p>
        </div>

        <div class="row g-4">
            <?php foreach ($staff ?? [] as $s): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-soft booking-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($s['display_name'] ?? '') ?></h5>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($s['specialty'] ?? '') ?></p>
                                </div>
                                <span class="badge bg-<?= ($s['is_active'] ?? false) ? 'success' : 'secondary' ?>"><?= ($s['is_active'] ?? false) ? 'เปิดใช้งาน' : 'ปิดใช้งาน' ?></span>
                            </div>
                            <div class="detail-list">
                                <div class="detail-row">
                                    <span class="detail-label">ประสบการณ์</span>
                                    <span class="detail-value"><?= $s['experience_years'] ?? 0 ?> ปี</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">วันทำงาน</span>
                                    <span class="detail-value"><?= implode(', ', $s['working_days'] ?? []) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">เวลา</span>
                                    <span class="detail-value"><?= $s['working_hours']['start'] ?? '10:00' ?> - <?= $s['working_hours']['end'] ?? '20:00' ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
