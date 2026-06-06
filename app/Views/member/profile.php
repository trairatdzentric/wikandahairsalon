<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">My Profile</span>
            <h1 class="page-title">โปรไฟล์ของฉัน</h1>
            <p class="page-subtitle">อัปเดตข้อมูลติดต่อและรหัสผ่านเพื่อให้ร้านติดต่อกลับได้ถูกต้อง</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="content-panel">
                    <div class="panel-header">
                        <h2 class="panel-title">ข้อมูลบัญชี</h2>
                        <span class="badge bg-primary bg-opacity-10 text-primary">Member</span>
                    </div>
                    <div class="panel-body">
                        <?php if ($user): ?>
                            <form id="profileForm">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อผู้ใช้ / Username</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">อีเมล / Email</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ-นามสกุล / Full Name</label>
                                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">เบอร์โทรศัพท์ / Phone</label>
                                        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">รหัสผ่านใหม่</label>
                                        <input type="password" name="password" class="form-control" placeholder="เว้นว่างถ้าไม่ต้องการเปลี่ยน">
                                    </div>
                                </div>
                                <div id="profileError" class="alert alert-danger d-none mt-3"></div>
                                <div id="profileSuccess" class="alert alert-success d-none mt-3"></div>
                                <button type="submit" class="btn btn-gradient w-100 mt-4">
                                    <i class="bi bi-save me-2"></i>บันทึกการเปลี่ยนแปลง
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-person-x"></i>
                                <h3 class="fw-bold">ไม่พบข้อมูลผู้ใช้</h3>
                                <p class="text-muted mb-0">กรุณาเข้าสู่ระบบอีกครั้ง</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('profileForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const errorDiv = document.getElementById('profileError');
    const successDiv = document.getElementById('profileSuccess');

    const payload = {
        email: form.email.value,
        full_name: form.full_name.value,
        phone: form.phone.value,
    };
    if (form.password.value) {
        payload.password = form.password.value;
    }

    const res = await fetch(`<?= apiPath() ?>/v1/users/${form.id.value}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (data.success) {
        successDiv.textContent = 'บันทึกสำเร็จ';
        successDiv.classList.remove('d-none');
        errorDiv.classList.add('d-none');
    } else {
        errorDiv.textContent = data.message;
        errorDiv.classList.remove('d-none');
        successDiv.classList.add('d-none');
    }
});
</script>


