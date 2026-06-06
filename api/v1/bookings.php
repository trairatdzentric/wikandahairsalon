<?php
/**
 * ============================================================
 *  api/v1/bookings.php — Bookings API
 * ============================================================
 *
 *  Endpoints:
 *   GET    /api/v1/bookings              — รายการจองทั้งหมด
 *   GET    /api/v1/bookings/{id}         — ข้อมูลจองรายตัว
 *   POST   /api/v1/bookings              — สร้างการจองใหม่
 *   PUT    /api/v1/bookings/{id}          — อัปเดตการจอง
 *   PUT    /api/v1/bookings/{id}/cancel  — ยกเลิกการจอง
 *   DELETE /api/v1/bookings/{id}          — ลบการจอง (admin/owner)
 * ============================================================
 */

use App\Services\BookingService;
use App\Repositories\BookingRepository;

$method = $_SERVER['REQUEST_METHOD'];
$service = new BookingService();
$repo    = new BookingRepository();

switch ($method) {

    // --------------------------------------------------------
    // GET /api/v1/bookings
    // GET /api/v1/bookings/{id}
    // --------------------------------------------------------
    case 'GET':
        if (isset($parts[2])) {
            $row = $repo->find((int) $parts[2]);
            if (!$row) {
                jsonResponse(false, null, 'ไม่พบการจอง', 404);
            }
            jsonResponse(true, $row);
        } else {
            $memberId = $_GET['member_id'] ?? null;
            $staffId  = $_GET['staff_id'] ?? null;
            $status   = $_GET['status'] ?? null;
            $date     = $_GET['date'] ?? null;

            if ($memberId) {
                jsonResponse(true, $repo->findByMember((int) $memberId));
            } elseif ($staffId) {
                jsonResponse(true, $repo->findByStaff((int) $staffId));
            } elseif ($status) {
                jsonResponse(true, $repo->findByStatus($status));
            } elseif ($date) {
                jsonResponse(true, $repo->findByDate($date));
            } else {
                jsonResponse(true, $repo->all());
            }
        }
        break;

    // --------------------------------------------------------
    // POST /api/v1/bookings
    // --------------------------------------------------------
    case 'POST':
        $user   = requireAuth();
        $input  = getJsonInput();

        // ถ้าเป็น member ให้ force member_id เป็นตัวเอง
        // If member, force member_id to self
        if (($user['role'] ?? '') === 'member') {
            $input['member_id'] = $user['id'];
        }

        $result = $service->createBooking($input);
        if (!$result['success']) {
            jsonResponse(false, null, $result['message'], 422);
        }

        jsonResponse(true, $result['booking'], $result['message'], 201);
        break;

    // --------------------------------------------------------
    // PUT /api/v1/bookings/{id}
    // PUT /api/v1/bookings/{id}/cancel
    // --------------------------------------------------------
    case 'PUT':
        $user = requireAuth();
        $id   = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสการจอง', 400);
        }

        $action = $parts[3] ?? '';

        if ($action === 'cancel') {
            // --- ยกเลิกการจอง / Cancel booking ---
            $memberId = ($user['role'] === 'member') ? $user['id'] : (int) (getJsonInput()['member_id'] ?? $user['id']);
            $reason   = getJsonInput()['reason'] ?? '';

            $result = $service->cancelBooking($id, $memberId, $reason);
            if (!$result['success']) {
                jsonResponse(false, null, $result['message'], 422);
            }
            jsonResponse(true, null, $result['message']);
        } else {
            // --- อัปเดตสถานะ / Update status ---
            $input = getJsonInput();

            if (isset($input['status'])) {
                $result = $service->updateStatus($id, $input['status']);
                if (!$result['success']) {
                    jsonResponse(false, null, $result['message'], 422);
                }
                jsonResponse(true, null, $result['message']);
            }

            // อัปเดตข้อมูลทั่วไป / General update
            $updated = $repo->update($id, $input);
            if (!$updated) {
                jsonResponse(false, null, 'ไม่พบการจอง', 404);
            }
            jsonResponse(true, $updated, 'แก้ไขการจองสำเร็จ');
        }
        break;

    // --------------------------------------------------------
    // DELETE /api/v1/bookings/{id}
    // --------------------------------------------------------
    case 'DELETE':
        requireRole(['admin', 'owner']);
        $id = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสการจอง', 400);
        }

        if (!$repo->delete($id)) {
            jsonResponse(false, null, 'ไม่พบการจอง', 404);
        }

        jsonResponse(true, null, 'ลบการจองสำเร็จ');
        break;

    // --------------------------------------------------------
    // อื่น ๆ
    // --------------------------------------------------------
    default:
        jsonResponse(false, null, 'Method not allowed', 405);
}
