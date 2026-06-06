<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Finance</span>
            <div class="d-lg-flex justify-content-between align-items-end gap-4">
                <div>
                    <h1 class="page-title">จัดการการชำระเงิน</h1>
                    <p class="page-subtitle">ตรวจรายการชำระเงิน อนุมัติ หรือปฏิเสธรายการที่รอตรวจสอบ</p>
                </div>
                <div class="page-actions mt-3 mt-lg-0">
                    <span class="badge bg-warning bg-opacity-10 text-warning"><?= count(array_filter($payments ?? [], fn($p) => ($p['status'] ?? '') === 'pending')) ?> รอตรวจสอบ</span>
                </div>
            </div>
        </div>

        <div class="content-panel">
            <div class="panel-header">
                <h2 class="panel-title">Payments</h2>
                <span class="text-muted small"><?= count($payments ?? []) ?> รายการทั้งหมด</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Booking ID</th>
                            <th>ประเภท</th>
                            <th>จำนวนเงิน</th>
                            <th>วิธีการ</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments ?? [] as $p): ?>
                            <tr>
                                <td class="fw-bold"><?= $p['id'] ?></td>
                                <td><?= $p['booking_id'] ?></td>
                                <td><span class="badge bg-light text-dark"><?= htmlspecialchars($p['payment_type'] ?? '') ?></span></td>
                                <td><?= number_format($p['amount'] ?? 0) ?> บาท</td>
                                <td><?= htmlspecialchars($p['method'] ?? '') ?></td>
                                <td><span class="badge <?= statusBadge($p['status'] ?? '') ?>"><?= htmlspecialchars($p['status'] ?? '') ?></span></td>
                                <td>
                                    <?php if (($p['status'] ?? '') === 'pending'): ?>
                                        <button class="btn btn-success btn-sm" onclick="approvePayment(<?= $p['id'] ?>)">อนุมัติ</button>
                                        <button class="btn btn-danger btn-sm" onclick="rejectPayment(<?= $p['id'] ?>)">ปฏิเสธ</button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
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
async function approvePayment(id) {
    if (!confirm('ยืนยันการอนุมัติ?')) return;
    const res = await fetch(`<?= apiPath() ?>/v1/payments/${id}/approve`, { method: 'PUT' });
    const data = await res.json();
    if (data.success) window.location.reload();
    else alert(data.message);
}

async function rejectPayment(id) {
    if (!confirm('ยืนยันการปฏิเสธ?')) return;
    const res = await fetch(`<?= apiPath() ?>/v1/payments/${id}/reject`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reason: 'ปฏิเสธโดยแอดมิน' })
    });
    const data = await res.json();
    if (data.success) window.location.reload();
    else alert(data.message);
}
</script>


