<?php

$prefix = '/Wikanda_Hair_Salon/public';
$apiPrefix = '/Wikanda_Hair_Salon/api';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (str_starts_with($uri, $apiPrefix)) {
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/Wikanda_Hair_Salon/api/index.php';
    $_SERVER['PHP_SELF'] = '/Wikanda_Hair_Salon/api/index.php';

    require __DIR__ . '/api/index.php';
    return true;
}

if (str_starts_with($uri, $prefix)) {
    $relative = substr($uri, strlen($prefix)) ?: '/';
} else {
    $relative = $uri;
}

$publicFile = __DIR__ . '/public' . $relative;
if ($relative !== '/' && is_file($publicFile)) {
    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    $types = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
    ];
    if (isset($types[$ext])) {
        header('Content-Type: ' . $types[$ext]);
    }
    readfile($publicFile);
    return true;
}

$_SERVER['REQUEST_URI'] = $relative;
$_SERVER['SCRIPT_NAME'] = '/Wikanda_Hair_Salon/public/index.php';
$_SERVER['PHP_SELF'] = '/Wikanda_Hair_Salon/public/index.php';

require __DIR__ . '/public/index.php';
