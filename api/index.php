<?php
/**
 * ============================================================
 *  api/index.php — Front Controller ของ REST API
 *                  API Entry Point
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - โหลด autoload
 *   - ตั้งค่า CORS + JSON header
 *   - แยก URL เป็น version + resource + id
 *   - route ไปไฟล์ที่ตรงกับ resource
 *   - ให้ helper functions สำหรับไฟล์ลูก
 *
 *  URL รูปแบบ: /api/v1/{resource}/{id?}
 *  ตัวอย่าง: /api/v1/bookings/3
 * ============================================================
 */

// ------------------------------------------------------------
// 1. โหลด autoload / Load autoloader
// ------------------------------------------------------------
require_once __DIR__ . '/../app/Core/autoload.php';

use App\Core\Session;

// ------------------------------------------------------------
// 2. ตั้งค่า HTTP Headers
// ------------------------------------------------------------
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// ตอบกลับ OPTIONS ทันที (preflight)
// Respond to OPTIONS immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ------------------------------------------------------------
// 3. แยก URL / Parse URL
// ------------------------------------------------------------
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('#^.*?/api/#', '', $uri); // ตัดทุกอย่างก่อน /api/
$parts = explode('/', trim($path, '/'));         // ['v1', 'bookings', '3']

$version  = $parts[0] ?? 'v1';
$resource = $parts[1] ?? null;

// ------------------------------------------------------------
// 4. Helper functions สำหรับไฟล์ลูก / Helper functions for child files
// ------------------------------------------------------------

/**
 * ส่ง response กลับเป็น JSON
 * Send JSON response
 *
 * @param bool   $success สำเร็จหรือไม่
 * @param mixed  $data    ข้อมูลที่ต้องการส่ง
 * @param string $message ข้อความ
 * @param int    $code    HTTP status code
 */
function jsonResponse(bool $success, mixed $data = null, string $message = '', int $code = 200): void
{
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'data'    => $data,
        'message' => $message,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * อ่าน JSON body จาก request
 * Read JSON body from request
 *
 * @return array
 */
function getJsonInput(): array
{
    $raw  = file_get_contents('php://input');
    $json = json_decode($raw, true);
    return is_array($json) ? $json : [];
}

/**
 * ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
 * Check if user is authenticated
 *
 * @return array ข้อมูลผู้ใช้ / User data
 */
function requireAuth(): array
{
    Session::start();
    $userId = Session::get('user_id');
    if (!$userId) {
        jsonResponse(false, null, 'กรุณาเข้าสู่ระบบก่อน', 401);
    }

    // ดึงข้อมูลผู้ใช้ / Fetch user data
    $repo = new \App\Repositories\UserRepository();
    $user = $repo->find((int) $userId);
    if (!$user) {
        jsonResponse(false, null, 'ไม่พบผู้ใช้งาน', 401);
    }

    return $user;
}

/**
 * ตรวจสอบบทบาทของผู้ใช้
 * Check user role
 *
 * @param string|array $roles บทบาทที่อนุญาต
 * @return array ข้อมูลผู้ใช้
 */
function requireRole(string|array $roles): array
{
    $user = requireAuth();
    $userRole = $user['role'] ?? '';

    $allowed = is_string($roles) ? [$roles] : $roles;
    if (!in_array($userRole, $allowed, true)) {
        jsonResponse(false, null, 'ไม่มีสิทธิ์เข้าถึง', 403);
    }

    return $user;
}

// ------------------------------------------------------------
// 5. Route ไปไฟล์ที่ตรงกับ resource
// ------------------------------------------------------------
if (!$resource) {
    jsonResponse(false, null, 'ไม่ระบุ resource', 400);
}

$file = __DIR__ . "/{$version}/{$resource}.php";

if (file_exists($file)) {
    require $file;
} else {
    jsonResponse(false, null, 'Not found', 404);
}
