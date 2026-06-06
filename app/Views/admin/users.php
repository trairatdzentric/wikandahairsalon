<section class="py-4">
    <div class="container">
        <div class="page-hero">
            <span class="page-kicker">Accounts</span>
            <h1 class="page-title">จัดการผู้ใช้งาน</h1>
            <p class="page-subtitle">ดูข้อมูลบัญชี บทบาท และช่องทางติดต่อของผู้ใช้ทั้งหมดในระบบ</p>
        </div>

        <div class="content-panel">
            <div class="panel-header">
                <h2 class="panel-title">Users</h2>
                <span class="text-muted small"><?= count($users ?? []) ?> บัญชี</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อผู้ใช้</th>
                            <th>อีเมล</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>บทบาท</th>
                            <th>โทรศัพท์</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users ?? [] as $u): ?>
                            <tr>
                                <td class="fw-bold"><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['username'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['full_name'] ?? '') ?></td>
                                <td><span class="badge <?= roleBadge($u['role'] ?? '') ?>"><?= htmlspecialchars($u['role'] ?? '') ?></span></td>
                                <td><?= htmlspecialchars($u['phone'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
