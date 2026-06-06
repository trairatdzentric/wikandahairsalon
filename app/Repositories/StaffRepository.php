<?php

/**
 * ============================================================
 *  StaffRepository.php — จัดการข้อมูลช่าง/พนักงาน / Staff Repository
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - CRUD ข้อมูลช่าง/พนักงาน (เชื่อมกับ users ผ่าน user_id)
 *   - ค้นหาตาม user_id, สถานะ active
 *   - ใช้ร่วมกับ BookingService สำหรับตรวจสอบวันทำงาน
 *
 *  ตาราง: staff.json
 * ============================================================
 */

namespace App\Repositories;

use App\Core\MysqlDatabase;

class StaffRepository extends BaseRepository
{
    /** @var string ชื่อตาราง / Table name */
    protected string $table = 'staff';

    /**
     * ค้นหาช่างตาม user_id
     * Find staff by user_id
     *
     * @param int $userId
     * @return array|null
     */
    public function findByUserId(int $userId): ?array
    {
        $rows = $this->where('user_id', $userId);
        return $rows[0] ?? null;
    }

    /**
     * ค้นหาช่างที่เปิดใช้งานอยู่
     * Find only active staff
     *
     * @return array
     */
    public function findActive(): array
    {
        return $this->where('is_active', true);
    }

    /**
     * ค้นหาช่างตามความเชี่ยวชาญ (ค้นหาคำบางส่วน)
     * Search staff by specialty (partial match)
     *
     * @param string $keyword
     * @return array
     */
    public function searchBySpecialty(string $keyword): array
    {
        // ใช้ SQL LIKE ถ้าเป็น MySQL
        if ($this->dbType === 'mysql' && $this->db instanceof \App\Core\MysqlDatabase) {
            $sql = "SELECT * FROM {$this->table} WHERE specialty LIKE ?";
            return $this->db->query($sql, ['%' . $keyword . '%']);
        }

        $results = [];
        $lowerKeyword = mb_strtolower($keyword);

        foreach ($this->all() as $row) {
            $specialty = mb_strtolower($row['specialty'] ?? '');
            if (str_contains($specialty, $lowerKeyword)) {
                $results[] = $row;
            }
        }

        return $results;
    }
}
