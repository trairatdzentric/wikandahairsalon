<?php

/**
 * ============================================================
 *  Session.php — ตัวจัดการ Session แบบ wrapper บาง ๆ
 *                Thin Session Wrapper
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - ห่อ $_SESSION ให้ใช้งานเป็นเมธอด (อ่านง่ายขึ้น)
 *   - ตรวจสอบการล็อกอินและ Role
 *   - เก็บ flash message (ข้อความที่แสดงครั้งเดียวแล้วหาย)
 *
 *  วิธีใช้ / Usage:
 *     Session::start();
 *     Session::set('user_id', 1);
 *     $uid = Session::get('user_id');
 *     Session::flash('success', 'บันทึกสำเร็จ');
 * ============================================================
 */

namespace App\Core;

class Session
{
    private static bool $isInfinityFree = false;
    private static bool $started = false;
    private static string $sessionFile = '';

    /**
     * ตรวจสอบว่ารันอยู่บน InfinityFree หรือไม่
     */
    private static function checkInfinityFree(): bool
    {
        return isset($_SERVER['HTTP_HOST'])
            && str_contains($_SERVER['HTTP_HOST'], 'free.nf');
    }

    /**
     * เริ่ม session (เรียกครั้งเดียวต่อ request)
     * Start session if not already started
     */
    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        self::$isInfinityFree = self::checkInfinityFree();

        if (self::$isInfinityFree) {
            // InfinityFree: ใช้ Custom File-based Session แทน
            self::startCustomSession();
        } else {
            // Local: ใช้ PHP Native Session ปกติ
            if (session_status() === PHP_SESSION_NONE) {
                $config = require __DIR__ . '/../../config/app.php';
                session_name($config['session']['name'] ?? 'wikanda_session');
                session_start();
            }
        }

        self::$started = true;
    }

    /**
     * เริ่ม Custom Session สำหรับ InfinityFree (ไม่ใช้ cookies)
     */
    private static function startCustomSession(): void
    {
        $sessionDir = __DIR__ . '/../../storage/sessions';
        if (!is_dir($sessionDir)) {
            mkdir($sessionDir, 0755, true);
        }

        // ใช้ IP + User Agent เป็น session identifier (ไม่ใช้ cookies)
        $sessionId = md5(($_SERVER['REMOTE_ADDR'] ?? 'unknown') . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
        self::$sessionFile = $sessionDir . '/sess_' . $sessionId . '.json';

        // โหลดข้อมูล session จากไฟล์
        if (file_exists(self::$sessionFile)) {
            $data = json_decode(file_get_contents(self::$sessionFile), true);
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $_SESSION[$key] = $value;
                }
            }
        }
    }

    /**
     * บันทึก Custom Session ลงไฟล์
     */
    private static function saveCustomSession(): void
    {
        if (self::$isInfinityFree && self::$sessionFile) {
            file_put_contents(self::$sessionFile, json_encode($_SESSION, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * กำหนดค่า / Set a session value
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
        self::saveCustomSession();
    }

    /**
     * อ่านค่า / Get a session value
     *
     * @param string $key
     * @param mixed  $default ค่าเริ่มต้นเมื่อไม่เจอ
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * ตรวจว่ามี key หรือไม่ / Check if key exists
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * ลบ key เดียว / Remove one key
     */
    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
        self::saveCustomSession();
    }

    /**
     * ล้าง session ทั้งหมด (ใช้ตอน logout)
     * Destroy all session data (used on logout)
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];

        if (self::$isInfinityFree && self::$sessionFile && file_exists(self::$sessionFile)) {
            unlink(self::$sessionFile);
        } else {
            session_destroy();
        }
    }

    // ============================================================
    // Auth helpers — ลัดสำหรับเช็คผู้ใช้ปัจจุบัน
    //                Shortcuts for current user
    // ============================================================

    /**
     * ผู้ใช้ล็อกอินอยู่หรือไม่ / Is the user logged in?
     */
    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }

    /**
     * คืน array ข้อมูลผู้ใช้ปัจจุบัน หรือ null
     * Return current user array or null
     */
    public static function user(): ?array
    {
        return self::get('user');
    }

    /**
     * คืน Role ของผู้ใช้ปัจจุบัน
     * Return current user role
     */
    public static function role(): ?string
    {
        $role = self::get('user_role');
        if ($role) {
            return (string) $role;
        }

        $user = self::user();
        return $user['role'] ?? null;
    }

    /**
     * เช็คว่าเป็น Role ที่กำหนดหรือไม่
     * Check whether the current user has one of the given roles
     *
     * @param string ...$roles เช่น 'admin', 'owner'
     */
    public static function hasRole(string ...$roles): bool
    {
        return in_array(self::role(), $roles, true);
    }

    // ============================================================
    // Flash messages — ข้อความที่แสดง 1 ครั้งแล้วหาย
    //                  One-time flash messages
    // ============================================================

    /**
     * ตั้ง flash message / Set a flash message
     *
     * @param string $key   เช่น 'success', 'error'
     * @param string $value ข้อความ / Message text
     */
    public static function flash(string $key, string $value): void
    {
        self::start();
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * อ่าน flash message แล้วลบทิ้ง
     * Read flash message and remove it
     */
    public static function getFlash(string $key): ?string
    {
        self::start();
        $value = $_SESSION['_flash'][$key] ?? null;
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
        }
        return $value;
    }
}
