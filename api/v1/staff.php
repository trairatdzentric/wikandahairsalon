<?php
/**
 * ============================================================
 *  api/v1/staff.php — Staff API
 * ============================================================
 *
 *  Endpoints:
 *   GET    /api/v1/staff         — รายการช่างทั้งหมด
 *   GET    /api/v1/staff/{id}    — ข้อมูลช่างรายคน
 *   POST   /api/v1/staff         — สร้างช่างใหม่ (admin/owner)
 *   PUT    /api/v1/staff/{id}    — แก้ไขข้อมูลช่าง (admin/owner)
 *   DELETE /api/v1/staff/{id}    — ลบช่าง (admin/owner)
 * ============================================================
 */

use App\Repositories\StaffRepository;

$method = $_SERVER['REQUEST_METHOD'];
$repo   = new StaffRepository();

switch ($method) {

    // --------------------------------------------------------
    // GET /api/v1/staff
    // GET /api/v1/staff/{id}
    // --------------------------------------------------------
    case 'GET':
        if (isset($parts[2])) {
            $row = $repo->find((int) $parts[2]);
            if (!$row) {
                jsonResponse(false, null, 'ไม่พบช่าง', 404);
            }
            jsonResponse(true, $row);
        } else {
            $active = isset($_GET['active']) ? filter_var($_GET['active'], FILTER_VALIDATE_BOOL) : null;

            if ($active === true) {
                jsonResponse(true, $repo->findActive());
            } else {
                jsonResponse(true, $repo->all());
            }
        }
        break;

    // --------------------------------------------------------
    // POST /api/v1/staff
    // --------------------------------------------------------
    case 'POST':
        requireRole(['admin', 'owner']);
        $input = getJsonInput();

        $required = ['user_id', 'display_name', 'specialty'];
        foreach ($required as $field) {
            if (empty($input[$field] ?? '')) {
                jsonResponse(false, null, "กรุณากรอก {$field}", 400);
            }
        }

        $staff = $repo->create($input);
        jsonResponse(true, $staff, 'สร้างข้อมูลช่างสำเร็จ', 201);
        break;

    // --------------------------------------------------------
    // PUT /api/v1/staff/{id}
    // --------------------------------------------------------
    case 'PUT':
        requireRole(['admin', 'owner']);
        $id = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสช่าง', 400);
        }

        $input   = getJsonInput();
        $updated = $repo->update($id, $input);

        if (!$updated) {
            jsonResponse(false, null, 'ไม่พบช่าง', 404);
        }

        jsonResponse(true, $updated, 'แก้ไขข้อมูลช่างสำเร็จ');
        break;

    // --------------------------------------------------------
    // DELETE /api/v1/staff/{id}
    // --------------------------------------------------------
    case 'DELETE':
        requireRole(['admin', 'owner']);
        $id = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสช่าง', 400);
        }

        if (!$repo->delete($id)) {
            jsonResponse(false, null, 'ไม่พบช่าง', 404);
        }

        jsonResponse(true, null, 'ลบข้อมูลช่างสำเร็จ');
        break;

    // --------------------------------------------------------
    // อื่น ๆ
    // --------------------------------------------------------
    default:
        jsonResponse(false, null, 'Method not allowed', 405);
}
