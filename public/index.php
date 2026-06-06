<?php
/**
 * ============================================================
 *  public/index.php — Front Controller (จุดเริ่มต้นของแอป)
 *                      Application Entry Point
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - โหลด autoload
 *   - เริ่ม session
 *   - ตั้ง timezone
 *   - ส่งต่อให้ Router dispatch
 *
 *  ทุก request ที่ไม่ใช่ไฟล์จริงจะถูก rewrite มาที่นี่
 * ============================================================
 */

require_once __DIR__ . '/../app/Core/autoload.php';

use App\Core\Router;
use App\Core\Session;

// เริ่ม session / Start session
Session::start();

// ตั้ง timezone / Set timezone
date_default_timezone_set('Asia/Bangkok');

// ส่งต่อให้ Router / Dispatch to router
(new Router())->dispatch();
