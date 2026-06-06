<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Operations</span>
            <div class="d-lg-flex justify-content-between align-items-end gap-4">
                <div>
                    <h1 class="page-title">จัดการการจอง</h1>
                    <p class="page-subtitle">อัปเดตสถานะคิวและติดตามงานบริการของร้านแบบรวดเร็ว</p>
                </div>
                <div class="page-actions mt-3 mt-lg-0">
                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= count($bookings ?? []) ?> รายการ</span>
                </div>
            </div>
        </div>

        <div class="content-panel">
            <div class="panel-header">
                <h2 class="panel-title">Bookings</h2>
                <span class="text-muted small">เลือกสถานะจาก dropdown เพื่ออัปเดตทันที</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>รหัสจอง</th>
                            <th>วันที่</th>
                            <th>เวลา</th>
                            <th>ราคา</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings ?? [] as $b): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($b['booking_code'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($b['booking_date'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($b['start_time'] ?? '-') ?> - <?= htmlspecialchars($b['end_time'] ?? '-') ?></td>
                                <td><?= number_format($b['total_price'] ?? 0) ?> บาท</td>
                                <td><span class="badge <?= statusBadge($b['status'] ?? '') ?>"><?= htmlspecialchars($b['status'] ?? '-') ?></span></td>
                                <td>
                                    <select class="form-select form-select-sm" onchange="updateStatus(<?= $b['id'] ?>, this.value)">
                                        <option value="pending" <?= ($b['status'] ?? '') === 'pending' ? 'selected' : '' ?>>รอดำเนินการ</option>
                                        <option value="confirmed" <?= ($b['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>ยืนยันแล้ว</option>
                                        <option value="in_service" <?= ($b['status'] ?? '') === 'in_service' ? 'selected' : '' ?>>กำลังให้บริการ</option>
                                        <option value="completed" <?= ($b['status'] ?? '') === 'completed' ? 'selected' : '' ?>>เสร็จสิ้น</option>
                                        <option value="cancelled" <?= ($b['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>ยกเลิก</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
async function updateStatus(id, status) {
    const res = await fetch(`<?= apiPath() ?>/v1/bookings/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status })
    });

    const data = await res.json();
    if (data.success) {
        window.location.reload();
    } else {
        alert(data.message);
    }
}
</script>


