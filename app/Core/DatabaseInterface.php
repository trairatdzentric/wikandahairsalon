<?php

/**
 * ============================================================
 *  DatabaseInterface.php — อินเตอร์เฟซสำหรับ Database
 * ============================================================
 *
 *  กำหนด contract ที่ Database ทุกประเภทต้อง implement
 *  รองรับทั้ง JSON และ MySQL database
 * ============================================================
 */

namespace App\Core;

interface DatabaseInterface
{
    /**
     * คืนข้อมูลทุกแถว / Get all rows
     * @return array
     */
    public function all(): array;

    /**
     * ค้นหาแถวตาม id / Find row by id
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array;

    /**
     * ค้นหาแถวตาม UUID / Find row by UUID
     * @param string $uuid
     * @return array|null
     */
    public function findByUuid(string $uuid): ?array;

    /**
     * ค้นหาหลายแถวด้วยเงื่อนไข / Query rows by condition
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public function where(string $key, mixed $value): array;

    /**
     * สร้างแถวใหม่ / Create new row
     * @param array $data
     * @return array
     */
    public function insert(array $data): array;

    /**
     * แก้ไขแถวตาม id / Update row by id
     * @param int $id
     * @param array $data
     * @return array|null
     */
    public function update(int $id, array $data): ?array;

    /**
     * ลบแถวตาม id / Delete row by id
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * นับจำนวนแถวทั้งหมด / Count all rows
     * @return int
     */
    public function count(): int;

    /**
     * ค้นหาด้วยเงื่อนไขหลายอย่าง / Query with multiple conditions
     * @param array $conditions
     * @return array
     */
    public function whereMultiple(array $conditions): array;

    /**
     * ค้นหาหนึ่งแถวด้วยเงื่อนไขหลายอย่าง / Find one with conditions
     * @param array $conditions
     * @return array|null
     */
    public function findOne(array $conditions): ?array;
}
