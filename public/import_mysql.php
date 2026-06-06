<?php
/**
 * One-time MySQL schema importer for InfinityFree.
 *
 * Usage after upload:
 * /import_mysql.php?key=wikanda-import-2026
 *
 * Delete this file after successful import.
 */

declare(strict_types=1);

$setupKey = 'wikanda-import-2026';

if (($_GET['key'] ?? '') !== $setupKey) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$configPath = __DIR__ . '/../config/database.php';
$schemaPath = __DIR__ . '/../database/infinityfree_schema.sql';

if (!file_exists($configPath) || !file_exists($schemaPath)) {
    http_response_code(500);
    echo 'Missing config/database.php or database/infinityfree_schema.sql';
    exit;
}

$db = require $configPath;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli(
        (string) $db['host'],
        (string) $db['username'],
        (string) $db['password'],
        (string) $db['database'],
        (int) $db['port']
    );
    $mysqli->set_charset((string) ($db['charset'] ?? 'utf8mb4'));

    $sql = file_get_contents($schemaPath);
    if ($sql === false) {
        throw new RuntimeException('Cannot read SQL schema file.');
    }

    $statements = splitSqlStatements($sql);
    $executed = 0;

    foreach ($statements as $statement) {
        $trimmed = trim($statement);
        if ($trimmed === '' || str_starts_with($trimmed, '--')) {
            continue;
        }

        $mysqli->query($trimmed);
        $executed++;
    }

    echo '<!doctype html><html lang="th"><meta charset="utf-8">';
    echo '<title>MySQL Import Complete</title>';
    echo '<body style="font-family:Arial,sans-serif;padding:32px;line-height:1.6">';
    echo '<h1>Import สำเร็จ</h1>';
    echo '<p>สร้าง/อัปเดตฐานข้อมูล <strong>' . htmlspecialchars((string) $db['database']) . '</strong> แล้ว</p>';
    echo '<p>จำนวนคำสั่งที่รัน: <strong>' . $executed . '</strong></p>';
    echo '<p style="color:#b00020">สำคัญ: ลบไฟล์ <code>public/import_mysql.php</code> หลังใช้งานเสร็จ</p>';
    echo '</body></html>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!doctype html><html lang="th"><meta charset="utf-8">';
    echo '<title>MySQL Import Failed</title>';
    echo '<body style="font-family:Arial,sans-serif;padding:32px;line-height:1.6">';
    echo '<h1>Import ไม่สำเร็จ</h1>';
    echo '<pre style="white-space:pre-wrap;background:#f6f6f6;padding:16px;border-radius:8px">';
    echo htmlspecialchars($e->getMessage());
    echo '</pre>';
    echo '</body></html>';
}

function splitSqlStatements(string $sql): array
{
    $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;
    $parts = [];
    $buffer = '';
    $inString = false;
    $quote = '';
    $length = strlen($sql);

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $prev = $i > 0 ? $sql[$i - 1] : '';

        if (($char === "'" || $char === '"') && $prev !== '\\') {
            if (!$inString) {
                $inString = true;
                $quote = $char;
            } elseif ($quote === $char) {
                $inString = false;
                $quote = '';
            }
        }

        if ($char === ';' && !$inString) {
            $parts[] = $buffer;
            $buffer = '';
            continue;
        }

        $buffer .= $char;
    }

    if (trim($buffer) !== '') {
        $parts[] = $buffer;
    }

    return $parts;
}
