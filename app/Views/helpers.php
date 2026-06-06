<?php

/**
 * ============================================================
 *  helpers.php — Helper functions สำหรับ Views
 * ============================================================
 */

/**
 * คืนค่า base path สำหรับสร้าง URL
 * ตรวจสอบอัตโนมัติว่ารันอยู่บน environment ไหน
 */
function basePath(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? '';

    if (str_contains($host, 'free.nf') || str_contains($host, 'atwebpages.com')) {
        return '';
    }

    if ($host === 'localhost:8000') {
        return '';
    }

    return '/Wikanda_Hair_Salon/public';
}

/**
 * สร้าง URL จาก path
 */
function url(string $path = ''): string
{
    $base = basePath();
    if ($path === '' || $path === '/') {
        return $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
}

function apiPath(string $path = ''): string
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $base = (str_contains($host, 'free.nf') || str_contains($host, 'atwebpages.com'))
        ? '/api'
        : '/Wikanda_Hair_Salon/api';

    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function assetPath(string $path = ''): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * คืนค่า CSS class ของ badge ตามสถานะการจอง
 * Get badge CSS class for booking status
 *
 * @param string $status
 * @return string
 */
function statusBadge(string $status): string
{
    $map = [
        'pending'    => 'warning',
        'confirmed'  => 'info',
        'in_service' => 'primary',
        'completed'  => 'success',
        'cancelled'  => 'danger',
        'verified'   => 'success',
        'rejected'   => 'danger',
    ];
    return 'bg-' . ($map[$status] ?? 'secondary');
}

/**
 * คืนค่า CSS class ของ badge ตามบทบาทผู้ใช้
 * Get badge CSS class for user role
 *
 * @param string $role
 * @return string
 */
function roleBadge(string $role): string
{
    $map = [
        'admin'  => 'danger',
        'owner'  => 'dark',
        'staff'  => 'info',
        'member' => 'success',
    ];
    return 'bg-' . ($map[$role] ?? 'secondary');
}

/**
 * ตรวจสอบว่า URL ปัจจุบันตรงกับ path ที่กำหนดหรือไม่
 * Check if current URL matches the given path
 *
 * @param string $path
 * @return bool
 */
function isActive(string $path): bool
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = basePath();

    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base)) ?: '/';
    }

    $uri = '/' . trim($uri, '/');
    $path = '/' . trim($path, '/');

    if ($path === '/') {
        return $uri === '/';
    }

    return $uri === $path || str_starts_with($uri, $path . '/');
}


