<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">New Booking</span>
            <h1 class="page-title">จองคิวทำผมใหม่</h1>
            <p class="page-subtitle">เลือกบริการ ช่าง วันและเวลา ระบบจะส่งข้อมูลเข้าคิวร้านทันทีหลังยืนยัน</p>
        </div>

        <div class="booking-shell">
            <aside class="booking-preview">
                <span class="page-kicker text-white">Salon Visit</span>
                <h2 class="fw-bold text-white mb-3">เตรียมลุคใหม่ของคุณให้พร้อม</h2>
                <p class="opacity-75 mb-4">เวลาทำการ 10:00 - 20:00 เลือกเวลาเป็นช่วงละ 30 นาที เพื่อให้ทีมช่างจัดคิวได้แม่นยำ</p>
                <div class="d-grid gap-3">
                    <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-scissors"></i>เลือกบริการที่ต้องการ</div>
                    <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-person-heart"></i>เลือกช่างที่เหมาะกับสไตล์</div>
                    <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-calendar2-check"></i>ยืนยันวันและเวลา</div>
                </div>
            </aside>

            <div class="content-panel">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">รายละเอียดการจอง</h2>
                        <p class="text-muted mb-0 small">กรอกข้อมูลให้ครบเพื่อสร้างคิวใหม่</p>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success">Online booking</span>
                </div>
                <div class="panel-body">
                    <form id="bookingForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">เลือกบริการ / Select Service</label>
                                <select name="service_id" class="form-select" required>
                                    <option value="">-- เลือกบริการ --</option>
                                    <?php foreach ($services ?? [] as $svc): ?>
                                        <option value="<?= $svc['id'] ?>" data-price="<?= $svc['price'] ?>" data-duration="<?= $svc['duration_minutes'] ?>">
                                            <?= htmlspecialchars($svc['name'] ?? '') ?> - <?= number_format($svc['price'] ?? 0) ?> บาท
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">เลือกช่าง / Select Stylist</label>
                                <select name="staff_id" class="form-select" required>
                                    <option value="">-- เลือกช่าง --</option>
                                    <?php foreach ($staff ?? [] as $s): ?>
                                        <option value="<?= $s['id'] ?>">
                                            <?= htmlspecialchars($s['display_name'] ?? '') ?> - <?= htmlspecialchars($s['specialty'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">วันที่ / Date</label>
                                <input type="date" name="booking_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">เวลา / Time</label>
                                <input type="time" name="start_time" class="form-control" required min="10:00" max="20:00" step="1800">
                            </div>

                            <div class="col-12">
                                <label class="form-label">หมายเหตุ / Note</label>
                                <textarea name="note" class="form-control" rows="3" placeholder="เช่น ต้องการปรึกษาสีผมก่อนทำ"></textarea>
                            </div>
                        </div>

                        <div id="bookingError" class="alert alert-danger d-none mt-3"></div>
                        <button type="submit" class="btn btn-gradient w-100 btn-lg mt-4">
                            <i class="bi bi-calendar-plus me-2"></i>จองคิว / Book Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const errorDiv = document.getElementById('bookingError');

    const res = await fetch('<?= apiPath() ?>/v1/bookings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            service_id: parseInt(form.service_id.value),
            staff_id: parseInt(form.staff_id.value),
            booking_date: form.booking_date.value,
            start_time: form.start_time.value,
            note: form.note.value
        })
    });

    const data = await res.json();
    if (data.success) {
        window.location.href = '<?= basePath() ?>/member/bookings';
    } else {
        errorDiv.textContent = data.message;
        errorDiv.classList.remove('d-none');
    }
});
</script>


