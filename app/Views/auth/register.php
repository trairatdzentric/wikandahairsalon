<section class="auth-shell py-4">
    <div class="container">
        <div class="auth-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="auth-aside">
                        <span class="page-kicker text-white">Join Wikanda</span>
                        <h1 class="fw-bold text-white mb-3">สมัครสมาชิกเพื่อจองคิวได้เร็วขึ้น</h1>
                        <p class="mb-4 opacity-75">เก็บข้อมูลติดต่อไว้ครั้งเดียว แล้วจัดการการจองครั้งต่อไปได้สะดวกกว่าเดิม</p>
                        <div class="d-grid gap-3">
                            <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-person-check"></i>โปรไฟล์ลูกค้า</div>
                            <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-clock-history"></i>ประวัติการจอง</div>
                            <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-gem"></i>บริการที่เหมาะกับคุณ</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="auth-form">
                        <span class="page-kicker">Create Account</span>
                        <h2 class="fw-bold mb-1">ลงทะเบียน</h2>
                        <p class="text-muted mb-4">สร้างบัญชีสมาชิกใหม่สำหรับ Wikanda Hair Salon</p>

                        <form id="registerForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อผู้ใช้ / Username</label>
                                    <input type="text" name="username" class="form-control" required autocomplete="username">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">อีเมล / Email</label>
                                    <input type="email" name="email" class="form-control" required autocomplete="email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">รหัสผ่าน / Password</label>
                                    <input type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">เบอร์โทรศัพท์ / Phone</label>
                                    <input type="tel" name="phone" class="form-control" required autocomplete="tel">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">ชื่อ-นามสกุล / Full Name</label>
                                    <input type="text" name="full_name" class="form-control" required autocomplete="name">
                                </div>
                            </div>
                            <div id="registerError" class="alert alert-danger d-none mt-3"></div>
                            <button type="submit" class="btn btn-gradient w-100 btn-lg mt-4">
                                <i class="bi bi-person-plus me-2"></i>ลงทะเบียน
                            </button>
                        </form>

                        <p class="text-center text-muted mt-4 mb-0">
                            มีบัญชีแล้ว? <a href="<?= basePath() ?>/login">เข้าสู่ระบบ</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const errorDiv = document.getElementById('registerError');

    const res = await fetch('<?= apiPath() ?>/v1/auth', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            username: form.username.value,
            email: form.email.value,
            password: form.password.value,
            full_name: form.full_name.value,
            phone: form.phone.value,
            role: 'member'
        })
    });

    const data = await res.json();
    if (data.success) {
        window.location.href = '<?= basePath() ?>/login';
    } else {
        errorDiv.textContent = data.message;
        errorDiv.classList.remove('d-none');
    }
});
</script>


