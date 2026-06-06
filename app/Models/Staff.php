<?php
/**
 * ============================================================
 *  Staff.php — โมเดลช่าง/พนักงาน / Staff Model
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - เก็บโครงสร้างข้อมูลช่าง/พนักงาน (เชื่อมกับ users ผ่าน user_id)
 *   - มีข้อมูลความเชี่ยวชาญ, ประสบการณ์, วันทำงาน
 *   - แปลงจาก/เป็น array สำหรับ JSON storage
 *
 *  ตาราง: staff.json
 * ============================================================
 */

namespace App\Models;

class Staff
{
    // ------------------------------------------------------------
    // Properties / คุณสมบัติ
    // ------------------------------------------------------------
    public int $id;
    public string $uuid;
    public int $user_id;            // FK → users.id (role=staff)
    public string $display_name;    // ชื่อที่แสดงในระบบจอง
    public string $specialty;       // ความเชี่ยวชาญ เช่น "ตัดผม, ทำสี"
    public int $experience_years;   // ประสบการณ์ (ปี)
    public string $bio;            // ประวัติสั้น ๆ
    public ?string $avatar;        // ชื่อไฟล์รูปประจำตัว

    /** @var array วันทำงาน เช่น ["mon","tue","wed"] / Working days */
    public array $working_days;

    /** @var array เวลาทำงาน เช่น {"start":"10:00","end":"20:00"} / Working hours */
    public array $working_hours;

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
        $s->user_id          = (int) ($row['user_id'] ?? 0);
        $s->display_name     = (string) ($row['display_name'] ?? '');
        $s->specialty        = (string) ($row['specialty'] ?? '');
        $s->experience_years = (int) ($row['experience_years'] ?? 0);
        $s->bio              = (string) ($row['bio'] ?? '');
        $s->avatar           = isset($row['avatar']) ? (string) $row['avatar'] : null;
        $s->working_days     = (array) ($row['working_days'] ?? []);
        $s->working_hours    = (array) ($row['working_hours'] ?? ['start' => '10:00', 'end' => '20:00']);
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
            'user_id'          => $this->user_id,
            'display_name'     => $this->display_name,
            'specialty'        => $this->specialty,
            'experience_years' => $this->experience_years,
            'bio'              => $this->bio,
            'avatar'           => $this->avatar,
            'working_days'     => $this->working_days,
            'working_hours'    => $this->working_hours,
            'is_active'        => $this->is_active,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }

    /**
     * ตรวจสอบว่าวันที่กำหนดเป็นวันทำงานหรือไม่
     * Check if a given day is a working day
     *
     * @param string $day ชื่อวัน เช่น "mon", "tue" / Day name e.g. "mon"
     * @return bool
     */
    public function isWorkingDay(string $day): bool
    {
        return in_array(strtolower($day), $this->working_days, true);
    }
}
