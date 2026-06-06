<section class="py-4">
    <div class="container">
        <div class="empty-state">
            <i class="bi bi-exclamation-triangle"></i>
            <h1 class="fw-bold">404</h1>
            <p class="text-muted"><?= htmlspecialchars($message ?? 'ไม่พบหน้าที่คุณค้นหา / Page Not Found') ?></p>
            <a href="<?= basePath() ?>/" class="btn btn-gradient">
                <i class="bi bi-house-door me-2"></i>กลับหน้าแรก
            </a>
        </div>
    </div>
</section>


