<?php
/**
 * ============================================================
 *  api/v1/services.php — Services API
 * ============================================================
 *
 *  Endpoints:
 *   GET    /api/v1/services         — รายการบริการทั้งหมด
 *   GET    /api/v1/services/{id}    — ข้อมูลบริการรายตัว
 *   POST   /api/v1/services         — สร้างบริการใหม่ (admin/owner)
 *   PUT    /api/v1/services/{id}    — แก้ไขบริการ (admin/owner)
 *   DELETE /api/v1/services/{id}    — ลบบริการ (admin/owner)
 * ============================================================
 */

use App\Repositories\ServiceRepository;

$method = $_SERVER['REQUEST_METHOD'];
$repo   = new ServiceRepository();

switch ($method) {

    // --------------------------------------------------------
    // GET /api/v1/services
    // GET /api/v1/services/{id}
    // --------------------------------------------------------
    case 'GET':
        if (isset($parts[2])) {
            $row = $repo->find((int) $parts[2]);
            if (!$row) {
                jsonResponse(false, null, 'ไม่พบบริการ', 404);
            }
            jsonResponse(true, $row);
        } else {
            $category = $_GET['category'] ?? null;
            $search   = $_GET['search'] ?? null;
            $active   = isset($_GET['active']) ? filter_var($_GET['active'], FILTER_VALIDATE_BOOL) : null;

            if ($search) {
                jsonResponse(true, $repo->searchByName($search));
            } elseif ($category) {
                jsonResponse(true, $repo->findByCategory($category));
            } elseif ($active === true) {
                jsonResponse(true, $repo->findActive());
            } else {
                jsonResponse(true, $repo->all());
            }
        }
        break;

    // --------------------------------------------------------
    // POST /api/v1/services
    // --------------------------------------------------------
    case 'POST':
        requireRole(['admin', 'owner']);
        $input = getJsonInput();

        $required = ['name', 'price', 'duration_minutes', 'category'];
        foreach ($required as $field) {
            if (empty($input[$field] ?? '')) {
                jsonResponse(false, null, "กรุณากรอก {$field}", 400);
            }
        }

        $service = $repo->create($input);
        jsonResponse(true, $service, 'สร้างบริการสำเร็จ', 201);
        break;

    // --------------------------------------------------------
    // PUT /api/v1/services/{id}
    // --------------------------------------------------------
    case 'PUT':
        requireRole(['admin', 'owner']);
        $id = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสบริการ', 400);
        }

        $input   = getJsonInput();
        $updated = $repo->update($id, $input);

        if (!$updated) {
            jsonResponse(false, null, 'ไม่พบบริการ', 404);
        }

        jsonResponse(true, $updated, 'แก้ไขบริการสำเร็จ');
        break;

    // --------------------------------------------------------
    // DELETE /api/v1/services/{id}
    // --------------------------------------------------------
    case 'DELETE':
        requireRole(['admin', 'owner']);
        $id = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสบริการ', 400);
        }

        if (!$repo->delete($id)) {
            jsonResponse(false, null, 'ไม่พบบริการ', 404);
        }

        jsonResponse(true, null, 'ลบบริการสำเร็จ');
        break;

    // --------------------------------------------------------
    // อื่น ๆ
    // --------------------------------------------------------
    default:
        jsonResponse(false, null, 'Method not allowed', 405);
}
