<?php
/**
 * Database configuration.
 * Keep real hosting passwords private and do not publish them.
 */

$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = $host === ''
    || str_starts_with($host, '127.0.0.1')
    || str_starts_with($host, 'localhost')
    || str_starts_with($host, '[::1]');

if ($isLocal) {
    return [
        'driver'    => 'json',
        'data_path' => __DIR__ . '/../data',
    ];
}

return [
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST') ?: 'YOUR_MYSQL_HOST',
    'port'      => (int) (getenv('DB_PORT') ?: 3306),
    'database'  => getenv('DB_DATABASE') ?: 'YOUR_MYSQL_DATABASE',
    'username'  => getenv('DB_USERNAME') ?: 'YOUR_MYSQL_USERNAME',
    'password'  => getenv('DB_PASSWORD') ?: '',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
