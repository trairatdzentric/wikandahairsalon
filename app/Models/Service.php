<?php
/**
 * ============================================================
 *  Service.php — โมเดลบริการ / Service Model
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - เก็บโครงสร้างข้อมูลบริการของร้าน (ตัดผม, ทำสี, ดัด, ยืด, etc.)
 *   - มี constants สำหรับหมวดหมู่บริการ
 *   - แปลงจาก/เป็น array สำหรับ JSON storage
 *
 *  ตาราง: services.json
 * ============================================================
 */

namespace App\Models;

class Service
{
    // ------------------------------------------------------------
    // Constants — หมวดหมู่บริการ / Service categories
    // ------------------------------------------------------------
    public const CATEGORY_HAIRCUT    = 'haircut';
    public const CATEGORY_WASH       = 'wash';
    public const CATEGORY_COLOR      = 'color';
    public const CATEGORY_PERM       = 'perm';
    public const CATEGORY_STRAIGHTEN = 'straighten';
    public const CATEGORY_TREATMENT  = 'treatment';
    public const CATEGORY_STYLING    = 'styling';
    public const CATEGORY_OTHER      = 'other';

    /** @var array รายการหมวดหมู่ที่ถูกต้องทั้งหมด / All valid categories */
    public const CATEGORIES = [
        self::CATEGORY_HAIRCUT,
        self::CATEGORY_WASH,
        self::CATEGORY_COLOR,
        self::CATEGORY_PERM,
        self::CATEGORY_STRAIGHTEN,
        self::CATEGORY_TREATMENT,
        self::CATEGORY_STYLING,
        self::CATEGORY_OTHER,
    ];

    // ------------------------------------------------------------
    // Properties / คุณสมบัติ
    // ------------------------------------------------------------
    public int $id;
    public string $uuid;
    public string $name;           // ชื่อภาษาไทย
    public string $name_en;         // ชื่อภาษาอังกฤษ
    public string $description;
    public float $price;
    public int $duration_minutes;  // ระยะเวลาโดยประมาณ (นาที)
    public string $category;
    public ?string $image;         // ชื่อไฟล์รูปภาพ
    public bool $is_active;
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
        $s = new self();
        $s->id               = (int) ($row['id'] ?? 0);
        $s->uuid             = (string) ($row['uuid'] ?? '');
        $s->name             = (string) ($row['name'] ?? '');
        $s->name_en          = (string) ($row['name_en'] ?? '');
        $s->description      = (string) ($row['description'] ?? '');
        $s->price            = (float) ($row['price'] ?? 0.0);
        $s->duration_minutes = (int) ($row['duration_minutes'] ?? 0);
        $s->category         = (string) ($row['category'] ?? self::CATEGORY_OTHER);
        $s->image            = isset($row['image']) ? (string) $row['image'] : null;
        $s->is_active        = (bool) ($row['is_active'] ?? true);
        $s->created_at       = (string) ($row['created_at'] ?? '');
        $s->updated_at       = isset($row['updated_at']) ? (string) $row['updated_at'] : null;
        return $s;
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
            'name'             => $this->name,
            'name_en'          => $this->name_en,
            'description'      => $this->description,
            'price'            => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'category'         => $this->category,
            'image'            => $this->image,
            'is_active'        => $this->is_active,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }

    /**
     * ตรวจสอบว่าหมวดหมู่ถูกต้องหรือไม่
     * Check if a category is valid
     *
     * @param string $category
     * @return bool
     */
    public static function isValidCategory(string $category): bool
    {
        return in_array($category, self::CATEGORIES, true);
    }
}
