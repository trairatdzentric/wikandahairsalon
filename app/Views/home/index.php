<!-- หน้าแรก / Homepage -->
<section class="hero">
    <div class="hero-pattern"></div>
    <div class="container text-center position-relative">
        <div class="hero-badge mb-3">
            <span>✨ ร้านทำผมคุณภาพอันดับ 1 ในเชียงใหม่</span>
        </div>
        <h1 class="display-3 fw-bold mb-3">
            ยินดีต้อนรับสู่ <span class="brand-gradient">Wikanda</span> Hair Salon
        </h1>
        <p class="lead text-muted mb-4 hero-subtitle">
            บริการทำผมคุณภาพ ด้วยทีมช่างมืออาชีพ<br>
            <span class="text-secondary">Quality hair services by professional stylists</span>
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?= basePath() ?>/booking/new" class="btn btn-gradient btn-lg px-4">
                <i class="bi bi-calendar-plus me-2"></i>จองคิวเลย
            </a>
            <a href="<?= basePath() ?>/services" class="btn btn-outline-light btn-lg px-4">
                <i class="bi bi-scissors me-2"></i>ดูบริการ
            </a>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge">Our Services</span>
            <h2 class="fw-bold mt-2">บริการยอดนิยม</h2>
            <p class="text-muted">เลือกบริการที่ตรงกับความต้องการของคุณ</p>
        </div>
        <div class="row g-4">
            <?php 
            $icons = ['bi-scissors', 'bi-brush', 'bi-droplet', 'bi-palette', 'bi-stars', 'bi-heart'];
            $colors = ['#FF6B9D', '#C8A8FF', '#FFB6D5', '#9B6BFF', '#FF8FAB', '#B8A9C9'];
            foreach (array_slice($services ?? [], 0, 4) as $i => $svc): 
            ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card card-soft h-100 service-card">
                        <div class="service-icon-wrapper" style="background: <?= $colors[$i % count($colors)] ?>15">
                            <i class="bi <?= $icons[$i % count($icons)] ?>" style="color: <?= $colors[$i % count($colors)] ?>"></i>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold mb-2"><?= htmlspecialchars($svc['name'] ?? '') ?></h5>
                            <p class="card-text text-muted small mb-3"><?= htmlspecialchars($svc['description'] ?? '') ?></p>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="fw-bold text-primary fs-5"><?= number_format($svc['price'] ?? 0) ?></span>
                                <span class="text-muted small">บาท</span>
                            </div>
                            <span class="badge bg-light text-dark mt-2"><?= $svc['duration_minutes'] ?? 0 ?> นาที</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= basePath() ?>/services" class="btn btn-outline-primary rounded-pill px-5 py-2">
                ดูบริการทั้งหมด <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 stats-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">ปีประสบการณ์</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">5,000+</div>
                    <div class="stat-label">ลูกค้าที่ใช้บริการ</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">ความพึงพอใจ</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <div class="stat-number">8+</div>
                    <div class="stat-label">บริการหลากหลาย</div>
                </div>
            </div>
        </div>
    </div>
</section>


