<?php
/**
 * ============================================================
 *  Router.php — ตัวจับคู่ URL กับ Controller
 *               URL to Controller dispatcher
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - อ่านตาราง routes จาก config/routes.php
 *   - จับคู่ URL ปัจจุบันกับ route ที่กำหนด
 *   - รองรับ parameter ใน URL เช่น /booking/{id}
 *   - เรียก Controller@method ที่ตรงกัน
 *
 *  วิธีใช้ / Usage:
 *     $router = new Router();
 *     $router->dispatch();
 * ============================================================
 */

namespace App\Core;

class Router
{
    /** @var array ตารางเส้นทางทั้งหมด / Loaded routes table */
    private array $routes;

    public function __construct()
    {
        $this->routes = require __DIR__ . '/../../config/routes.php';
    }

    /**
     * จับคู่ URL ปัจจุบันแล้วเรียก Controller
     * Match current URL and dispatch to controller
     */
    public function dispatch(): void
    {
        $request = new Request();
        $method  = $request->method();
        $uri     = $request->uri();

        // วน loop ทุก route หา route ที่ตรง
        // Loop through routes to find a match
        foreach ($this->routes as $pattern => $handler) {

            // pattern เช่น "GET /booking/{id}" → แยกเป็น method + path
            [$routeMethod, $routePath] = explode(' ', $pattern, 2);

            if (strtoupper($routeMethod) !== $method) {
                continue;
            }

            // แปลง {id} ใน path ให้เป็น regex แบบ named group
            // Convert {param} in path to a named regex group
            $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $routePath);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {

                // ดึงเฉพาะ parameter ที่เป็น named group (ตัด numeric out)
                // Keep only named parameters
                $params = array_filter(
                    $matches,
                    fn($key) => !is_int($key),
                    ARRAY_FILTER_USE_KEY
                );

                $this->callController($handler, $params);
                return;
            }
        }

        // ไม่พบ route ที่ตรง → 404
        // No matching route → 404
        $this->notFound();
    }

    /**
     * แยก "ControllerName@method" แล้วเรียกใช้
     * Parse "Controller@method" string and call it
     */
    private function callController(string $handler, array $params): void
    {
        [$controllerName, $methodName] = explode('@', $handler);

        $fqcn = 'App\\Controllers\\' . $controllerName;
        if (!class_exists($fqcn)) {
            throw new \RuntimeException("ไม่พบ Controller: {$fqcn}");
        }

        $controller = new $fqcn();
        if (!method_exists($controller, $methodName)) {
            throw new \RuntimeException("ไม่พบเมธอด {$methodName} ใน {$fqcn}");
        }

        // ส่ง parameter ตามลำดับเข้า method
        // Pass route params to the controller method
        call_user_func_array([$controller, $methodName], array_values($params));
    }

    /**
     * แสดงหน้า 404 / Show 404 page
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - ไม่พบหน้าที่คุณค้นหา / Page Not Found</h1>';
    }
}
