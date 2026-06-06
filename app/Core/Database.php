<?php

/**
 * ============================================================
 *  Database.php — ตัวจัดการฐานข้อมูล JSON
 *                 JSON-based Database Manager
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - อ่าน/เขียนไฟล์ JSON ในโฟลเดอร์ data/
 *   - จัดการ auto-increment id และ UUID
 *   - ป้องกัน race condition ด้วย flock()
 *
 *  วิธีใช้ / Usage:
 *     $db = new Database('users');
 *     $rows = $db->all();              // อ่านทั้งหมด
 *     $row  = $db->find(1);            // อ่านตาม id
 *     $new  = $db->insert([...]);      // เพิ่มแถวใหม่
 *     $db->update(1, ['name' => 'x']); // แก้ไข
 *     $db->delete(1);                  // ลบ
 *
 *  หมายเหตุ: คลาสนี้คือ "Persistence Layer" ในสถาปัตยกรรม
 *           วันหลังถ้าย้ายไป MySQL ก็เปลี่ยนแค่คลาสนี้คลาสเดียว
 * ============================================================
 */

namespace App\Core;

class Database implements DatabaseInterface
{
    /** @var string ชื่อตาราง (= ชื่อไฟล์ json ไม่รวมนามสกุล) / Table name */
    private string $table;

    /** @var string path เต็มของไฟล์ JSON / Full path to JSON file */
    private string $filePath;

    /** @var array โหลดข้อมูลทั้งก้อนมาเก็บใน memory / In-memory data */
    private array $store;

    /**
     * สร้าง instance สำหรับตารางหนึ่งตาราง
     * Create database handle for one table
     *
     * @param string $table ชื่อตาราง เช่น 'users' / Table name e.g. 'users'
     */
    public function __construct(string $table)
    {
        $config         = require __DIR__ . '/../../config/app.php';
        $this->table    = $table;
        $this->filePath = $config['data_path'] . '/' . $table . '.json';

        // ถ้าไฟล์ยังไม่มี ให้สร้างเปล่า ๆ
        // If file does not exist, create empty one
        if (!file_exists($this->filePath)) {
            $this->writeRaw([
                '_meta' => ['description' => $table, 'next_id' => 1],
                'data'  => [],
            ]);
        }

        $this->store = $this->readRaw();
    }

    // ============================================================
    // เมธอดหลักสำหรับใช้งานภายนอก / Public CRUD methods
    // ============================================================

    /**
     * คืนข้อมูลทุกแถว / Return all rows
     *
     * @return array
     */
    public function all(): array
    {
        return $this->store['data'] ?? [];
    }

    /**
     * ค้นแถวเดียวตาม id / Find one row by integer id
     *
     * @param int $id
     * @return array|null คืน null ถ้าไม่เจอ / null when not found
     */
    public function find(int $id): ?array
    {
        foreach ($this->all() as $row) {
            if (isset($row['id']) && (int) $row['id'] === $id) {
                return $row;
            }
        }
        return null;
    }

    /**
     * ค้นแถวเดียวตาม UUID / Find one row by UUID string
     *
     * @param string $uuid
     * @return array|null
     */
    public function findByUuid(string $uuid): ?array
    {
        foreach ($this->all() as $row) {
            if (isset($row['uuid']) && $row['uuid'] === $uuid) {
                return $row;
            }
        }
        return null;
    }

    /**
     * ค้นหลายแถวด้วยเงื่อนไข key=value / Query rows where key equals value
     *
     * ตัวอย่าง: $db->where('role', 'staff')
     *
     * @param string $key   ชื่อ field / Field name
     * @param mixed  $value ค่าที่ต้องการ / Expected value
     * @return array
     */
    public function where(string $key, mixed $value): array
    {
        $result = [];
        foreach ($this->all() as $row) {
            if (isset($row[$key]) && $row[$key] === $value) {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * เพิ่มแถวใหม่ + สร้าง id และ uuid อัตโนมัติ
     * Insert a new row with auto-generated id and uuid
     *
     * @param array $row ข้อมูลที่ต้องการบันทึก (ไม่ต้องใส่ id/uuid)
     * @return array แถวที่บันทึกแล้ว (รวม id และ uuid) / Saved row
     */
    public function insert(array $row): array
    {
        $nextId = $this->store['_meta']['next_id'] ?? 1;
        $now    = date('Y-m-d H:i:s');

        // เติม id และ uuid อัตโนมัติ / Auto-fill id and uuid
        $row['id']         = $nextId;
        $row['uuid']       = self::generateUuid();
        $row['created_at'] = $row['created_at'] ?? $now;
        $row['updated_at'] = $row['updated_at'] ?? $now;

        $this->store['data'][]          = $row;
        $this->store['_meta']['next_id'] = $nextId + 1;
        $this->writeRaw($this->store);

        return $row;
    }

    /**
     * แก้ไขแถวตาม id / Update row by id
     *
     * @param int   $id
     * @param array $changes ค่าที่ต้องการอัปเดต (เฉพาะคีย์ที่ต้องการแก้)
     * @return bool สำเร็จหรือไม่
     */
    public function update(int $id, array $changes): ?array
    {
        foreach ($this->store['data'] as $i => $row) {
            if ((int) $row['id'] === $id) {

                // กันแก้ id และ uuid โดยพลาด / Protect id and uuid
                unset($changes['id'], $changes['uuid']);

                $changes['updated_at'] = date('Y-m-d H:i:s');
                $this->store['data'][$i] = array_merge($row, $changes);

                $this->writeRaw($this->store);
                return $this->store['data'][$i];
            }
        }
        return null;
    }

    /**
     * ลบแถวตาม id / Delete row by id
     *
     * @param int $id
     * @return bool ลบสำเร็จหรือไม่ / Did the row exist?
     */
    public function delete(int $id): bool
    {
        foreach ($this->store['data'] as $i => $row) {
            if ((int) $row['id'] === $id) {
                array_splice($this->store['data'], $i, 1);
                $this->writeRaw($this->store);
                return true;
            }
        }
        return false;
    }

    /**
     * นับจำนวนแถว / Count rows
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * ค้นหาด้วยเงื่อนไขหลายอย่าง / Query with multiple conditions
     * @param array $conditions
     * @return array
     */
    public function whereMultiple(array $conditions): array
    {
        if (empty($conditions)) {
            return $this->all();
        }

        $results = [];
        foreach ($this->all() as $row) {
            $match = true;
            foreach ($conditions as $key => $value) {
                if (!isset($row[$key]) || $row[$key] !== $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $results[] = $row;
            }
        }
        return $results;
    }

    /**
     * ค้นหาหนึ่งแถวด้วยเงื่อนไขหลายอย่าง / Find one with conditions
     * @param array $conditions
     * @return array|null
     */
    public function findOne(array $conditions): ?array
    {
        $results = $this->whereMultiple($conditions);
        return $results[0] ?? null;
    }

    // ============================================================
    // Helper: สร้าง UUID v4 / Generate UUID v4
    // ============================================================

    /**
     * สร้าง UUID v4 แบบ random
     * Generate a random UUID v4 string
     *
     * รูปแบบ: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     *
     * @return string
     */
    public static function generateUuid(): string
    {
        // สุ่มไบต์ 16 ตัว / 16 random bytes
        $bytes = random_bytes(16);

        // กำหนด version 4 (เลข hex 4 ใน byte ที่ 7)
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);

        // กำหนด variant (10xx ใน byte ที่ 9)
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        // แปลงเป็น hex แล้วใส่ขีดตามตำแหน่งของ UUID
        $hex = bin2hex($bytes);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    // ============================================================
    // ภายในคลาส: อ่าน/เขียนไฟล์ JSON / Internal file I/O
    // ============================================================

    /**
     * อ่านไฟล์ JSON ทั้งก้อน
     * Read the entire JSON file
     */
    private function readRaw(): array
    {
        $fp = fopen($this->filePath, 'r');
        if (!$fp) {
            throw new \RuntimeException("ไม่สามารถเปิดไฟล์ {$this->filePath} ได้");
        }

        // ล็อกแบบอ่าน / Shared lock for reading
        flock($fp, LOCK_SH);
        $content = stream_get_contents($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            // ถ้าไฟล์เสีย ให้คืนโครงเปล่า / Return empty structure on corrupt file
            return ['_meta' => ['next_id' => 1], 'data' => []];
        }
        return $decoded;
    }

    /**
     * เขียนไฟล์ JSON ทั้งก้อน (atomic)
     * Write the entire JSON file atomically
     */
    private function writeRaw(array $data): void
    {
        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $fp = fopen($this->filePath, 'c+');
        if (!$fp) {
            throw new \RuntimeException("ไม่สามารถเขียนไฟล์ {$this->filePath} ได้");
        }

        // ล็อกแบบเขียน (exclusive) / Exclusive lock for writing
        flock($fp, LOCK_EX);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
