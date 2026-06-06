<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Our Services</span>
            <div class="d-lg-flex justify-content-between align-items-end gap-4">
                <div>
                    <h1 class="page-title">บริการของ <span class="brand-gradient">Wikanda</span></h1>
                    <p class="page-subtitle">เลือกบริการที่เหมาะกับลุคของคุณ พร้อมราคาและระยะเวลาชัดเจนก่อนจองคิว</p>
                </div>
                <div class="page-actions mt-3 mt-lg-0">
                    <a href="<?= basePath() ?>/booking/new" class="btn btn-gradient">
                        <i class="bi bi-calendar-plus me-2"></i>จองคิว
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($services ?? [] as $i => $svc): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-soft service-card h-100">
                        <div class="service-icon-wrapper">
                            <i class="bi <?= ['bi-scissors', 'bi-brush', 'bi-droplet', 'bi-palette', 'bi-stars', 'bi-heart'][$i % 6] ?>"></i>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars($svc['name'] ?? '') ?></h5>
                            <?php if (!empty($svc['name_en'])): ?>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($svc['name_en']) ?></p>
                            <?php endif; ?>
                            <p class="card-text text-muted"><?= htmlspecialchars($svc['description'] ?? '') ?></p>
                            <div class="service-meta justify-content-center">
                                <span class="service-pill"><i class="bi bi-cash"></i><?= number_format($svc['price'] ?? 0) ?> บาท</span>
                                <span class="service-pill"><i class="bi bi-clock"></i><?= $svc['duration_minutes'] ?? 0 ?> นาที</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


