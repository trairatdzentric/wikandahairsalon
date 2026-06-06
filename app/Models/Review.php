<?php
/**
 * ============================================================
 *  Review.php — โมเดลรีวิวบริการ / Review Model
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - เก็บโครงสร้างข้อมูลรีวิวจากลูกค้าหลังใช้บริการ
 *   - มี constants สำหรับช่วงคะแนน (1-5)
 *   - แปลงจาก/เป็น array สำหรับ JSON storage
 *
 *  ตาราง: reviews.json
 * ============================================================
 */

namespace App\Models;

class Review
{
    // ------------------------------------------------------------
    // Constants — ช่วงคะแนนรีวิว / Rating range
    // ------------------------------------------------------------
    public const RATING_MIN = 1;
    public const RATING_MAX = 5;

    // ------------------------------------------------------------
    // Properties / คุณสมบัติ
    // ------------------------------------------------------------
    public int $id;
    public string $uuid;
    public int $booking_id;    // FK → bookings.id
    public int $member_id;      // FK → users.id (role=member)
    public int $staff_id;       // FK → staff.id
    public int $rating;         // 1-5
    public string $comment;
    public string $created_at;
    public ?string $updated_at;

    // ------------------------------------------------------------
    // Methods
    // ------------------------------------------------------------

    /**
     * สร้าง instance จาก array (อ่านจาก JSON)
     * Create instance from array (read from JSON)
     *
     * @param array $row ข้อมูลแถวจาก JSON / Row data from JSON
     * @return self
     */
    public static function fromArray(array $row): self
    {
        $r = new self();
        $r->id         = (int) ($row['id'] ?? 0);
        $r->uuid       = (string) ($row['uuid'] ?? '');
        $r->booking_id = (int) ($row['booking_id'] ?? 0);
        $r->member_id  = (int) ($row['member_id'] ?? 0);
        $r->staff_id   = (int) ($row['staff_id'] ?? 0);
        $r->rating     = (int) ($row['rating'] ?? self::RATING_MIN);
        $r->comment    = (string) ($row['comment'] ?? '');
        $r->created_at = (string) ($row['created_at'] ?? '');
        $r->updated_at = isset($row['updated_at']) ? (string) $row['updated_at'] : null;
        return $r;
    }

    /**
     * แปลง instance เป็น array (เขียนลง JSON)
     * Convert instance to array (write to JSON)
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'uuid'       => $this->uuid,
            'booking_id' => $this->booking_id,
            'member_id'  => $this->member_id,
            'staff_id'   => $this->staff_id,
            'rating'     => $this->rating,
            'comment'    => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * ตรวจสอบว่าคะแนนอยู่ในช่วงที่ถูกต้องหรือไม่
     * Check if a rating is within valid range
     *
     * @param int $rating
     * @return bool
     */
    public static function isValidRating(int $rating): bool
    {
        return $rating >= self::RATING_MIN && $rating <= self::RATING_MAX;
    }

    /**
     * คืนค่าคะแนนเป็นดาว (★) สำหรับแสดงผล
     * Get rating as star string for display
     *
     * @return string เช่น "★★★★☆"
     */
    public function stars(): string
    {
        $filled = str_repeat('★', $this->rating);
        $empty  = str_repeat('☆', self::RATING_MAX - $this->rating);
        return $filled . $empty;
    }
}
