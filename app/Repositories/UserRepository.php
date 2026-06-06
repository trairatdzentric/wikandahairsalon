<?php
/**
 * ============================================================
 *  UserRepository.php — จัดการข้อมูลผู้ใช้งาน / User Repository
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - CRUD ผู้ใช้งานทุกประเภท (admin, owner, staff, member)
 *   - ค้นหาด้วย email, username, role
 *   - ใช้ร่วมกับ AuthService สำหรับ login/register
 *
 *  ตาราง: users.json
 * ============================================================
 */

namespace App\Repositories;

class UserRepository extends BaseRepository
{
    /** @var string ชื่อตาราง / Table name */
    protected string $table = 'users';

    /**
     * ค้นหาผู้ใช้ตาม email
     * Find user by email
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $rows = $this->where('email', $email);
        return $rows[0] ?? null;
    }

    /**
     * ค้นหาผู้ใช้ตาม username
     * Find user by username
     *
     * @param string $username
     * @return array|null
     */
    public function findByUsername(string $username): ?array
    {
        $rows = $this->where('username', $username);
        return $rows[0] ?? null;
    }

    /**
     * ค้นหาผู้ใช้ตามบทบาท
     * Find users by role
     *
     * @param string $role เช่น 'staff', 'member' / e.g. 'staff', 'member'
     * @return array
     */
    public function findByRole(string $role): array
    {
        return $this->where('role', $role);
    }

    /**
     * ค้นหาผู้ใช้ตาม LINE user ID
     * Find user by LINE user ID
     *
     * @param string $lineUserId
     * @return array|null
     */
    public function findByLineUserId(string $lineUserId): ?array
    {
        $rows = $this->where('line_user_id', $lineUserId);
        return $rows[0] ?? null;
    }

    /**
     * ตรวจสอบว่า email ซ้ำหรือไม่
     * Check if email already exists
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    /**
     * ตรวจสอบว่า username ซ้ำหรือไม่
     * Check if username already exists
     *
     * @param string $username
     * @return bool
     */
    public function usernameExists(string $username): bool
    {
        return $this->findByUsername($username) !== null;
    }
}
