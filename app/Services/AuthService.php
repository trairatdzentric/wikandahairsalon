<?php
/**
 * ============================================================
 *  AuthService.php — บริการยืนยันตัวตน / Authentication Service
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - ลงทะเบียนผู้ใช้ใหม่ (register)
 *   - ตรวจสอบรหัสผ่าน (login)
 *   - จัดการ session (logout)
 *   - hash/verify รหัสผ่าน
 *
 *  ใช้ร่วมกับ: UserRepository, Session (Core)
 * ============================================================
 */

namespace App\Services;

use App\Core\Session;
use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    /** @var UserRepository ที่เก็บข้อมูลผู้ใช้ / User data store */
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * ลงทะเบียนผู้ใช้ใหม่
     * Register a new user
     *
     * @param array $data ข้อมูลผู้ใช้ / User data
     * @return array ['success' => bool, 'user' => array|null, 'message' => string]
     */
    public function register(array $data): array
    {
        // ตรวจสอบข้อมูลจำเป็น / Validate required fields
        $required = ['username', 'email', 'password', 'full_name', 'phone'];
        foreach ($required as $field) {
            if (empty($data[$field] ?? '')) {
                return ['success' => false, 'user' => null, 'message' => "กรุณากรอก {$field}"];
            }
        }

        // ตรวจสอบความยาวรหัสผ่าน / Check password length
        $minLen = (int) (config('app.security.password_min_length') ?? 8);
        if (strlen($data['password']) < $minLen) {
            return [
                'success' => false,
                'user'    => null,
                'message' => "รหัสผ่านต้องมีอย่างน้อย {$minLen} ตัวอักษร",
            ];
        }

        // ตรวจสอบ email ซ้ำ / Check duplicate email
        if ($this->userRepo->emailExists($data['email'])) {
            return ['success' => false, 'user' => null, 'message' => 'อีเมลนี้ถูกใช้งานแล้ว'];
        }

        // ตรวจสอบ username ซ้ำ / Check duplicate username
        if ($this->userRepo->usernameExists($data['username'])) {
            return ['success' => false, 'user' => null, 'message' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว'];
        }

        // สร้างผู้ใช้ / Create user
        $userData = [
            'username'      => $data['username'],
            'email'         => $data['email'],
            'password_hash' => $this->hashPassword($data['password']),
            'full_name'     => $data['full_name'],
            'phone'         => $data['phone'],
            'role'          => $data['role'] ?? User::ROLE_MEMBER,
            'line_user_id'  => $data['line_user_id'] ?? null,
            'avatar'        => $data['avatar'] ?? null,
        ];

        $user = $this->userRepo->create($userData);

        return [
            'success' => true,
            'user'    => $user,
            'message' => 'ลงทะเบียนสำเร็จ',
        ];
    }

    /**
     * เข้าสู่ระบบ
     * Login user
     *
     * @param string $emailOrUsername อีเมลหรือชื่อผู้ใช้
     * @param string $password        รหัสผ่าน
     * @return array ['success' => bool, 'user' => array|null, 'message' => string]
     */
    public function login(string $emailOrUsername, string $password): array
    {
        // ลองหาด้วย email ก่อน / Try email first
        $user = $this->userRepo->findByEmail($emailOrUsername);

        // ถ้าไม่เจอ ลองหาด้วย username / If not found, try username
        if (!$user) {
            $user = $this->userRepo->findByUsername($emailOrUsername);
        }

        if (!$user) {
            return ['success' => false, 'user' => null, 'message' => 'ไม่พบผู้ใช้งาน'];
        }

        // ตรวจสอบรหัสผ่าน / Verify password
        if (!$this->verifyPassword($password, $user['password_hash'])) {
            return ['success' => false, 'user' => null, 'message' => 'รหัสผ่านไม่ถูกต้อง'];
        }

        // สร้าง session / Create session
        Session::set('user_id', $user['id']);
        Session::set('user_uuid', $user['uuid']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['full_name']);

        return [
            'success' => true,
            'user'    => $user,
            'message' => 'เข้าสู่ระบบสำเร็จ',
        ];
    }

    /**
     * ออกจากระบบ
     * Logout user
     *
     * @return void
     */
    public function logout(): void
    {
        Session::destroy();
    }

    /**
     * ตรวจสอบว่าผู้ใช้เข้าสู่ระบบอยู่หรือไม่
     * Check if user is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return Session::has('user_id');
    }

    /**
     * คืนข้อมูลผู้ใช้ที่เข้าสู่ระบบอยู่
     * Get current logged-in user
     *
     * @return array|null
     */
    public function currentUser(): ?array
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }
        return $this->userRepo->find((int) $userId);
    }

    /**
     * ตรวจสอบบทบาทของผู้ใช้ปัจจุบัน
     * Check if current user has a specific role
     *
     * @param string|array $roles บทบาทที่ต้องการตรวจสอบ
     * @return bool
     */
    public function hasRole(string|array $roles): bool
    {
        $currentRole = Session::get('user_role');
        if (!$currentRole) {
            return false;
        }

        if (is_string($roles)) {
            return $currentRole === $roles;
        }

        return in_array($currentRole, $roles, true);
    }

    /**
     * hash รหัสผ่านด้วย bcrypt
     * Hash password with bcrypt
     *
     * @param string $password รหัสผ่านต้นฉบับ / Plain password
     * @return string รหัสผ่านที่ hash แล้ว / Hashed password
     */
    public function hashPassword(string $password): string
    {
        $cost = (int) (config('app.security.bcrypt_cost') ?? 10);
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    /**
     * ตรวจสอบรหัสผ่านกับ hash
     * Verify password against hash
     *
     * @param string $password     รหัสผ่านต้นฉบับ / Plain password
     * @param string $passwordHash hash ที่เก็บไว้ / Stored hash
     * @return bool
     */
    public function verifyPassword(string $password, string $passwordHash): bool
    {
        return password_verify($password, $passwordHash);
    }
}
