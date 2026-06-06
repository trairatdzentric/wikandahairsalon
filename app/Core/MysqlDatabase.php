<?php

/**
 * ============================================================
 *  MysqlDatabase.php — ตัวจัดการฐานข้อมูล MySQL
 *                 MySQL Database Manager
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - เชื่อมต่อและจัดการ MySQL database
 *   - รองรับ prepared statements
 *   - จัดการ UUID และ timestamps อัตโนมัติ
 *
 *  วิธีใช้ / Usage:
 *     $db = new MysqlDatabase('users');
 *     $rows = $db->all();
 *     $row  = $db->find(1);
 *     $new  = $db->insert([...]);
 *     $db->update(1, ['name' => 'x']);
 *     $db->delete(1);
 * ============================================================
 */

namespace App\Core;

use PDO;
use PDOException;

class MysqlDatabase implements DatabaseInterface
{
    /** @var PDO|null Connection instance */
    private static ?PDO $connection = null;

    /** @var string ชื่อตาราง / Table name */
    private string $table;

    /**
     * Constructor
     * @param string $table ชื่อตาราง
     */
    public function __construct(string $table)
    {
        $this->table = $table;
        $this->getConnection();
    }

    /**
     * Get database connection (singleton)
     * @return PDO
     */
    private function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';

            try {
                self::$connection = new PDO(
                    "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
                    $config['username'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    /**
     * Generate UUID v4
     * @return string
     */
    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        $stmt = $this->getConnection()->query("SELECT * FROM {$this->quoteIdentifier($this->table)}");
        return $stmt->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?array
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM {$this->quoteIdentifier($this->table)} WHERE `id` = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUuid(string $uuid): ?array
    {
        $stmt = $this->getConnection()->prepare("SELECT * FROM {$this->quoteIdentifier($this->table)} WHERE `uuid` = ?");
        $stmt->execute([$uuid]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function where(string $key, mixed $value): array
    {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM {$this->quoteIdentifier($this->table)} WHERE {$this->quoteIdentifier($key)} = ?"
        );
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    public function whereMultiple(array $conditions): array
    {
        if (empty($conditions)) {
            return $this->all();
        }

        $whereClauses = [];
        $values = [];

        foreach ($conditions as $key => $value) {
            $whereClauses[] = $this->quoteIdentifier($key) . " = ?";
            $values[] = $value;
        }

        $whereSql = implode(' AND ', $whereClauses);
        $stmt = $this->getConnection()->prepare("SELECT * FROM {$this->quoteIdentifier($this->table)} WHERE {$whereSql}");
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findOne(array $conditions): ?array
    {
        $results = $this->whereMultiple($conditions);
        return $results[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $data): array
    {
        // Add UUID if not present
        if (!isset($data['uuid'])) {
            $data['uuid'] = $this->generateUuid();
        }

        // Add timestamps
        $now = date('Y-m-d H:i:s');
        if (!isset($data['created_at'])) {
            $data['created_at'] = $now;
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = $now;
        }

        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->quoteIdentifier($this->table),
            implode(', ', array_map([$this, 'quoteIdentifier'], $columns)),
            implode(', ', $placeholders)
        );

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(array_values($data));

        // Get the inserted ID
        $data['id'] = (int) $this->getConnection()->lastInsertId();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): ?array
    {
        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        $sets = [];
        $values = [];

        foreach ($data as $key => $value) {
            $sets[] = $this->quoteIdentifier($key) . " = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $sql = sprintf(
            "UPDATE %s SET %s WHERE `id` = ?",
            $this->quoteIdentifier($this->table),
            implode(', ', $sets)
        );

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($values);
        return $this->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $stmt = $this->getConnection()->prepare("DELETE FROM {$this->quoteIdentifier($this->table)} WHERE `id` = ?");
        return $stmt->execute([$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        $stmt = $this->getConnection()->query("SELECT COUNT(*) FROM {$this->quoteIdentifier($this->table)}");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Execute raw SQL query
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute raw SQL (non-select)
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->getConnection()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Begin transaction
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     * @return bool
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback transaction
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }

    private function quoteIdentifier(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new \InvalidArgumentException('Invalid SQL identifier: ' . $identifier);
        }

        return '`' . $identifier . '`';
    }
}
