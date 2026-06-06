<?php
/**
 * ============================================================
 *  ReviewRepository.php — จัดการข้อมูลรีวิว / Review Repository
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - CRUD รีวิวจากลูกค้า
 *   - ค้นหาตาม booking_id, member_id, staff_id
 *   - คำนวณคะแนนเฉลี่ยของช่าง
 *
 *  ตาราง: reviews.json
 * ============================================================
 */

namespace App\Repositories;

class ReviewRepository extends BaseRepository
{
    /** @var string ชื่อตาราง / Table name */
    protected string $table = 'reviews';

    /**
     * ค้นหารีวิวตามการจอง
     * Find review by booking id
     *
     * @param int $bookingId
     * @return array|null
     */
    public function findByBooking(int $bookingId): ?array
    {
        $rows = $this->where('booking_id', $bookingId);
        return $rows[0] ?? null;
    }

    /**
     * ค้นหารีวิวของสมาชิกคนหนึ่ง
     * Find reviews by member id
     *
     * @param int $memberId
     * @return array
     */
    public function findByMember(int $memberId): array
    {
        return $this->where('member_id', $memberId);
    }

    /**
     * ค้นหารีวิวของช่างคนหนึ่ง
     * Find reviews by staff id
     *
     * @param int $staffId
     * @return array
     */
    public function findByStaff(int $staffId): array
    {
        if ($this->dbType === 'mysql') {
            return $this->where('staff_id', $staffId);
        }

        $results = [];
        foreach ($this->all() as $row) {
            if ((int) ($row['staff_id'] ?? 0) === $staffId) {
                $results[] = $row;
            }
        }

        return $results;
    }

    /**
     * คำนวณคะแนนเฉลี่ยของช่างคนหนึ่ง
     * Calculate average rating for a staff
     *
     * @param int $staffId
     * @return float คะแนนเฉลี่ย (0-5) / Average rating
     */
    public function averageRatingByStaff(int $staffId): float
    {
        $reviews = $this->findByStaff($staffId);
        if (empty($reviews)) {
            return 0.0;
        }

        $sum = 0;
        foreach ($reviews as $row) {
            $sum += (int) ($row['rating'] ?? 0);
        }

        return round($sum / count($reviews), 2);
    }

    /**
     * นับจำนวนรีวิวของช่างคนหนึ่ง
     * Count reviews for a staff
     *
     * @param int $staffId
     * @return int
     */
    public function countByStaff(int $staffId): int
    {
        return count($this->findByStaff($staffId));
    }
}
