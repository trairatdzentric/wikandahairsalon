<?php
/**
 * Wikanda Hair Salon - JSON to MySQL migration.
 *
 * CLI:
 *   php database/migrate_json_to_mysql.php
 *
 * Browser after upload:
 *   /database/migrate_json_to_mysql.php?key=wikanda-migrate-2026
 */

declare(strict_types=1);

$setupKey = 'wikanda-migrate-2026';
if (PHP_SAPI !== 'cli' && ($_GET['key'] ?? '') !== $setupKey) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

$config = require __DIR__ . '/../config/database.php';
if (($config['driver'] ?? '') !== 'mysql') {
    fail('Database driver is not mysql for this environment.');
}

try {
    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            (int) ($config['port'] ?? 3306),
            $config['database'],
            $config['charset'] ?? 'utf8mb4'
        ),
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $migrator = new JsonMysqlMigrator($pdo, __DIR__ . '/../data');
    $results = $migrator->run();

    renderResult(true, 'Migration completed', $results);
} catch (Throwable $e) {
    fail($e->getMessage());
}

final class JsonMysqlMigrator
{
    private array $cache = [];

    public function __construct(
        private PDO $pdo,
        private string $dataPath
    ) {
    }

    public function run(): array
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        $order = ['reviews', 'payments', 'bookings', 'staff', 'services', 'users', 'settings'];
        foreach ($order as $table) {
            $this->pdo->exec('TRUNCATE TABLE ' . $this->qi($table));
        }

        $results = [];
        foreach (['users', 'services', 'staff', 'bookings', 'payments', 'reviews', 'settings'] as $table) {
            $rows = $this->transform($table, $this->load($table));
            $results[$table] = $this->insertRows($table, $rows);
        }

        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        return $results;
    }

    private function load(string $table): array
    {
        if (isset($this->cache[$table])) {
            return $this->cache[$table];
        }

        $file = $this->dataPath . '/' . $table . '.json';
        if (!file_exists($file)) {
            return $this->cache[$table] = [];
        }

        $json = (string) file_get_contents($file);
        $json = preg_replace('/^\xEF\xBB\xBF/', '', $json) ?? $json;
        $json = ltrim($json, "\x00..\x1F");

        $decoded = json_decode($json, true);
        if ($table === 'settings' && !is_array($decoded)) {
            return $this->cache[$table] = [];
        }
        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid JSON: ' . $file);
        }

        return $this->cache[$table] = $decoded['data'] ?? [];
    }

    private function transform(string $table, array $rows): array
    {
        return array_map(fn (array $row): array => match ($table) {
            'users' => $this->pick($row, [
                'id', 'uuid', 'username', 'email', 'password_hash', 'full_name',
                'phone', 'role', 'line_user_id', 'avatar', 'created_at', 'updated_at',
            ]),
            'services' => $this->withDefaults($this->pick($row, [
                'id', 'uuid', 'name', 'name_en', 'description', 'price',
                'duration_minutes', 'category', 'image', 'is_active', 'created_at', 'updated_at',
            ]), ['updated_at' => $row['created_at'] ?? now()]),
            'staff' => $this->withDefaults($this->pick($row, [
                'id', 'uuid', 'user_id', 'display_name', 'specialty', 'bio', 'is_active',
            ]), ['created_at' => now(), 'updated_at' => now()]),
            'bookings' => $this->pick($row, [
                'id', 'uuid', 'booking_code', 'member_id', 'service_id', 'staff_id',
                'booking_date', 'start_time', 'end_time', 'total_price', 'status',
                'note', 'created_at', 'updated_at',
            ]),
            'payments' => [
                'id' => $row['id'] ?? null,
                'uuid' => $row['uuid'] ?? null,
                'booking_id' => $row['booking_id'] ?? null,
                'amount' => $row['amount'] ?? 0,
                'method' => $row['method'] ?? 'transfer',
                'status' => $row['status'] ?? 'pending',
                'slip_path' => $row['slip_image'] ?? null,
                'slip_uploaded_at' => $row['created_at'] ?? null,
                'verified_at' => $row['verified_at'] ?? null,
                'verified_by' => $row['verified_by'] ?? null,
                'note' => $row['note'] ?? null,
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['updated_at'] ?? now(),
            ],
            'reviews' => $this->reviewRow($row),
            'settings' => [
                'id' => $row['id'] ?? null,
                'uuid' => $row['uuid'] ?? null,
                'key' => $row['key'] ?? '',
                'value' => is_bool($row['value'] ?? null) ? (($row['value'] ?? false) ? '1' : '0') : (string) ($row['value'] ?? ''),
                'type' => $row['type'] ?? 'string',
                'created_at' => $row['created_at'] ?? now(),
                'updated_at' => $row['updated_at'] ?? now(),
            ],
            default => $row,
        }, $rows);
    }

    private function reviewRow(array $row): array
    {
        $booking = $this->findLoadedById('bookings', (int) ($row['booking_id'] ?? 0));

        return [
            'id' => $row['id'] ?? null,
            'uuid' => $row['uuid'] ?? null,
            'member_id' => $row['member_id'] ?? ($booking['member_id'] ?? null),
            'service_id' => $row['service_id'] ?? ($booking['service_id'] ?? null),
            'staff_id' => $row['staff_id'] ?? ($booking['staff_id'] ?? null),
            'booking_id' => $row['booking_id'] ?? null,
            'rating' => $row['rating'] ?? 5,
            'comment' => $row['comment'] ?? '',
            'is_visible' => $row['is_visible'] ?? 1,
            'created_at' => $row['created_at'] ?? now(),
            'updated_at' => $row['updated_at'] ?? ($row['created_at'] ?? now()),
        ];
    }

    private function findLoadedById(string $table, int $id): ?array
    {
        foreach ($this->load($table) as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                return $row;
            }
        }
        return null;
    }

    private function insertRows(string $table, array $rows): int
    {
        if ($rows === []) {
            return 0;
        }

        $columns = array_keys($rows[0]);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->qi($table),
            implode(', ', array_map([$this, 'qi'], $columns)),
            implode(', ', array_fill(0, count($columns), '?'))
        );
        $stmt = $this->pdo->prepare($sql);

        $count = 0;
        foreach ($rows as $row) {
            $stmt->execute(array_values($row));
            $count++;
        }

        return $count;
    }

    private function pick(array $row, array $keys): array
    {
        $picked = [];
        foreach ($keys as $key) {
            $picked[$key] = $row[$key] ?? null;
        }
        return $picked;
    }

    private function withDefaults(array $row, array $defaults): array
    {
        foreach ($defaults as $key => $value) {
            if (empty($row[$key])) {
                $row[$key] = $value;
            }
        }
        return $row;
    }

    private function qi(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid SQL identifier: ' . $identifier);
        }
        return '`' . $identifier . '`';
    }
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function fail(string $message): never
{
    renderResult(false, $message, []);
    exit(1);
}

function renderResult(bool $success, string $message, array $results): void
{
    if (PHP_SAPI === 'cli') {
        echo ($success ? '[SUCCESS] ' : '[ERROR] ') . $message . PHP_EOL;
        foreach ($results as $table => $count) {
            echo str_pad((string) $table, 14) . ': ' . $count . PHP_EOL;
        }
        return;
    }

    echo '<!doctype html><html lang="th"><meta charset="utf-8">';
    echo '<title>Wikanda Migration</title>';
    echo '<body style="font-family:Arial,sans-serif;padding:32px;line-height:1.6">';
    echo '<h1>' . ($success ? 'Migration สำเร็จ' : 'Migration ไม่สำเร็จ') . '</h1>';
    echo '<p>' . htmlspecialchars($message) . '</p>';
    if ($results !== []) {
        echo '<ul>';
        foreach ($results as $table => $count) {
            echo '<li><strong>' . htmlspecialchars((string) $table) . '</strong>: ' . (int) $count . ' records</li>';
        }
        echo '</ul>';
    }
    echo '<p style="color:#b00020">หลังสำเร็จให้ลบไฟล์ database/migrate_json_to_mysql.php หรือปิด key นี้</p>';
    echo '</body></html>';
}
