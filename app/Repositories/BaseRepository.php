<?php

/**
 * ============================================================
 *  BaseRepository.php — คลาสฐานของ Repository ทุกตัว
 *                        Base Repository Class
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - ห่อหุ้ม Core\Database ให้ใช้งานง่ายขึ้น
 *   - มี CRUD พื้นฐานที่ Repository ลูกสืบทอดไปใช้
 *   - ซ่อนรายละเอียดการอ่าน/เขียน JSON จาก Service layer
 *   - รองรับทั้ง JSON และ MySQL database
 *
 *  วิธีใช้ / Usage:
 *     class UserRepository extends BaseRepository {
 *         protected string $table = 'users';
 *     }
 * ============================================================
 */

namespace App\Repositories;

use App\Core\Database;
use App\Core\MysqlDatabase;
use App\Core\DatabaseInterface;

abstract class BaseRepository
{
    /** @var DatabaseInterface ตัวจัดการฐานข้อมูล / Database handler */
    protected DatabaseInterface $db;

    /** @var string ชื่อตาราง / Table name */
    protected string $table;

    /** @var string ประเภทฐานข้อมูล ('json' หรือ 'mysql') */
    protected string $dbType;

    /**
     * สร้าง instance พร้อมเชื่อมต่อฐานข้อมูล
     * Create instance with database connection
     */
    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $this->dbType = $config['driver'] ?? 'json';

        // ใช้ MysqlDatabase ถ้า driver เป็น mysql
        if ($this->dbType === 'mysql') {
            $this->db = new MysqlDatabase($this->table);
        } else {
            $this->db = new Database($this->table);
        }
    }

    /**
     * คืนข้อมูลทุกแถว / Get all rows
     *
     * @return array
     */
    public function all(): array
    {
        return $this->db->all();
    }

    /**
     * ค้นหาแถวตาม id / Find row by id
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        return $this->db->find($id);
    }

    /**
     * ค้นหาแถวตาม UUID / Find row by UUID
     *
     * @param string $uuid
     * @return array|null
     */
    public function findByUuid(string $uuid): ?array
    {
        return $this->db->findByUuid($uuid);
    }

    /**
     * ค้นหาหลายแถวด้วยเงื่อนไข key=value / Query rows by condition
     *
     * @param string $key
     * @param mixed  $value
     * @return array
     */
    public function where(string $key, mixed $value): array
    {
        return $this->db->where($key, $value);
    }

    /**
     * สร้างแถวใหม่ / Create new row
     *
     * @param array $data
     * @return array แถวที่สร้างแล้ว (รวม id, uuid) / Created row
     */
    public function create(array $data): array
    {
        return $this->db->insert($data);
    }

    /**
     * แก้ไขแถวตาม id / Update row by id
     *
     * @param int   $id
     * @param array $data
     * @return array|null
     */
    public function update(int $id, array $data): ?array
    {
        return $this->db->update($id, $data);
    }

    /**
     * ลบแถวตาม id / Delete row by id
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($id);
    }

    /**
     * นับจำนวนแถวทั้งหมด / Count all rows
     *
     * @return int
     */
    public function count(): int
    {
        return $this->db->count();
    }
}
