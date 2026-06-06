<?php
/**
 * ============================================================
 *  api/v1/auth.php — Authentication API
 * ============================================================
 *
 *  Endpoints:
 *   POST   /api/v1/auth/register  — ลงทะเบียน
 *   POST   /api/v1/auth/login     — เข้าสู่ระบบ
 *   POST   /api/v1/auth/logout   — ออกจากระบบ
 *   GET    /api/v1/auth/me       — ข้อมูลผู้ใช้ปัจจุบัน
 * ============================================================
 */

use App\Services\AuthService;

$method = $_SERVER['REQUEST_METHOD'];
$auth   = new AuthService();

switch ($method) {

    // --------------------------------------------------------
    // POST /api/v1/auth/register
    // --------------------------------------------------------
    case 'POST':
        $input = getJsonInput();
        $action = $input['action'] ?? '';

        if ($action === 'login') {
            // --- Login ---
            $emailOrUsername = $input['email'] ?? $input['username'] ?? '';
            $password        = $input['password'] ?? '';

            if (!$emailOrUsername || !$password) {
                jsonResponse(false, null, 'กรุณากรอกอีเมล/ชื่อผู้ใช้และรหัสผ่าน', 400);
            }

            $result = $auth->login($emailOrUsername, $password);
            if (!$result['success']) {
                jsonResponse(false, null, $result['message'], 401);
            }

            jsonResponse(true, [
                'user'  => $result['user'],
                'token' => session_id(), // ใช้ session ID เป็น token
            ], $result['message']);

        } elseif ($action === 'logout') {
            // --- Logout ---
            $auth->logout();
            jsonResponse(true, null, 'ออกจากระบบสำเร็จ');

        } else {
            // --- Register (default) ---
            $result = $auth->register($input);
            if (!$result['success']) {
                jsonResponse(false, null, $result['message'], 422);
            }

            jsonResponse(true, $result['user'], $result['message'], 201);
        }
        break;

    // --------------------------------------------------------
    // GET /api/v1/auth/me
    // --------------------------------------------------------
    case 'GET':
        $user = requireAuth();
        jsonResponse(true, $user, 'ข้อมูลผู้ใช้ปัจจุบัน');
        break;

    // --------------------------------------------------------
    // อื่น ๆ
    // --------------------------------------------------------
    default:
        jsonResponse(false, null, 'Method not allowed', 405);
}
