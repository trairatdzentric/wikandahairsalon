<?php
/**
 * ============================================================
 *  Booking.php — โมเดลการจองคิว / Booking Model
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - เก็บโครงสร้างข้อมูลการจองคิวของลูกค้า
 *   - มี constants สำหรับสถานะการจอง
 *   - แปลงจาก/เป็น array สำหรับ JSON storage
 *
 *  ตาราง: bookings.json
 * ============================================================
 */

namespace App\Models;

class Booking
{
    // ------------------------------------------------------------
    // Constants — สถานะการจอง / Booking statuses
    // ------------------------------------------------------------
    public const STATUS_PENDING    = 'pending';
    public const STATUS_CONFIRMED  = 'confirmed';
    public const STATUS_IN_SERVICE = 'in_service';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_CANCELLED  = 'cancelled';

    /** @var array รายการสถานะที่ถูกต้องทั้งหมด / All valid statuses */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_IN_SERVICE,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    // ------------------------------------------------------------
    // Properties / คุณสมบัติ
    // ------------------------------------------------------------
    public int $id;
    public string $uuid;
    public string $booking_code;   // รหัสจอง เช่น WK20260518-006
    public int $member_id;         // FK → users.id (role=member)
    public int $service_id;        // FK → services.id
    public int $staff_id;           // FK → staff.id
    public string $booking_date;   // YYYY-MM-DD
    public string $start_time;     // HH:MM
    public string $end_time;       // HH:MM
    public float $total_price;
    public string $status;
    public ?string $note;
    public string $created_at;
    public string $updated_at;

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
        $b = new self();
        $b->id           = (int) ($row['id'] ?? 0);
        $b->uuid         = (string) ($row['uuid'] ?? '');
        $b->booking_code = (string) ($row['booking_code'] ?? '');
        $b->member_id    = (int) ($row['member_id'] ?? 0);
        $b->service_id   = (int) ($row['service_id'] ?? 0);
        $b->staff_id     = (int) ($row['staff_id'] ?? 0);
        $b->booking_date = (string) ($row['booking_date'] ?? '');
        $b->start_time   = (string) ($row['start_time'] ?? '');
        $b->end_time     = (string) ($row['end_time'] ?? '');
        $b->total_price  = (float) ($row['total_price'] ?? 0.0);
        $b->status       = (string) ($row['status'] ?? self::STATUS_PENDING);
        $b->note         = isset($row['note']) ? (string) $row['note'] : null;
        $b->created_at   = (string) ($row['created_at'] ?? '');
        $b->updated_at   = (string) ($row['updated_at'] ?? '');
        return $b;
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
            'id'           => $this->id,
            'uuid'         => $this->uuid,
            'booking_code' => $this->booking_code,
            'member_id'    => $this->member_id,
            'service_id'   => $this->service_id,
            'staff_id'     => $this->staff_id,
            'booking_date' => $this->booking_date,
            'start_time'   => $this->start_time,
            'end_time'     => $this->end_time,
            'total_price'  => $this->total_price,
            'status'       => $this->status,
            'note'         => $this->note,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }

    /**
     * ตรวจสอบว่าสถานะถูกต้องหรือไม่
     * Check if a status is valid
     *
     * @param string $status
     * @return bool
     */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::STATUSES, true);
    }

    /**
     * ตรวจสอบว่าการจองยังสามารถยกเลิกได้หรือไม่
     * Check if booking can still be cancelled
     *
     * @return bool
     */
    public function canCancel(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED], true);
    }

    /**
     * ตรวจสอบว่าการจองเสร็จสิ้นแล้วหรือไม่
     * Check if booking is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
