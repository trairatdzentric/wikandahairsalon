<?php
/**
 * ============================================================
 *  api/v1/reviews.php — Reviews API
 * ============================================================
 *
 *  Endpoints:
 *   GET    /api/v1/reviews         — รายการรีวิวทั้งหมด
 *   GET    /api/v1/reviews/{id}    — ข้อมูลรีวิวรายตัว
 *   POST   /api/v1/reviews         — สร้างรีวิวใหม่
 *   PUT    /api/v1/reviews/{id}    — แก้ไขรีวิว (เจ้าของรีวิว)
 *   DELETE /api/v1/reviews/{id}    — ลบรีวิว (admin/owner)
 * ============================================================
 */

use App\Models\Review;
use App\Repositories\ReviewRepository;

$method = $_SERVER['REQUEST_METHOD'];
$repo   = new ReviewRepository();

switch ($method) {

    // --------------------------------------------------------
    // GET /api/v1/reviews
    // GET /api/v1/reviews/{id}
    // --------------------------------------------------------
    case 'GET':
        if (isset($parts[2])) {
            $row = $repo->find((int) $parts[2]);
            if (!$row) {
                jsonResponse(false, null, 'ไม่พบรีวิว', 404);
            }
            jsonResponse(true, $row);
        } else {
            $memberId = $_GET['member_id'] ?? null;
            $staffId  = $_GET['staff_id'] ?? null;

            if ($memberId) {
                jsonResponse(true, $repo->findByMember((int) $memberId));
            } elseif ($staffId) {
                jsonResponse(true, $repo->findByStaff((int) $staffId));
            } else {
                jsonResponse(true, $repo->all());
            }
        }
        break;

    // --------------------------------------------------------
    // POST /api/v1/reviews
    // --------------------------------------------------------
    case 'POST':
        $user  = requireAuth();
        $input = getJsonInput();

        // ตรวจสอบคะแนน / Validate rating
        $rating = (int) ($input['rating'] ?? 0);
        if (!Review::isValidRating($rating)) {
            jsonResponse(false, null, 'คะแนนต้องอยู่ระหว่าง 1-5', 400);
        }

        // force member_id เป็นตัวเอง / Force member_id to self
        $input['member_id'] = $user['id'];

        $review = $repo->create($input);
        jsonResponse(true, $review, 'สร้างรีวิวสำเร็จ', 201);
        break;

    // --------------------------------------------------------
    // PUT /api/v1/reviews/{id}
    // --------------------------------------------------------
    case 'PUT':
        $user = requireAuth();
        $id   = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสรีวิว', 400);
        }

        $review = $repo->find($id);
        if (!$review) {
            jsonResponse(false, null, 'ไม่พบรีวิว', 404);
        }

        // สมาชิกแก้ไขได้แค่รีวิวตัวเอง / Members can only edit own review
        if ($user['role'] === 'member' && (int) $review['member_id'] !== $user['id']) {
            jsonResponse(false, null, 'ไม่มีสิทธิ์แก้ไขรีวิวนี้', 403);
        }

        $input = getJsonInput();

        // ตรวจสอบคะแนน / Validate rating
        if (isset($input['rating']) && !Review::isValidRating((int) $input['rating'])) {
            jsonResponse(false, null, 'คะแนนต้องอยู่ระหว่าง 1-5', 400);
        }

        $updated = $repo->update($id, $input);
        jsonResponse(true, $updated, 'แก้ไขรีวิวสำเร็จ');
        break;

    // --------------------------------------------------------
    // DELETE /api/v1/reviews/{id}
    // --------------------------------------------------------
    case 'DELETE':
        $user = requireAuth();
        $id   = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสรีวิว', 400);
        }

        $review = $repo->find($id);
        if (!$review) {
            jsonResponse(false, null, 'ไม่พบรีวิว', 404);
        }

        // สมาชิกลบได้แค่รีวิวตัวเอง / Members can only delete own review
        if ($user['role'] === 'member' && (int) $review['member_id'] !== $user['id']) {
            jsonResponse(false, null, 'ไม่มีสิทธิ์ลบรีวิวนี้', 403);
        }

        $repo->delete($id);
        jsonResponse(true, null, 'ลบรีวิวสำเร็จ');
        break;

    // --------------------------------------------------------
    // อื่น ๆ
    // --------------------------------------------------------
    default:
        jsonResponse(false, null, 'Method not allowed', 405);
}
