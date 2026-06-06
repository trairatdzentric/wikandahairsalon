<?php

/**
 * Wikanda Hair Salon - Public Entry Point (InfinityFree)
 * ไฟล์นี้วางใน /htdocs/index.php
 */

// กำหนด BASE_PATH สำหรับ InfinityFree (อยู่ใน htdocs/)
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Error reporting (ปิดใน production)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/error.log');

// Autoloader
require_once APP_PATH . '/Core/autoload.php';

// Start session (แบบง่ายสำหรับ InfinityFree)
@session_start();

// Load configuration
$config = require CONFIG_PATH . '/app.php';

// Initialize router
use App\Core\Router;

$router = new Router();

// Load routes
require CONFIG_PATH . '/routes.php';

// Dispatch request
$router->dispatch();
