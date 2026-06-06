<?php

/**
 * Wikanda Hair Salon - Pre-Deployment Checklist
 * 
 * รันไฟล์นี้ก่อนอัปโหลดขึ้น InfinityFree เพื่อตรวจสอบความพร้อม
 * Run this before deploying to InfinityFree
 */

// Load database configuration
$dbConfig = require __DIR__ . '/../config/database.php';

$errors = [];
$warnings = [];
$success = [];

echo "========================================\n";
echo "Pre-Deployment Checklist\n";
echo "Wikanda Hair Salon\n";
echo "========================================\n\n";

// 1. Check PHP Version
$phpVersion = phpversion();
if (version_compare($phpVersion, '8.0.0', '>=')) {
    $success[] = "PHP Version: $phpVersion ✓";
} else {
    $errors[] = "PHP Version: $phpVersion (ต้องการ 8.0+) ✗";
}

// 2. Check Required Extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'session', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        $success[] = "Extension: $ext ✓";
    } else {
        $errors[] = "Extension: $ext ไม่พร้อมใช้งาน ✗";
    }
}

// 3. Check Database Connection
try {
    $db = new PDO(
        "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['database'] . ";charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $success[] = "Database Connection ✓";

    // 4. Check Tables
    $requiredTables = ['users', 'services', 'staff', 'bookings', 'payments', 'reviews', 'settings'];
    $stmt = $db->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($requiredTables as $table) {
        if (in_array($table, $existingTables)) {
            // Check if table has data
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            $success[] = "Table: $table ✓ ($count records)";
        } else {
            $errors[] = "Table: $table ไม่พบ ✗";
        }
    }
} catch (PDOException $e) {
    $errors[] = "Database Connection: " . $e->getMessage() . " ✗";
}

// 5. Check Directory Permissions
$directories = [
    __DIR__ . '/../storage' => 'Storage Directory',
    __DIR__ . '/../storage/logs' => 'Logs Directory',
    __DIR__ . '/../storage/uploads' => 'Uploads Directory',
    __DIR__ . '/../storage/uploads/slips' => 'Slips Directory',
];

foreach ($directories as $dir => $name) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            $success[] = "$name: Writable ✓";
        } else {
            $warnings[] = "$name: ไม่สามารถเขียนได้ ⚠";
        }
    } else {
        $warnings[] = "$name: ไม่พบไดเรกทอรี ⚠";
    }
}

// 6. Check Configuration Files
$configFiles = [
    __DIR__ . '/../config/app.php' => 'App Config',
    __DIR__ . '/../config/database.php' => 'Database Config',
    __DIR__ . '/../config/routes.php' => 'Routes Config',
];

foreach ($configFiles as $file => $name) {
    if (file_exists($file)) {
        $success[] = "$name: Found ✓";
    } else {
        $errors[] = "$name: Not Found ✗";
    }
}

// 7. Check Core Files
$coreFiles = [
    __DIR__ . '/../app/Core/autoload.php' => 'Autoloader',
    __DIR__ . '/../app/Core/Router.php' => 'Router',
    __DIR__ . '/../app/Core/Database.php' => 'Database (JSON)',
    __DIR__ . '/../app/Core/MysqlDatabase.php' => 'MySQL Database',
    __DIR__ . '/../app/Core/DatabaseInterface.php' => 'Database Interface',
];

foreach ($coreFiles as $file => $name) {
    if (file_exists($file)) {
        $success[] = "$name: Found ✓";
    } else {
        $errors[] = "$name: Not Found ✗";
    }
}

// 8. Check Repository Files
$repoFiles = [
    __DIR__ . '/../app/Repositories/BaseRepository.php',
    __DIR__ . '/../app/Repositories/UserRepository.php',
    __DIR__ . '/../app/Repositories/ServiceRepository.php',
    __DIR__ . '/../app/Repositories/StaffRepository.php',
    __DIR__ . '/../app/Repositories/BookingRepository.php',
    __DIR__ . '/../app/Repositories/PaymentRepository.php',
    __DIR__ . '/../app/Repositories/ReviewRepository.php',
    __DIR__ . '/../app/Repositories/SettingRepository.php',
];

$missingRepos = 0;
foreach ($repoFiles as $file) {
    if (!file_exists($file)) {
        $missingRepos++;
    }
}

if ($missingRepos === 0) {
    $success[] = "All Repositories: Found ✓";
} else {
    $errors[] = "Repositories: $missingRepos missing ✗";
}

// 9. Check JSON Data Files (for migration)
$jsonFiles = [
    __DIR__ . '/../data/users.json',
    __DIR__ . '/../data/services.json',
    __DIR__ . '/../data/staff.json',
    __DIR__ . '/../data/bookings.json',
    __DIR__ . '/../data/payments.json',
    __DIR__ . '/../data/reviews.json',
    __DIR__ . '/../data/settings.json',
];

$jsonReady = true;
foreach ($jsonFiles as $file) {
    if (!file_exists($file)) {
        $jsonReady = false;
        break;
    }
}

if ($jsonReady) {
    $success[] = "JSON Data Files: Ready for migration ✓";
} else {
    $warnings[] = "JSON Data Files: Some files missing ⚠";
}

// 10. Check Database Driver
$config = require __DIR__ . '/../config/database.php';
if ($config['driver'] === 'mysql') {
    $success[] = "Database Driver: MySQL ✓";
} else {
    $warnings[] = "Database Driver: JSON Mode (ควรเปลี่ยนเป็น 'mysql' ก่อน deploy) ⚠";
}

// Summary
echo "\n========================================\n";
echo "Results\n";
echo "========================================\n\n";

if (!empty($success)) {
    echo "✓ SUCCESS:\n";
    foreach ($success as $msg) {
        echo "  ✓ $msg\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠ WARNINGS:\n";
    foreach ($warnings as $msg) {
        echo "  ⚠ $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "✗ ERRORS:\n";
    foreach ($errors as $msg) {
        echo "  ✗ $msg\n";
    }
    echo "\n";
}

echo "========================================\n";
echo "Summary\n";
echo "========================================\n";
echo "Success:   " . count($success) . "\n";
echo "Warnings:  " . count($warnings) . "\n";
echo "Errors:    " . count($errors) . "\n";
echo "\n";

if (empty($errors)) {
    echo "✓ พร้อมสำหรับการ Deploy!\n";
    echo "\nขั้นตอนต่อไป:\n";
    echo "1. รัน migration: php database/migrate_json_to_mysql.php\n";
    echo "2. อัปโหลดไฟล์ขึ้น InfinityFree\n";
    echo "3. ทดสอบการทำงาน\n";
    exit(0);
} else {
    echo "✗ กรุณาแก้ไขข้อผิดพลาดก่อน Deploy\n";
    exit(1);
}
