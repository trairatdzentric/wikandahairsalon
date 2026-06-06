<?php
/**
 * ============================================================
 *  api/v1/users.php — Users API
 * ============================================================
 *
 *  Endpoints:
 *   GET    /api/v1/users         — รายการผู้ใช้ทั้งหมด (admin/owner)
 *   GET    /api/v1/users/{id}    — ข้อมูลผู้ใช้รายคน
 *   POST   /api/v1/users         — สร้างผู้ใช้ใหม่ (admin/owner)
 *   PUT    /api/v1/users/{id}    — แก้ไขผู้ใช้
 *   DELETE /api/v1/users/{id}    — ลบผู้ใช้ (admin/owner)
 * ============================================================
 */

use App\Repositories\UserRepository;
use App\Services\AuthService;

$method = $_SERVER['REQUEST_METHOD'];
$repo   = new UserRepository();
$auth   = new AuthService();

switch ($method) {

    // --------------------------------------------------------
    // GET /api/v1/users
    // GET /api/v1/users/{id}
    // --------------------------------------------------------
    case 'GET':
        requireRole(['admin', 'owner']);

        if (isset($parts[2])) {
            $row = $repo->find((int) $parts[2]);
            if (!$row) {
                jsonResponse(false, null, 'ไม่พบผู้ใช้', 404);
            }
            jsonResponse(true, $row);
        } else {
            $role = $_GET['role'] ?? null;
            if ($role) {
                jsonResponse(true, $repo->findByRole($role));
            } else {
                jsonResponse(true, $repo->all());
            }
        }
        break;

    // --------------------------------------------------------
    // POST /api/v1/users
    // --------------------------------------------------------
    case 'POST':
        requireRole(['admin', 'owner']);
        $input = getJsonInput();

        $result = $auth->register($input);
        if (!$result['success']) {
            jsonResponse(false, null, $result['message'], 422);
        }

        jsonResponse(true, $result['user'], 'สร้างผู้ใช้สำเร็จ', 201);
        break;

    // --------------------------------------------------------
    // PUT /api/v1/users/{id}
    // --------------------------------------------------------
    case 'PUT':
        $user = requireAuth();
        $id   = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสผู้ใช้', 400);
        }

        // สมาชิกแก้ไขได้แค่ตัวเอง / Members can only edit themselves
        if ($user['role'] === 'member' && $user['id'] !== $id) {
            jsonResponse(false, null, 'ไม่มีสิทธิ์แก้ไขผู้ใช้นี้', 403);
        }

        $input = getJsonInput();

        // ห้ามแก้ไข role ถ้าไม่ใช่ admin/owner
        // Only admin/owner can change role
        if (!in_array($user['role'], ['admin', 'owner'], true) && isset($input['role'])) {
            unset($input['role']);
        }

        // ถ้ามีรหัสผ่านใหม่ → hash ก่อน
        // Hash new password if provided
        if (!empty($input['password'])) {
            $input['password_hash'] = $auth->hashPassword($input['password']);
            unset($input['password']);
        }

        $updated = $repo->update($id, $input);
        if (!$updated) {
            jsonResponse(false, null, 'ไม่พบผู้ใช้', 404);
        }

        jsonResponse(true, $updated, 'แก้ไขผู้ใช้สำเร็จ');
        break;

    // --------------------------------------------------------
    // DELETE /api/v1/users/{id}
    // --------------------------------------------------------
    case 'DELETE':
        requireRole(['admin', 'owner']);
        $id = (int) ($parts[2] ?? 0);

        if (!$id) {
            jsonResponse(false, null, 'ไม่ระบุรหัสผู้ใช้', 400);
        }

        if (!$repo->delete($id)) {
            jsonResponse(false, null, 'ไม่พบผู้ใช้', 404);
        }

        jsonResponse(true, null, 'ลบผู้ใช้สำเร็จ');
        break;

    // --------------------------------------------------------
    // อื่น ๆ
    // --------------------------------------------------------
    default:
        jsonResponse(false, null, 'Method not allowed', 405);
}
