<?php
/**
 * ============================================================
 *  User.php — โมเดลผู้ใช้งาน / User Model
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - เก็บโครงสร้างข้อมูลผู้ใช้งานทุกประเภท (admin, owner, staff, member)
 *   - มี constants สำหรับบทบาท (role)
 *   - แปลงจาก/เป็น array สำหรับ JSON storage
 *
 *  ตาราง: users.json
 * ============================================================
 */

namespace App\Models;

class User
{
    // ------------------------------------------------------------
    // Constants — บทบาทผู้ใช้งาน / User roles
    // ------------------------------------------------------------
    public const ROLE_ADMIN  = 'admin';
    public const ROLE_OWNER  = 'owner';
    public const ROLE_STAFF  = 'staff';
    public const ROLE_MEMBER = 'member';

    /** @var array รายการบทบาทที่ถูกต้องทั้งหมด / All valid roles */
    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_OWNER,
        self::ROLE_STAFF,
        self::ROLE_MEMBER,
    ];

    // ------------------------------------------------------------
    // Properties / คุณสมบัติ
    // ------------------------------------------------------------
    public int $id;
    public string $uuid;
    public string $username;
    public string $email;
    public string $password_hash;
    public string $full_name;
    public string $phone;
    public string $role;
    public ?string $line_user_id;
    public ?string $avatar;
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
        $u = new self();
        $u->id            = (int) ($row['id'] ?? 0);
        $u->uuid          = (string) ($row['uuid'] ?? '');
        $u->username      = (string) ($row['username'] ?? '');
        $u->email         = (string) ($row['email'] ?? '');
        $u->password_hash = (string) ($row['password_hash'] ?? '');
        $u->full_name     = (string) ($row['full_name'] ?? '');
        $u->phone         = (string) ($row['phone'] ?? '');
        $u->role          = (string) ($row['role'] ?? self::ROLE_MEMBER);
        $u->line_user_id  = isset($row['line_user_id']) ? (string) $row['line_user_id'] : null;
        $u->avatar        = isset($row['avatar']) ? (string) $row['avatar'] : null;
        $u->created_at    = (string) ($row['created_at'] ?? '');
        $u->updated_at    = (string) ($row['updated_at'] ?? '');
        return $u;
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
            'id'            => $this->id,
            'uuid'          => $this->uuid,
            'username'      => $this->username,
            'email'         => $this->email,
            'password_hash' => $this->password_hash,
            'full_name'     => $this->full_name,
            'phone'         => $this->phone,
            'role'          => $this->role,
            'line_user_id'  => $this->line_user_id,
            'avatar'        => $this->avatar,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }

    /**
     * ตรวจสอบว่าบทบาทถูกต้องหรือไม่
     * Check if a role is valid
     *
     * @param string $role
     * @return bool
     */
    public static function isValidRole(string $role): bool
    {
        return in_array($role, self::ROLES, true);
    }
}
