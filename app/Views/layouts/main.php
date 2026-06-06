<?php require_once __DIR__ . '/../helpers.php'; ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Wikanda Hair Salon') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= assetPath('css/style.css') ?>" rel="stylesheet">
</head>

<body>
    <!-- ปุ่มเปิด sidebar บนมือถือ / Mobile toggle -->
    <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()" aria-label="เปิดเมนู">
        <i class="bi bi-list"></i>
    </button>

    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="page-wrapper">
        <?php include __DIR__ . '/../partials/navbar.php'; ?>

        <main class="main-content">
            <?= $content ?? '' ?>
        </main>

        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= assetPath('js/main.js') ?>"></script>
</body>

</html>

