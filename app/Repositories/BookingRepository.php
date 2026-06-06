<?php

/**
 * ============================================================
 *  BookingRepository.php — จัดการข้อมูลการจอง / Booking Repository
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - CRUD การจองคิว
 *   - ค้นหาตาม member_id, staff_id, status, วันที่
 *   - ตรวจสอบช่วงเวลาซ้อนทับ (time conflict)
 *
 *  ตาราง: bookings.json
 * ============================================================
 */

namespace App\Repositories;

class BookingRepository extends BaseRepository
{
    /** @var string ชื่อตาราง / Table name */
    protected string $table = 'bookings';

    /**
     * ค้นหาการจองตามรหัสจอง
     * Find booking by booking code
     *
     * @param string $code เช่น "WK20260518-006"
     * @return array|null
     */
    public function findByBookingCode(string $code): ?array
    {
        $rows = $this->where('booking_code', $code);
        return $rows[0] ?? null;
    }

    /**
     * ค้นหาการจองของสมาชิกคนหนึ่ง
     * Find bookings by member id
     *
     * @param int $memberId
     * @return array
     */
    public function findByMember(int $memberId): array
    {
        return $this->where('member_id', $memberId);
    }

    /**
     * ค้นหาการจองของช่างคนหนึ่ง
     * Find bookings by staff id
     *
     * @param int $staffId
     * @return array
     */
    public function findByStaff(int $staffId): array
    {
        return $this->where('staff_id', $staffId);
    }

    /**
     * ค้นหาการจองตามสถานะ
     * Find bookings by status
     *
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        return $this->where('status', $status);
    }

    /**
     * ค้นหาการจองในวันที่กำหนด
     * Find bookings by date
     *
     * @param string $date รูปแบบ YYYY-MM-DD
     * @return array
     */
    public function findByDate(string $date): array
    {
        return $this->where('booking_date', $date);
    }

    /**
     * ค้นหาการจองของช่างในวันที่กำหนด
     * Find bookings for a staff on a specific date
     *
     * @param int    $staffId
     * @param string $date    YYYY-MM-DD
     * @return array
     */
    public function findByStaffAndDate(int $staffId, string $date): array
    {
        // ใช้ whereMultiple ถ้าเป็น MySQL, ใช้ loop ถ้าเป็น JSON
        if ($this->dbType === 'mysql') {
            return $this->db->whereMultiple([
                'staff_id' => $staffId,
                'booking_date' => $date
            ]);
        }

        $results = [];
        foreach ($this->all() as $row) {
            if (
                (int) ($row['staff_id'] ?? 0) === $staffId
                && ($row['booking_date'] ?? '') === $date
            ) {
                $results[] = $row;
            }
        }
        return $results;
    }

    /**
     * ตรวจสอบว่ามีการจองซ้อนทับในช่วงเวลาหรือไม่
     * Check if there's a time conflict for a staff on a date
     *
     * @param int    $staffId
     * @param string $date      YYYY-MM-DD
     * @param string $startTime HH:MM
     * @param string $endTime   HH:MM
     * @param int|null $excludeId ข้ามการจอง id นี้ (สำหรับแก้ไข)
     * @return bool true = มีซ้อนทับ / has conflict
     */
    public function hasTimeConflict(
        int $staffId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        $bookings = $this->findByStaffAndDate($staffId, $date);

        foreach ($bookings as $row) {
            if ($excludeId !== null && (int) $row['id'] === $excludeId) {
                continue;
            }

            // ข้ามการจองที่ยกเลิกแล้ว
            // Skip cancelled bookings
            if (($row['status'] ?? '') === 'cancelled') {
                continue;
            }

            $existingStart = $row['start_time'] ?? '00:00';
            $existingEnd   = $row['end_time'] ?? '00:00';

            // ตรวจสอบซ้อนทับ: (startA < endB) && (endA > startB)
            if ($startTime < $existingEnd && $endTime > $existingStart) {
                return true;
            }
        }

        return false;
    }
}
