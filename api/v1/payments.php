<?php
/**
 * ============================================================
 *  api/v1/payments.php — Payments API
 * ============================================================
 *
 *  Endpoints:
 *   GET    /api/v1/payments              — รายการชำระเงินทั้งหมด
 *   GET    /api/v1/payments/{id}           — ข้อมูลชำระเงินรายตัว
 *   POST   /api/v1/payments               — สร้างรายการชำระเงิน
 *   PUT    /api/v1/payments/{id}          — อัปเดตรายการชำระเงิน
 *   PUT    /api/v1/payments/{id}/verify   — ตรวจสอบสลิปด้วย Slip2Go
 *   PUT    /api/v1/payments/{id}/approve  — อนุมัติด้วยมือ (admin/owner)
 *   PUT    /api/v1/payments/{id}/reject   — ปฏิเสธ (admin/owner)
 *   DELETE /api/v1/payments/{id}          — ลบรายการ (admin/owner)
 * ============================================================
 */

use App\Services\PaymentService;
use App\Repositories\PaymentRepository;

$method  = $_SERVER['REQUEST_METHOD'];
$service = new PaymentService();
$repo    = new PaymentRepository();

switch ($method) {

    // --------------------------------------------------------
    // GET /api/v1/payments
    // GET /api/v1/payments/{id}
    // --------------------------------------------------------
    case 'GET':
        if (isset($parts[2])) {
            $row = $repo->find((int) $parts[2]);
            if (!$row) {
                jsonResponse(false, null, 'ไม่พบรายการชำระเงิน', 404);
            }
            jsonResponse(true, $row);
        } else {
            $bookingId = $_GET['booking_id'] ?? null;
            $status    = $_GET['status'] ?? null;
            $method    = $_GET['method'] ?? null;

            if ($bookingId) {
                jsonResponse(true, $repo->findByBooking((int) $bookingId));
            } elseif ($status) {
                jsonResponse(true, $repo->findByStatus($status));
            } elseif ($method) {
                jsonResponse(true, $repo->findByMethod($method));
            } else {
                jsonResponse(true, $repo->all());
            }
        }
        break;

    // --------------------------------------------------------
    // POST /api/v1/payments
    // --------------------------------------------------------
    case 'POST':
        requireAuth();
        $input  = getJsonInput();
        $result = $service->createPayment($input);

        if (!$result['success']) {
            jsonResponse(false, null, $result['message'], 422);
        }

        jsonResponse(true, $result['payment'], $result['message'], 201);
        break;

    // --------------------------------------------------------
    // PUT /api/v1/payments/{id}
    // PUT /api/v1/payments/{id}/verify
    // PUT /api/v1/payments/{id}/approve
    // PUT /api/v1/payments/{id}/reject
    // --------------------------------------------------------
    case 'PUT':
        $user   = requireAuth();
        $id     = (int) ($parts[2] ?? 0);
        $action = $parts[3] ?? '';

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสการชำระเงิน', 400);
        }

        switch ($action) {
            case 'verify':
                $result = $service->verifyPayment($id);
                jsonResponse($result['success'], null, $result['message']);

            case 'approve':
                requireRole(['admin', 'owner']);
                $result = $service->approveManually($id, (int) $user['id']);
                jsonResponse($result['success'], null, $result['message']);

            case 'reject':
                requireRole(['admin', 'owner']);
                $reason = getJsonInput()['reason'] ?? '';
                $result = $service->rejectPayment($id, (int) $user['id'], $reason);
                jsonResponse($result['success'], null, $result['message']);

            default:
                // อัปเดตข้อมูลทั่วไป / General update
                $input   = getJsonInput();
                $updated = $repo->update($id, $input);

                if (!$updated) {
                    jsonResponse(false, null, 'ไม่พบรายการชำระเงิน', 404);
                }
                jsonResponse(true, $updated, 'แก้ไขรายการชำระเงินสำเร็จ');
        }
        break;

    // --------------------------------------------------------
    // DELETE /api/v1/payments/{id}
    // --------------------------------------------------------
    case 'DELETE':
        requireRole(['admin', 'owner']);
        $id = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสการชำระเงิน', 400);
        }

        if (!$repo->delete($id)) {
            jsonResponse(false, null, 'ไม่พบรายการชำระเงิน', 404);
        }

        jsonResponse(true, null, 'ลบรายการชำระเงินสำเร็จ');
        break;

    // --------------------------------------------------------
    // อื่น ๆ
    // --------------------------------------------------------
    default:
        jsonResponse(false, null, 'Method not allowed', 405);
}
