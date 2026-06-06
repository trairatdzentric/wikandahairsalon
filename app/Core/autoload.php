<?php
/**
 * ============================================================
 *  autoload.php — ตัวโหลดคลาสอัตโนมัติ (PSR-4 Autoloader)
 *                  Automatic Class Loader
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - ลงทะเบียน spl_autoload_register สำหรับ namespace App\
 *   - แปลง App\Models\User → app/Models/User.php
 *   - ตั้งค่า timezone และ error reporting ตาม config/app.php
 *   - ให้ helper functions: app_path(), config()
 *
 *  วิธีใช้ / Usage:
 *     require_once __DIR__ . '/../app/Core/autoload.php';
 *     use App\Core\Router; // โหลดอัตโนมัติ
 * ============================================================
 */

// ------------------------------------------------------------
// 1. ป้องกันการเรียกไฟล์ซ้ำ / Prevent double inclusion
// ------------------------------------------------------------
if (defined('APP_AUTOLOADED')) {
    return;
}
define('APP_AUTOLOADED', true);

// ------------------------------------------------------------
// 2. ตั้งค่าพื้นฐาน / Base configuration
// ------------------------------------------------------------
$baseDir = dirname(__DIR__, 2); // จาก app/Core/ → รากโปรเจกต์

// โหลด config ถ้ามี / Load config if available
$configFile = $baseDir . '/config/app.php';
if (file_exists($configFile)) {
    $config = require $configFile;

    // ตั้งค่า timezone / Set timezone
    if (!empty($config['timezone'])) {
        date_default_timezone_set($config['timezone']);
    }

    // ตั้งค่า error reporting / Set error reporting
    if (($config['env'] ?? 'production') === 'development' || ($config['debug'] ?? false)) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
    } else {
        error_reporting(0);
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
    }
}

// ------------------------------------------------------------
// 3. PSR-4 Autoloader สำหรับ namespace App\
//    Map App\Core\Database → app/Core/Database.php
// ------------------------------------------------------------
spl_autoload_register(function (string $class): void {
    // prefix หลักของโปรเจกต์ / Project namespace prefix
    $prefix = 'App\\';

    // ไดเรกทอรีฐานที่ไฟล์คลาสจะถูกเก็บ / Base directory for classes
    $baseDir = __DIR__ . '/../';

    // ถ้าคลาสไม่ได้ขึ้นต้นด้วย App\ ให้ข้ามไป
    // If class doesn't start with App\, skip
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    // ตัด prefix ออก → เหลือส่วน relative เช่น Core\Database
    // Remove prefix → get relative part e.g. Core\Database
    $relative = substr($class, strlen($prefix));

    // แปลง backslash เป็น slash แล้วต่อท้ายด้วย .php
    // Convert backslashes to slashes and append .php
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    // โหลดไฟล์ถ้ามีอยู่ / Load file if it exists
    if (file_exists($file)) {
        require_once $file;
    }
});

// ------------------------------------------------------------
// 4. Helper functions สะดวก / Convenience helpers
// ------------------------------------------------------------
if (!function_exists('app_path')) {
    /**
     * คืนค่า path ภายในโปรเจกต์
     * Get absolute path inside the project
     *
     * @param string $path เส้นทางย่อย / Sub-path
     * @return string เส้นทางเต็ม / Full path
     */
    function app_path(string $path = ''): string
    {
        return dirname(__DIR__, 2) . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('config')) {
    /**
     * อ่านค่าตั้งค่าจากไฟล์ config
     * Read configuration value
     *
     * @param string $key     ชื่อ key เช่น 'app.name' / Config key
     * @param mixed  $default ค่าเริ่มต้นถ้าไม่พบ / Default value
     * @return mixed ค่าที่อ่านได้ / Config value
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $configs = [];

        $segments = explode('.', $key);
        $file     = array_shift($segments);
        $filePath = app_path("config/{$file}.php");

        if (!isset($configs[$file])) {
            $configs[$file] = file_exists($filePath) ? require $filePath : [];
        }

        $value = $configs[$file];
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
