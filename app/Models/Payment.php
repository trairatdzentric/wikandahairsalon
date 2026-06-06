<?php
/**
 * ============================================================
 *  Payment.php — โมเดลการชำระเงิน / Payment Model
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - เก็บโครงสร้างข้อมูลการชำระเงินของแต่ละการจอง
 *   - มี constants สำหรับประเภทการจ่าย, วิธีการจ่าย, สถานะ
 *   - แปลงจาก/เป็น array สำหรับ JSON storage
 *
 *  ตาราง: payments.json
 * ============================================================
 */

namespace App\Models;

class Payment
{
    // ------------------------------------------------------------
    // Constants — ประเภทการชำระเงิน / Payment types
    // ------------------------------------------------------------
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_FULL    = 'full';

    /** @var array ประเภทที่ถูกต้องทั้งหมด / All valid types */
    public const TYPES = [
        self::TYPE_DEPOSIT,
        self::TYPE_FULL,
    ];

    // ------------------------------------------------------------
    // Constants — วิธีการชำระเงิน / Payment methods
    // ------------------------------------------------------------
    public const METHOD_PROMPTPAY     = 'promptpay';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_CASH          = 'cash';

    /** @var array วิธีการที่ถูกต้องทั้งหมด / All valid methods */
    public const METHODS = [
        self::METHOD_PROMPTPAY,
        self::METHOD_BANK_TRANSFER,
        self::METHOD_CASH,
    ];

    // ------------------------------------------------------------
    // Constants — สถานะการชำระเงิน / Payment statuses
    // ------------------------------------------------------------
    public const STATUS_PENDING  = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    /** @var array สถานะที่ถูกต้องทั้งหมด / All valid statuses */
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_VERIFIED,
        self::STATUS_REJECTED,
    ];

    // ------------------------------------------------------------
    // Properties / คุณสมบัติ
    // ------------------------------------------------------------
    public int $id;
    public string $uuid;
    public int $booking_id;         // FK → bookings.id
    public string $payment_type;     // deposit | full
    public float $amount;
    public string $method;          // promptpay | bank_transfer | cash
    public ?string $slip_image;      // ชื่อไฟล์สลิป (ถ้ามี)
    public ?string $slip2go_ref;     // รหัสอ้างอิง Slip2Go
    public bool $slip2go_verified;   // ผ่านการตรวจสอบ Slip2Go หรือไม่
    public string $status;
    public ?int $verified_by;       // FK → users.id (ผู้ตรวจสอบ)
    public ?string $verified_at;     // เวลาที่ตรวจสอบ
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
        $p = new self();
        $p->id               = (int) ($row['id'] ?? 0);
        $p->uuid             = (string) ($row['uuid'] ?? '');
        $p->booking_id       = (int) ($row['booking_id'] ?? 0);
        $p->payment_type     = (string) ($row['payment_type'] ?? self::TYPE_DEPOSIT);
        $p->amount           = (float) ($row['amount'] ?? 0.0);
        $p->method           = (string) ($row['method'] ?? self::METHOD_CASH);
        $p->slip_image       = isset($row['slip_image']) ? (string) $row['slip_image'] : null;
        $p->slip2go_ref      = isset($row['slip2go_ref']) ? (string) $row['slip2go_ref'] : null;
        $p->slip2go_verified = (bool) ($row['slip2go_verified'] ?? false);
        $p->status           = (string) ($row['status'] ?? self::STATUS_PENDING);
        $p->verified_by      = isset($row['verified_by']) ? (int) $row['verified_by'] : null;
        $p->verified_at      = isset($row['verified_at']) ? (string) $row['verified_at'] : null;
        $p->note             = isset($row['note']) ? (string) $row['note'] : null;
        $p->created_at       = (string) ($row['created_at'] ?? '');
        $p->updated_at       = (string) ($row['updated_at'] ?? '');
        return $p;
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
            'id'               => $this->id,
            'uuid'             => $this->uuid,
            'booking_id'       => $this->booking_id,
            'payment_type'     => $this->payment_type,
            'amount'           => $this->amount,
            'method'           => $this->method,
            'slip_image'       => $this->slip_image,
            'slip2go_ref'      => $this->slip2go_ref,
            'slip2go_verified' => $this->slip2go_verified,
            'status'           => $this->status,
            'verified_by'      => $this->verified_by,
            'verified_at'      => $this->verified_at,
            'note'             => $this->note,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
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
     * ตรวจสอบว่าวิธีการชำระเงินถูกต้องหรือไม่
     * Check if a payment method is valid
     *
     * @param string $method
     * @return bool
     */
    public static function isValidMethod(string $method): bool
    {
        return in_array($method, self::METHODS, true);
    }

    /**
     * ตรวจสอบว่าประเภทการชำระเงินถูกต้องหรือไม่
     * Check if a payment type is valid
     *
     * @param string $type
     * @return bool
     */
    public static function isValidType(string $type): bool
    {
        return in_array($type, self::TYPES, true);
    }

    /**
     * ตรวจสอบว่าการชำระเงินผ่านการยืนยันแล้วหรือไม่
     * Check if payment is verified
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }
}
