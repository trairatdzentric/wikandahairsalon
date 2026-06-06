# Wikanda Hair Salon - โครงสร้างและคำอธิบายโปรเจกต์

## ภาพรวม (Overview)

**Wikanda Hair Salon** เป็น Web Application สำหรับบริหารจัดการร้านเสริมสวย ครอบคลุมระบบจองคิว ชำระเงิน แจ้งเตือน LINE OA และรายงาน Dashboard

**Tech Stack:**

- **Backend:** PHP 8+ (Pure PHP, ไม่ใช้ Framework)
- **Frontend:** HTML + CSS + JavaScript + Bootstrap 5
- **Database:** MySQL (InfinityFree Hosting)
- **Notification:** LINE Messaging API
- **Slip Verification:** Slip2Go API

---

## โครงสร้างโฟลเดอร์ (Folder Structure)

```
Wikanda_Hair_Salon/
│
├── 📄 .htaccess                    ← กฎ Rewrite URL สำหรับ InfinityFree
├── 📄 index.php                    ← Entry Point หลัก (InfinityFree)
├── 📄 dev-router.php               ← Router สำหรับพัฒนาในเครื่อง
├── 📄 temp_hash.php                ← ไฟล์ชั่วคราวสำหรับทดสอบ
├── 📄 README.md                    ← คู่มือเริ่มต้นใช้งาน
│
├── 📁 api/                         ← REST API Endpoints
│   └── v1/
│       ├── auth.php                ← API: Login/Register/Logout
│       ├── bookings.php            ← API: CRUD การจอง
│       ├── payments.php            ← API: การชำระเงิน
│       ├── reviews.php             ← API: รีวิว
│       ├── services.php            ← API: บริการ
│       ├── staff.php               ← API: ช่าง/พนักงาน
│       └── users.php               ← API: ผู้ใช้งาน
│
├── 📁 app/                         ← Application Logic (หัวใจของระบบ)
│   ├── Controllers/                ← รับ HTTP Request → สั่ง Service → ส่ง View
│   │   ├── AdminController.php     ← จัดการส่วน Admin
│   │   ├── AuthController.php      ← จัดการ Login/Register
│   │   ├── BookingController.php   ← จัดการการจอง
│   │   ├── HomeController.php      ← หน้าแรก
│   │   ├── MemberController.php    ← ส่วนสมาชิก
│   │   └── StaffController.php     ← ส่วนพนักงาน
│   │
│   ├── Core/                       ← Framework Core (ไม่แตะต้อง)
│   │   ├── autoload.php            ← Autoloader สำหรับ namespace App\
│   │   ├── Controller.php          ← Base Controller
│   │   ├── Database.php            ← JSON Database (Legacy)
│   │   ├── DatabaseInterface.php   ← Interface สำหรับ Database
│   │   ├── MysqlDatabase.php       ← MySQL Database Implementation
│   │   ├── Request.php             ← จัดการ $_GET/$_POST
│   │   ├── Router.php              ← จับ URL → เรียก Controller
│   │   ├── Session.php             ← จัดการ Session
│   │   └── View.php                ← Render Template
│   │
│   ├── Middleware/                 ← ตัวกรอง Request
│   │   └── AuthMiddleware.php      ← เช็คสิทธิ์ก่อนเข้า Controller
│   │
│   ├── Models/                     ← Entity (โครงสร้างข้อมูล)
│   │   ├── Booking.php             ← โครงสร้างการจอง
│   │   ├── Payment.php             ← โครงสร้างการชำระเงิน
│   │   ├── Review.php              ← โครงสร้างรีวิว
│   │   ├── Service.php             ← โครงสร้างบริการ
│   │   ├── Staff.php               ← โครงสร้างช่าง
│   │   └── User.php                ← โครงสร้างผู้ใช้
│   │
│   ├── Repositories/               ← Data Access Layer
│   │   ├── BaseRepository.php      ← Repository ฐาน (รองรับ JSON/MySQL)
│   │   ├── BookingRepository.php   ← คุยกับตาราง bookings
│   │   ├── PaymentRepository.php   ← คุยกับตาราง payments
│   │   ├── ReviewRepository.php    ← คุยกับตาราง reviews
│   │   ├── ServiceRepository.php   ← คุยกับตาราง services
│   │   ├── SettingRepository.php   ← คุยกับตาราง settings
│   │   ├── StaffRepository.php     ← คุยกับตาราง staff
│   │   └── UserRepository.php        ← คุยกับตาราง users
│   │
│   ├── Services/                   ← Business Logic (ตรรกะธุรกิจ)
│   │   ├── AuthService.php         ← ตรวจสอบสิทธิ์
│   │   ├── BookingService.php      ← จองคิว/ตรวจเวลาซ้อน
│   │   ├── IntegrationSettingsService.php ← ตั้งค่าระบบ
│   │   ├── LineNotifyService.php   ← ส่ง LINE
│   │   ├── PaymentService.php      ← จัดการชำระเงิน
│   │   ├── ReportService.php       ← สร้างรายงาน
│   │   └── Slip2GoService.php      ← ตรวจสอบสลิป
│   │
│   └── Views/                      ← HTML Templates
│       ├── admin/                  ← หน้า Admin
│       ├── auth/                   ← หน้า Login/Register
│       ├── booking/                ← หน้าจองคิว
│       ├── errors/                 ← หน้า Error
│       ├── home/                   ← หน้าแรก
│       ├── layouts/                ← Layout หลัก
│       ├── member/                 ← หน้าสมาชิก
│       ├── partials/               ← ส่วนย่อย (navbar, footer)
│       └── staff/                  ← หน้าพนักงาน
│
├── 📁 config/                      ← ไฟล์ตั้งค่า
│   ├── app.php                     ← ตั้งค่าทั่วไป
│   ├── database.php                ← ตั้งค่า MySQL
│   ├── line.php                    ← LINE API Token
│   ├── routes.php                  ← กำหนด URL → Controller
│   └── slip2go.php                 ← Slip2Go API Key
│
├── 📁 data/                        ← JSON Data (Legacy/Backup)
│   ├── bookings.json
│   ├── payments.json
│   ├── reviews.json
│   ├── services.json
│   ├── settings.json
│   ├── staff.json
│   └── users.json
│
├── 📁 database/                    ← ไฟล์ฐานข้อมูล
│   ├── data_import.sql             ← ข้อมูลตัวอย่างสำหรับ import
│   ├── infinityfree_schema.sql     ← Schema สำหรับ InfinityFree
│   ├── migrate_json_to_mysql.php   ← Script ย้ายข้อมูล JSON → MySQL
│   └── pre_deployment_check.php    ← ตรวจสอบก่อน deploy
│
├── 📁 docs/                        ← เอกสารทั้งหมด
│   ├── diagrams/                   ← แผนผังต่างๆ
│   │   ├── ACTIVITY_FLOW.md
│   │   ├── ER_DIAGRAM.md
│   │   ├── SYSTEM_ARCHITECTURE.md
│   │   ├── USE_CASE_DIAGRAM.md
│   │   └── WIREFRAME.md
│   ├── exports/                    ← PDF สำหรับส่งมอบ
│   │   ├── PLAN.pdf
│   │   ├── PROJECT_ANALYSIS.pdf
│   │   ├── PROJECT_OVERVIEW.pdf
│   │   └── USER_GUIDE.pdf
│   ├── guides/                     ← คู่มือการใช้งาน
│   │   ├── ARCHITECTURE_GUIDE.md
│   │   ├── HANDOFF.md
│   │   ├── INFINITYFREE_DEPLOYMENT.md
│   │   └── USER_GUIDE.md
│   └── plans/                      ← แผนงานโครงการ
│       ├── PLAN.md
│       ├── PROJECT_ANALYSIS.md
│       ├── PROJECT_OVERVIEW.md
│       └── TEST_PLAN.md
│
├── 📁 public/                      ← Web Root (XAMPP)
│   ├── index.php                   ← Entry Point (XAMPP)
│   ├── .htaccess
│   └── assets/
│       ├── css/
│       │   └── style.css
│       ├── images/
│       └── js/
│           └── main.js
│
└── 📁 storage/                     ← ไฟล์ที่สร้างขึ้น
    ├── logs/                       ← Log files
    └── uploads/
        └── slips/                  ← สลิปการโอนเงิน
```

---

## สถาปัตยกรรม (Architecture)

### Clean Architecture Layers

```
┌─────────────────────────────────────────────────────────────┐
│  Presentation Layer                                          │
│  - Controllers: รับ HTTP Request                             │
│  - Views: แสดงผล HTML                                       │
│  - API: คืนค่า JSON                                         │
├─────────────────────────────────────────────────────────────┤
│  Service Layer                                               │
│  - Business Logic (ตรรกะธุรกิจ)                              │
│  - คำนวณราคา, ตรวจสอบเวลา, ส่ง LINE                         │
├─────────────────────────────────────────────────────────────┤
│  Repository Layer                                            │
│  - Data Access (คุยกับ Database)                            │
│  - ซ่อนว่าข้อมูลมาจาก JSON หรือ MySQL                        │
├─────────────────────────────────────────────────────────────┤
│  Model Layer                                                 │
│  - Entity (โครงสร้างข้อมูล)                                 │
│  - ไม่มี logic                                              │
├─────────────────────────────────────────────────────────────┤
│  Persistence Layer                                           │
│  - MySQL Database (InfinityFree)                             │
└─────────────────────────────────────────────────────────────┘
```

---

## Flow การทำงาน (Workflow)

### 1. การจองคิว (Booking Flow)

```
[Client] → HTTP POST /booking/create
              ↓
[BookingController] → รับข้อมูล
              ↓
[BookingService] → ตรวจสอบเวลาซ้อน
              ↓
[BookingRepository] → บันทึกลง MySQL
              ↓
[LineNotifyService] → ส่งแจ้งเตือน
              ↓
[View] → แสดงผลสำเร็จ
```

### 2. การชำระเงิน (Payment Flow)

```
[Client] → อัปโหลดสลิป
              ↓
[PaymentController] → รับไฟล์
              ↓
[Slip2GoService] → ตรวจสอบสลิป
              ↓
[PaymentService] → บันทึกผล
              ↓
[PaymentRepository] → อัปเดตสถานะ
              ↓
[LineNotifyService] → แจ้งเตือนลูกค้า
```

---

## Database Schema

### ตารางหลัก (7 ตาราง)

| ตาราง      | คำอธิบาย         | ความสัมพันธ์                    |
| ---------- | ---------------- | ------------------------------- |
| `users`    | ผู้ใช้งานทั้งหมด | 1:N → bookings, reviews         |
| `services` | บริการของร้าน    | 1:N → bookings, reviews         |
| `staff`    | ข้อมูลช่าง       | 1:N → bookings                  |
| `bookings` | การจองคิว        | N:1 → users, services, staff    |
| `payments` | การชำระเงิน      | N:1 → bookings                  |
| `reviews`  | รีวิวจากลูกค้า   | N:1 → users, services, bookings |
| `settings` | ตั้งค่าระบบ      | -                               |

### ER Diagram

```
users ||--o{ bookings : "makes"
users ||--o{ reviews : "writes"
users ||--o| staff : "is"
services ||--o{ bookings : "booked_in"
services ||--o{ reviews : "reviewed"
staff ||--o{ bookings : "assigned_to"
bookings ||--o{ payments : "has"
bookings ||--o| reviews : "has"
```

---

## การ Deploy ขึ้น InfinityFree

### ขั้นตอน

1. **เตรียม Database**
   - เข้า phpMyAdmin
   - Import `database/infinityfree_schema.sql`
   - Import `database/data_import.sql`

2. **อัปโหลดไฟล์**
   - ไฟล์ใน root → `/htdocs/`
   - โฟลเดอร์ `app/` → `/htdocs/app/`
   - โฟลเดอร์ `config/` → `/htdocs/config/`
   - สร้าง `storage/logs/` และ `storage/uploads/slips/`

3. **ตั้งค่า Permission**
   - `storage/` → 755
   - `storage/logs/` → 755
   - `storage/uploads/slips/` → 755

4. **ทดสอบ**
   - เปิด `https://your-domain.infinityfreeapp.com/`
   - ทดสอบ Login

---

## บทบาทผู้ใช้งาน (User Roles)

| บทบาท      | สิทธิ์                                              |
| ---------- | --------------------------------------------------- |
| **Admin**  | จัดการทุกอย่าง: ผู้ใช้, บริการ, การจอง, การชำระเงิน |
| **Owner**  | ดูรายงาน, จัดการช่าง, ตั้งค่าระบบ                   |
| **Staff**  | ดูการจองของตัวเอง, อัปเดตสถานะงาน                   |
| **Member** | จองคิว, ดูประวัติ, รีวิว, อัปโหลดสลิป               |

---

## API Endpoints

### Authentication

- `POST /api/v1/auth/login` - เข้าสู่ระบบ
- `POST /api/v1/auth/register` - สมัครสมาชิก
- `POST /api/v1/auth/logout` - ออกจากระบบ

### Services

- `GET /api/v1/services` - ดูบริการทั้งหมด
- `GET /api/v1/services/{id}` - ดูบริการเฉพาะ

### Bookings

- `GET /api/v1/bookings` - ดูการจอง
- `POST /api/v1/bookings` - สร้างการจอง
- `PUT /api/v1/bookings/{id}` - อัปเดตการจอง

### Payments

- `GET /api/v1/payments` - ดูการชำระเงิน
- `POST /api/v1/payments` - สร้างการชำระเงิน
- `POST /api/v1/payments/{id}/verify` - ตรวจสอบสลิป

---

## การพัฒนาต่อไป (Next Steps)

### Phase 1: Core (✅ เสร็จแล้ว)

- [x] Database Schema
- [x] Repository Layer (MySQL)
- [x] Migration Script

### Phase 2: Services (⏳ กำลังทำ)

- [ ] AuthService
- [ ] BookingService
- [ ] PaymentService

### Phase 3: Controllers + Views (⏳ รอ)

- [ ] HomeController + View
- [ ] AuthController + View
- [ ] BookingController + View

### Phase 4: API (⏳ รอ)

- [ ] REST API Endpoints
- [ ] API Documentation

### Phase 5: Testing + Deploy (⏳ รอ)

- [ ] Unit Tests
- [ ] Integration Tests
- [ ] Deploy to InfinityFree

---

## เอกสารอ้างอิง

| ไฟล์                                     | คำอธิบาย                        |
| ---------------------------------------- | ------------------------------- |
| `docs/guides/HANDOFF.md`                 | สถานะปัจจุบัน + งานที่ต้องทำต่อ |
| `docs/plans/PLAN.md`                     | แผนการพัฒนาทั้งหมด              |
| `docs/guides/ARCHITECTURE_GUIDE.md`      | อธิบายสถาปัตยกรรมละเอียด        |
| `docs/diagrams/ER_DIAGRAM.md`            | แผนผังฐานข้อมูล                 |
| `docs/guides/INFINITYFREE_DEPLOYMENT.md` | คู่มือ Deploy                   |

---

## ข้อมูลติดต่อ

- **โปรเจกต์:** Wikanda Hair Salon
- **จัดทำ:** [ชื่อผู้จัดทำ]
- **วันที่:** 2026-06-06
- **เวอร์ชัน:** 1.0.0

---

**หมายเหตุ:** เอกสารนี้อัปเดตล่าสุดเมื่อ 2026-06-06
