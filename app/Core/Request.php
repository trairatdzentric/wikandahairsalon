<?php
/**
 * ============================================================
 *  Request.php — ตัวห่อข้อมูลคำขอ HTTP
 *                HTTP Request Wrapper
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - ดึงค่าจาก $_GET, $_POST, JSON body
 *   - คืนค่า method และ URI
 *   - ห่อไฟล์ที่อัปโหลด
 *
 *  วิธีใช้ / Usage:
 *     $request = new Request();
 *     $email   = $request->input('email');
 *     if ($request->method() === 'POST') { ... }
 * ============================================================
 */

namespace App\Core;

class Request
{
    /** @var array ข้อมูลทั้งหมดที่รวมมาจาก GET + POST + JSON body */
    private array $data;

    public function __construct()
    {
        // รวม GET + POST / Merge GET and POST
        $this->data = array_merge($_GET, $_POST);

        // ถ้า request เป็น JSON ให้ parse เพิ่ม
        // If JSON request, parse and merge body
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $raw  = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $this->data = array_merge($this->data, $json);
            }
        }
    }

    /**
     * HTTP method เช่น GET, POST, PUT, DELETE
     * Return HTTP method
     */
    public function method(): string
    {
        // รองรับ method override ผ่าน hidden field _method
        // Support _method override (HTML form ส่ง POST แต่อยากให้เป็น PUT/DELETE)
        $override = $this->input('_method');
        if ($override) {
            return strtoupper($override);
        }
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * URI ปัจจุบัน ตัด query string และ base path ออก
     * Current URI path (without query string and base path)
     */
    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // ตัด query string ออก / Strip query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // ตัด base path ของโปรเจค (กรณีรันใน subfolder บน XAMPP)
        // Strip project base path (when running in subfolder on XAMPP)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath   = rtrim(dirname($scriptName), '/\\');
        if ($basePath && $basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        return '/' . ltrim($uri, '/');
    }

    /**
     * อ่านค่า input ทีละ key / Read one input by key
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * อ่านทุก input / All inputs
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * ตรวจว่ามี key หรือไม่
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * อ่านไฟล์ที่อัปโหลด / Get uploaded file
     *
     * คืน null ถ้าไม่มีไฟล์หรือมี error
     */
    public function file(string $key): ?array
    {
        if (!isset($_FILES[$key]) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        return $_FILES[$key];
    }

    /**
     * เช็คว่า request เป็น AJAX/JSON หรือไม่
     * Is this an AJAX/JSON request?
     */
    public function isAjax(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT']           ?? '';
        $xhr    = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return stripos($accept, 'application/json') !== false
            || strcasecmp($xhr, 'XMLHttpRequest') === 0;
    }
}
