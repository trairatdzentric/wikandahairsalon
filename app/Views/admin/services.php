<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Catalog</span>
            <h1 class="page-title">จัดการบริการ</h1>
            <p class="page-subtitle">ตรวจสอบราคา ระยะเวลา หมวดหมู่ และสถานะเปิดใช้งานของแต่ละบริการ</p>
        </div>

        <div class="content-panel">
            <div class="panel-header">
                <h2 class="panel-title">Services</h2>
                <span class="text-muted small"><?= count($services ?? []) ?> บริการ</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อบริการ</th>
                            <th>ราคา</th>
                            <th>ระยะเวลา</th>
                            <th>หมวดหมู่</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services ?? [] as $s): ?>
                            <tr>
                                <td class="fw-bold"><?= $s['id'] ?></td>
                                <td><?= htmlspecialchars($s['name'] ?? '') ?></td>
                                <td><?= number_format($s['price'] ?? 0) ?> บาท</td>
                                <td><?= $s['duration_minutes'] ?? 0 ?> นาที</td>
                                <td><span class="badge bg-light text-dark"><?= htmlspecialchars($s['category'] ?? '') ?></span></td>
                                <td><span class="badge bg-<?= ($s['is_active'] ?? false) ? 'success' : 'secondary' ?>"><?= ($s['is_active'] ?? false) ? 'เปิดใช้งาน' : 'ปิดใช้งาน' ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
