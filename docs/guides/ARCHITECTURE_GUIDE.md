# Wikanda Hair Salon - Architecture Guide

## โครงสร้างโปรเจกต์ (Project Structure)

```
Wikanda_Hair_Salon/
├── api/                    ← REST API Endpoints (คืนค่า JSON)
│   └── v1/
│       ├── auth.php
│       ├── bookings.php
│       ├── services.php
│       └── ...
│
├── app/                    ← Application Logic
│   ├── Controllers/        ← รับ HTTP Request → เรียก Service → ส่ง View
│   ├── Core/               ← Framework Core (Router, Database, etc.)
│   ├── Middleware/         ← ตัวกรอง Request (Auth, etc.)
│   ├── Models/             ← โครงสร้างข้อมูล (Entity)
│   ├── Repositories/       ← คุยกับ Database
│   ├── Services/           ← Business Logic (ตรรกะธุรกิจ)
│   └── Views/              ← HTML Templates
│
├── config/                 ← Configuration files
├── public/                 ← Web root (assets)
└── storage/                ← Logs, uploads
```

---

## ความแตกต่าง: `app/Services/` vs `api/`

| หัวข้อ          | `app/Services/`                  | `api/`                            |
| --------------- | -------------------------------- | --------------------------------- |
| **คืออะไร**     | PHP Classes เก็บตรรกะธุรกิจ      | PHP Endpoints รับ HTTP Request    |
| **ทำอะไร**      | คำนวณราคา, ตรวจสอบเวลา, ส่ง LINE | รับข้อมูลจาก client, คืน JSON     |
| **ถูกเรียกโดย** | Controllers หรือ API             | Client (Browser, Mobile, Postman) |
| **คืนค่า**      | Array, Object, Boolean           | JSON Response                     |
| **ตัวอย่าง**    | `AuthService::login()`           | `POST /api/v1/auth/login`         |

---

## Flow การทำงาน

```
┌─────────────────────────────────────────────────────────────┐
│  Client (Browser/Mobile App)                                │
│  - ส่ง HTTP Request                                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  api/v1/xxx.php หรือ app/Controllers/XXXController.php      │
│  - รับ HTTP Request                                         │
│  - ตรวจสอบ input                                            │
│  - เรียก Service                                            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  app/Services/XXXService.php                                │
│  - Business Logic (ตรรกะธุรกิจ)                              │
│  - คำนวณราคา, ตรวจสอบเวลาซ้อนทับ, ส่ง LINE                  │
│  - ไม่รู้จัก HTTP หรือ JSON                                 │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  app/Repositories/XXXRepository.php                           │
│  - Data Access Layer                                        │
│  - คุยกับ Database (MySQL)                                  │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  MySQL Database                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## ตัวอย่างโค้ด

### 1. Service (Business Logic)

**ไฟล์:** `app/Services/AuthService.php`

```php
<?php
namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * ตรวจสอบการเข้าสู่ระบบ
     * @param string $email
     * @param string $password
     * @return array|null ข้อมูลผู้ใช้ถ้าสำเร็จ, null ถ้าล้มเหลว
     */
    public function login(string $email, string $password): ?array
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        // สร้าง session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        return $user;
    }
}
```

**คืนค่า:** Array หรือ null (ไม่ใช่ JSON!)

---

### 2. API Endpoint

**ไฟล์:** `api/v1/auth.php`

```php
<?php
/**
 * API Endpoint: Authentication
 * POST /api/v1/auth/login
 */

require_once __DIR__ . '/../../app/Core/autoload.php';

use App\Services\AuthService;

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$service = new AuthService();

if ($method === 'POST' && $action === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $service->login($email, $password);

    if ($user) {
        echo json_encode([
            'success' => true,
            'data' => $user
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid credentials'
        ]);
    }
}
```

**คืนค่า:** JSON Response

---

### 3. Controller (Web)

**ไฟล์:** `app/Controllers/AuthController.php`

```php
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * แสดงหน้า login
     */
    public function loginForm(): void
    {
        $this->view('auth/login');
    }

    /**
     * จัดการการ login (POST)
     */
    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->authService->login($email, $password);

        if ($user) {
            header('Location: /dashboard');
            exit;
        } else {
            $this->view('auth/login', ['error' => 'Invalid credentials']);
        }
    }
}
```

**คืนค่า:** HTML (ผ่าน View)

---

## สรุปหลักการสำคัญ

1. **Service ไม่รู้จัก HTTP** - ทำงานกับข้อมูลล้วน ไม่สนใจว่าถูกเรียกจากไหน
2. **API รู้จัก HTTP** - รับ Request, คืน JSON
3. **Controller รู้จัก HTTP** - รับ Request, คืน HTML
4. **Service ถูกใช้ซ้ำได้** - ทั้ง API และ Controller เรียกใช้ Service เดียวกัน

---

## ตารางสรุปการใช้งาน

| ถ้าต้องการ...             | สร้างที่...                          | คืนค่า...    |
| ------------------------- | ------------------------------------ | ------------ |
| สร้าง REST API (คืน JSON) | `api/v1/xxx.php`                     | JSON         |
| สร้างหน้าเว็บ (แสดง HTML) | `app/Controllers/XXXController.php`  | HTML         |
| เขียน Business Logic      | `app/Services/XXXService.php`        | Array/Object |
| เขียนโครงสร้างข้อมูล      | `app/Models/XXX.php`                 | -            |
| เขียนคุยกับ Database      | `app/Repositories/XXXRepository.php` | Array        |

---

**เอกสารนี้อัปเดตล่าสุด:** 2026-06-06
