# Wikanda Hair Salon

ระบบจัดการร้านทำผม Wikanda Hair Salon — พัฒนาด้วย PHP 8+ แบบ Pure (ไม่ใช้ Framework)

## ความต้องการของระบบ / Requirements

- PHP 8.0 ขึ้นไป
- Apache + mod_rewrite (หรือ Nginx กับ config ที่เทียบเท่า)
- XAMPP (แนะนำสำหรับ Windows)

## วิธีติดตั้ง / Installation

### 1. วางโปรเจกต์ใน htdocs

```bash
# ถ้าใช้ XAMPP บน Windows
# คัดลอกโฟลเดอร์ Wikanda_Hair_Salon ไปวางที่ C:\xampp\htdocs\
```

### 2. ตั้งค่า Apache

ตรวจสอบว่า `mod_rewrite` เปิดใช้งานแล้วใน `httpd.conf`:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

และใน `<Directory>` ของ htdocs ให้ `AllowOverride All`:

```apache
<Directory "C:/xampp/htdocs">
    AllowOverride All
    Require all granted
</Directory>
```

### 3. สร้างโฟลเดอร์ที่จำเป็น

```bash
# สร้างโฟลเดอร์สำหรับเก็บ log และ uploads
mkdir storage/logs
mkdir storage/uploads
mkdir storage/uploads/slips
```

### 4. ตั้งค่า config (ถ้าจำเป็น)

แก้ไขไฟล์ `config/app.php` ให้ตรงกับ environment:

```php
'base_url' => 'http://localhost/Wikanda_Hair_Salon/public',
'api_url'  => 'http://localhost/Wikanda_Hair_Salon/api',
```

### 5. เข้าใช้งาน

- **หน้าเว็บ:** http://localhost/Wikanda_Hair_Salon/public/
- **API:** http://localhost/Wikanda_Hair_Salon/api/v1/

## บัญชีทดสอบ / Test Accounts

| บทบาท  | ชื่อผู้ใช้ | รหัสผ่าน    |
| ------ | ---------- | ----------- |
| Admin  | admin      | password123 |
| Owner  | owner      | password123 |
| Staff  | staff01    | password123 |
| Staff  | staff02    | password123 |
| Member | member01   | password123 |
| Member | member02   | password123 |

## โครงสร้างโปรเจกต์ / Project Structure

```
Wikanda_Hair_Salon/
├── api/                    # REST API endpoints
│   ├── index.php           # API Front Controller
│   └── v1/                 # API version 1
│       ├── auth.php
│       ├── bookings.php
│       ├── payments.php
│       ├── reports.php
│       ├── reviews.php
│       ├── services.php
│       ├── staff.php
│       └── users.php
├── app/
│   ├── Controllers/        # Web Controllers
│   ├── Core/               # Framework Core (Router, View, Database, etc.)
│   ├── Models/             # Data Models
│   ├── Repositories/       # Data Access Layer
│   ├── Services/           # Business Logic
│   └── Views/              # HTML Templates
├── config/                 # Configuration files
├── data/                   # JSON database files
├── public/                 # Web root
│   ├── assets/
│   │   ├── css/style.css   # Lovable style
│   │   └── js/main.js      # Frontend JS
│   ├── .htaccess
│   └── index.php           # Web Front Controller
└── storage/                # Uploads & logs
```

## API Endpoints

### Authentication

- `POST /api/v1/auth` — register / login / logout
- `GET /api/v1/auth/me` — current user

### Users

- `GET /api/v1/users` — list users (admin/owner)
- `GET /api/v1/users/{id}` — get user
- `POST /api/v1/users` — create user (admin/owner)
- `PUT /api/v1/users/{id}` — update user
- `DELETE /api/v1/users/{id}` — delete user (admin/owner)

### Services

- `GET /api/v1/services` — list services
- `GET /api/v1/services/{id}` — get service
- `POST /api/v1/services` — create service (admin/owner)
- `PUT /api/v1/services/{id}` — update service (admin/owner)
- `DELETE /api/v1/services/{id}` — delete service (admin/owner)

### Bookings

- `GET /api/v1/bookings` — list bookings
- `GET /api/v1/bookings/{id}` — get booking
- `POST /api/v1/bookings` — create booking
- `PUT /api/v1/bookings/{id}` — update booking
- `PUT /api/v1/bookings/{id}/cancel` — cancel booking
- `DELETE /api/v1/bookings/{id}` — delete booking (admin/owner)

### Payments

- `GET /api/v1/payments` — list payments
- `GET /api/v1/payments/{id}` — get payment
- `POST /api/v1/payments` — create payment
- `PUT /api/v1/payments/{id}/verify` — verify with Slip2Go
- `PUT /api/v1/payments/{id}/approve` — manual approve (admin/owner)
- `PUT /api/v1/payments/{id}/reject` — reject (admin/owner)
- `DELETE /api/v1/payments/{id}` — delete payment (admin/owner)

### Reports (admin/owner)

- `GET /api/v1/reports/revenue/day?date=YYYY-MM-DD`
- `GET /api/v1/reports/revenue/month?month=YYYY-MM`
- `GET /api/v1/reports/revenue/year?year=YYYY`
- `GET /api/v1/reports/top-services?limit=5`
- `GET /api/v1/reports/today`

## สไตล์ UI / UI Style

- **Font:** Plus Jakarta Sans
- **สีหลัก:** Gradient ชมพู-ม่วง (#FFB6D5 → #C8A8FF)
- **พื้นหลัง:** ครีม (#FFF8F3)
- **การ์ด:** Glassmorphism + soft shadow
- **ปุ่ม:** Gradient + hover lift
- **Framework:** Bootstrap 5

## หมายเหตุ / Notes

- ฐานข้อมูลใช้ JSON files (อยู่ใน `data/`) — สามารถย้ายไป MySQL ได้ในอนาคตโดยแก้แค่ `Core/Database.php`
- LINE Messaging API และ Slip2Go ปิดใช้งานโดยค่าเริ่มต้น (ตั้งค่าใน `config/line.php` และ `config/slip2go.php`)
- ไม่รองรับ Docker — ใช้ XAMPP เท่านั้น

## ผู้พัฒนา / Developer

พัฒนาโดย AI Assistant สำหรับ Wikanda Hair Salon
