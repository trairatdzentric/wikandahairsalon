<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Reports</span>
            <h1 class="page-title">รายงานภาพรวม</h1>
            <p class="page-subtitle">ติดตามรายได้และบริการยอดนิยมสำหรับตัดสินใจบริหารร้าน</p>
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <span class="metric-label">รายได้วันนี้</span>
                <span class="metric-value text-success"><?= number_format($todayRevenue['total'] ?? 0) ?></span>
                <span class="metric-note"><?= $todayRevenue['count'] ?? 0 ?> รายการ / บาท</span>
            </div>
            <div class="metric-card">
                <span class="metric-label">รายได้เดือนนี้</span>
                <span class="metric-value text-success"><?= number_format($monthRevenue['total'] ?? 0) ?></span>
                <span class="metric-note"><?= $monthRevenue['count'] ?? 0 ?> รายการ / บาท</span>
            </div>
        </div>

        <div class="content-panel">
            <div class="panel-header">
                <h2 class="panel-title">Top Services</h2>
                <span class="text-muted small">บริการที่ถูกจองมากที่สุด</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>อันดับ</th>
                            <th>บริการ</th>
                            <th>จำนวนการจอง</th>
                            <th>รายได้รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topServices ?? [] as $i => $svc): ?>
                            <tr>
                                <td class="fw-bold"><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($svc['name'] ?? '') ?></td>
                                <td><?= $svc['count'] ?? 0 ?></td>
                                <td><?= number_format($svc['revenue'] ?? 0) ?> บาท</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
