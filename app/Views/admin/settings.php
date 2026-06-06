<?php
$lineConfigured = $status['line_configured'] ?? false;
$slipConfigured = $status['slip2go_configured'] ?? false;
?>

<section class="page-hero">
    <div>
        <p class="eyebrow">Integration Settings</p>
        <h1>ตั้งค่าการเชื่อมต่อระบบ</h1>
        <p>บันทึก LINE Messaging API และ Slip2Go API key ลงฐานข้อมูล JSON ของระบบ โดยไม่ต้องแก้ไฟล์ config โดยตรง</p>
    </div>
</section>

<section class="content-panel">
    <form method="POST" action="<?= basePath() ?>/admin/settings" class="needs-validation" novalidate>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card card-soft h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                            <div>
                                <p class="eyebrow mb-2">LINE Messaging API</p>
                                <h5 class="fw-bold mb-1">LINE OA Notification</h5>
                                <p class="text-muted mb-0">ใช้ Channel Access Token สำหรับส่งข้อความแจ้งเตือนผ่าน LINE</p>
                            </div>
                            <span class="badge <?= $lineConfigured ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= $lineConfigured ? 'Configured' : 'Missing key' ?>
                            </span>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="line_enabled" name="line_enabled" value="1" <?= !empty($settings['line']['enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="line_enabled">เปิดใช้งาน LINE Messaging API</label>
                        </div>

                        <label class="form-label fw-semibold" for="line_channel_access_token">Channel Access Token</label>
                        <input type="password" class="form-control" id="line_channel_access_token" name="line_channel_access_token" placeholder="<?= $lineConfigured ? 'กรอกเฉพาะเมื่อต้องการเปลี่ยน token' : 'วาง LINE Channel Access Token' ?>" autocomplete="new-password">
                        <div class="form-text">
                            <?= $lineConfigured ? 'มี token บันทึกไว้แล้ว หากไม่กรอกใหม่ระบบจะใช้ค่าเดิม' : 'ยังไม่มี token ในฐานข้อมูล' ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-soft h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                            <div>
                                <p class="eyebrow mb-2">Slip2Go API</p>
                                <h5 class="fw-bold mb-1">Slip Verification</h5>
                                <p class="text-muted mb-0">ใช้ API key สำหรับตรวจสอบสลิปโอนเงินอัตโนมัติ</p>
                            </div>
                            <span class="badge <?= $slipConfigured ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= $slipConfigured ? 'Configured' : 'Missing key' ?>
                            </span>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="slip2go_enabled" name="slip2go_enabled" value="1" <?= !empty($settings['slip2go']['enabled']) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold" for="slip2go_enabled">เปิดใช้งาน Slip2Go Verification</label>
                        </div>

                        <label class="form-label fw-semibold" for="slip2go_api_key">Slip2Go API Key</label>
                        <input type="password" class="form-control" id="slip2go_api_key" name="slip2go_api_key" placeholder="<?= $slipConfigured ? 'กรอกเฉพาะเมื่อต้องการเปลี่ยน key' : 'วาง Slip2Go API Key' ?>" autocomplete="new-password">
                        <div class="form-text">
                            <?= $slipConfigured ? 'มี API key บันทึกไว้แล้ว หากไม่กรอกใหม่ระบบจะใช้ค่าเดิม' : 'ยังไม่มี API key ในฐานข้อมูล' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= basePath() ?>/admin" class="btn btn-outline-secondary">กลับแดชบอร์ด</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i>บันทึกการตั้งค่า
            </button>
        </div>
    </form>
</section>


