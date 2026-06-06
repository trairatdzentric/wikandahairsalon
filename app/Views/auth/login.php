<section class="auth-shell py-4">
    <div class="container">
        <div class="auth-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="auth-aside">
                        <span class="page-kicker text-white">Member Access</span>
                        <h1 class="fw-bold text-white mb-3">เข้าสู่ระบบจัดการคิวของคุณ</h1>
                        <p class="mb-4 opacity-75">ดูรายการจองล่าสุด ตรวจสถานะ และจัดการโปรไฟล์จากที่เดียว</p>
                        <div class="d-grid gap-3">
                            <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-calendar-check"></i>ติดตามสถานะการจอง</div>
                            <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-shield-check"></i>ข้อมูลปลอดภัยสำหรับสมาชิก</div>
                            <div class="service-pill bg-white bg-opacity-10 text-white"><i class="bi bi-stars"></i>จองบริการครั้งถัดไปเร็วขึ้น</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="auth-form">
                        <span class="page-kicker">Sign In</span>
                        <h2 class="fw-bold mb-1">เข้าสู่ระบบ</h2>
                        <p class="text-muted mb-4">กรอกอีเมลหรือชื่อผู้ใช้เพื่อเข้าใช้งาน</p>

                        <form id="loginForm">
                            <div class="mb-3">
                                <label class="form-label">อีเมลหรือชื่อผู้ใช้ / Email or Username</label>
                                <input type="text" name="email" class="form-control form-control-lg" required autocomplete="username">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">รหัสผ่าน / Password</label>
                                <input type="password" name="password" class="form-control form-control-lg" required autocomplete="current-password">
                            </div>
                            <div id="loginError" class="alert alert-danger d-none"></div>
                            <button type="submit" class="btn btn-gradient w-100 btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>เข้าสู่ระบบ
                            </button>
                        </form>

                        <p class="text-center text-muted mt-4 mb-0">
                            ยังไม่มีบัญชี? <a href="<?= basePath() ?>/register">ลงทะเบียน</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const errorDiv = document.getElementById('loginError');

    const res = await fetch('<?= apiPath() ?>/v1/auth', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'login',
            email: form.email.value,
            password: form.password.value
        })
    });

    const data = await res.json();
    if (data.success) {
        window.location.href = '<?= basePath() ?>/';
    } else {
        errorDiv.textContent = data.message;
        errorDiv.classList.remove('d-none');
    }
});
</script>


