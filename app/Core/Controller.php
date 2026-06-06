<?php
/**
 * ============================================================
 *  Controller.php — คลาสฐานของ Controller ทุกตัว
 *                   Base Controller Class
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - มี helper พื้นฐานสำหรับ render view, redirect, json
 *   - ให้ Controller ลูกใช้ $this->view(...) แทนที่จะ import เอง
 *
 *  วิธีใช้ / Usage:
 *     class HomeController extends Controller {
 *         public function index() {
 *             $this->view('home/index', ['title' => 'หน้าแรก']);
 *         }
 *     }
 * ============================================================
 */

namespace App\Core;

abstract class Controller
{
    /** @var Request คำขอปัจจุบัน / Current request */
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Render view + layout / Render view with layout
     */
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    /**
     * Render JSON / Send JSON response (สำหรับ Web Controller ถ้าอยากตอบ JSON)
     */
    protected function json(mixed $data, int $status = 200): void
    {
        View::json($data, $status);
    }

    /**
     * Redirect ไปยัง URL อื่น / Redirect to another URL
     *
     * @param string $url URL ปลายทาง / Destination URL (absolute or relative)
     */
    protected function redirect(string $url): void
    {
        // ถ้าเป็น path สัมพัทธ์ ให้เติม base_url นำหน้า
        // If relative path, prepend base_url
        if (!str_starts_with($url, 'http')) {
            $url = $this->appUrl($url);
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * Build an application URL using the current browser host when available.
     */
    private function appUrl(string $path): string
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

    /**
     * แสดงหน้า 404 แบบเรียบง่าย / Render a simple not found response
     */
    protected function notFound(string $message = 'ไม่พบหน้าที่คุณค้นหา / Page Not Found'): void
    {
        http_response_code(404);
        View::render('errors/404', [
            'title'   => '404',
            'message' => $message,
        ]);
    }

    /**
     * เช็คว่าผู้ใช้ล็อกอินหรือยัง — ถ้ายังจะเด้งไปหน้า login
     * Require login or redirect to /login
     */
    protected function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'กรุณาเข้าสู่ระบบก่อน / Please login first');
            $this->redirect('/login');
        }
    }

    /**
     * เช็คว่าผู้ใช้มี role ตามที่กำหนด ถ้าไม่ใช่จะเด้งกลับหน้าแรก
     * Require role or redirect home
     *
     * @param string ...$roles allowed roles เช่น 'admin', 'owner'
     */
    protected function requireRole(string ...$roles): void
    {
        $this->requireLogin();
        if (!Session::hasRole(...$roles)) {
            Session::flash('error', 'คุณไม่มีสิทธิ์เข้าหน้านี้ / Access denied');
            $this->redirect('/');
        }
    }
}
