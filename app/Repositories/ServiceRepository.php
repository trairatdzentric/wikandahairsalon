<?php

/**
 * ============================================================
 *  ServiceRepository.php — จัดการข้อมูลบริการ / Service Repository
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - CRUD บริการของร้าน (ตัดผม, ทำสี, ดัด, ยืด, etc.)
 *   - ค้นหาตามหมวดหมู่, สถานะ active
 *   - ใช้ร่วมกับ BookingService สำหรับคำนวณราคาและเวลา
 *
 *  ตาราง: services.json
 * ============================================================
 */

namespace App\Repositories;

use App\Core\MysqlDatabase;

class ServiceRepository extends BaseRepository
{
    /** @var string ชื่อตาราง / Table name */
    protected string $table = 'services';

    /**
     * ค้นหาบริการตามหมวดหมู่
     * Find services by category
     *
     * @param string $category
     * @return array
     */
    public function findByCategory(string $category): array
    {
        return $this->where('category', $category);
    }

    /**
     * ค้นหาบริการที่เปิดใช้งานอยู่
     * Find only active services
     *
     * @return array
     */
    public function findActive(): array
    {
        return $this->where('is_active', true);
    }

    /**
     * ค้นหาบริการตามชื่อ (ค้นหาคำบางส่วน)
     * Search services by name (partial match)
     *
     * @param string $keyword
     * @return array
     */
    public function searchByName(string $keyword): array
    {
        // ใช้ SQL LIKE ถ้าเป็น MySQL
        if ($this->dbType === 'mysql' && $this->db instanceof \App\Core\MysqlDatabase) {
            $sql = "SELECT * FROM {$this->table} WHERE name LIKE ? OR name_en LIKE ?";
            return $this->db->query($sql, ['%' . $keyword . '%', '%' . $keyword . '%']);
        }

        $results = [];
        $lowerKeyword = mb_strtolower($keyword);

        foreach ($this->all() as $row) {
            $name    = mb_strtolower($row['name'] ?? '');
            $nameEn  = mb_strtolower($row['name_en'] ?? '');
            if (str_contains($name, $lowerKeyword) || str_contains($nameEn, $lowerKeyword)) {
                $results[] = $row;
            }
        }

        return $results;
    }
}
