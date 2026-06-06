<?php
/**
 * ============================================================
 *  AuthMiddleware.php — ตรวจสิทธิ์ผู้ใช้ก่อนเข้าหน้า / Route auth guard
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - ตรวจว่าผู้ใช้เข้าสู่ระบบแล้วหรือยัง
 *   - ตรวจบทบาทที่อนุญาตให้เข้าหน้า
 *   - redirect ไปหน้าที่เหมาะสมเมื่อไม่มีสิทธิ์
 * ============================================================
 */

namespace App\Middleware;

use App\Core\Session;

class AuthMiddleware
{
    /**
     * ต้องเข้าสู่ระบบก่อนเข้าใช้งาน / Require an authenticated user
     */
    public static function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'กรุณาเข้าสู่ระบบก่อน / Please login first');
            self::redirect('/login');
        }
    }

    /**
     * ต้องมีบทบาทตามที่กำหนด / Require one of the allowed roles
     *
     * @param string ...$roles บทบาทที่อนุญาต / Allowed roles
     */
    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();

        $role = Session::get('user_role') ?: Session::role();
        if (!in_array($role, $roles, true)) {
            Session::flash('error', 'คุณไม่มีสิทธิ์เข้าหน้านี้ / Access denied');
            self::redirect('/');
        }
    }

    /**
     * ส่งผู้ใช้ไปหน้าอื่น / Redirect to a web path
     */
    private static function redirect(string $url): void
    {
        if (!str_starts_with($url, 'http')) {
            $url = self::appUrl($url);
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * Build an application URL using the current browser host when available.
     */
    private static function appUrl(string $path): string
    {
        $config = require __DIR__ . '/../../config/app.php';
        $baseUrl = $config['base_url'];

        if (!empty($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $basePath = parse_url($baseUrl, PHP_URL_PATH) ?: '';
            $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim($basePath, '/');
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
