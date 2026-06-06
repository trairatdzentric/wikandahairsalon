<?php
/**
 * ============================================================
 *  View.php — ตัว render template HTML
 *             Simple HTML Template Renderer
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - โหลดไฟล์ template จาก app/Views/
 *   - แทรกข้อมูลแบบ extract($data)
 *   - รองรับ layout (template wrapper)
 *
 *  วิธีใช้ / Usage:
 *     View::render('home/index', [
 *         'title'   => 'หน้าแรก',
 *         'message' => 'ยินดีต้อนรับ',
 *     ]);
 * ============================================================
 */

namespace App\Core;

class View
{
    /**
     * Render template โดยใช้ layout หลัก
     * Render a view inside the main layout
     *
     * @param string $view ชื่อ template เช่น 'home/index'
     * @param array  $data ข้อมูลที่ต้องการส่งไป view (key จะกลายเป็นตัวแปร)
     */
    public static function render(string $view, array $data = []): void
    {
        self::loadHelpers();

        // เก็บเนื้อหา view ลงตัวแปร $content
        // Capture the view content into $content
        $content = self::renderPartial($view, $data);

        // ใส่ลง layout หลัก / Wrap with main layout
        $layoutPath = self::path('layouts/main');
        if (!file_exists($layoutPath)) {
            // ถ้าไม่มี layout ก็แสดงเนื้อหาตรง ๆ
            // Fallback: print content as-is when no layout
            echo $content;
            return;
        }

        // ทำให้ค่าใน $data ใช้เป็นตัวแปรใน layout ได้ด้วย
        // Extract data variables for the layout
        extract($data, EXTR_SKIP);

        include $layoutPath;
    }

    /**
     * Render template ย่อย (ไม่ครอบ layout) — ใช้สำหรับ partial / fragment
     * Render a view without the main layout
     *
     * @return string HTML ที่ render เสร็จแล้ว / Rendered HTML
     */
    public static function renderPartial(string $view, array $data = []): string
    {
        self::loadHelpers();

        $path = self::path($view);
        if (!file_exists($path)) {
            throw new \RuntimeException("ไม่พบ view: {$view} ({$path})");
        }

        // extract: เปลี่ยน key ของ array เป็นตัวแปรในขอบเขตปัจจุบัน
        // extract turns array keys into local variables
        extract($data, EXTR_SKIP);

        // เก็บ output ลง buffer แทนที่จะส่งออกหน้าจอ
        // Capture output into a buffer instead of sending to browser
        ob_start();
        include $path;
        return ob_get_clean();
    }

    /**
     * Render เป็น JSON สำหรับ API (กรณีต้องใช้ใน Web Controller)
     * Render data as JSON response
     */
    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * แปลงชื่อ view เป็น path จริง
     * Convert view name to absolute file path
     */
    private static function path(string $view): string
    {
        return __DIR__ . '/../Views/' . $view . '.php';
    }

    private static function loadHelpers(): void
    {
        $helpers = __DIR__ . '/../Views/helpers.php';
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }
}
