# Wikanda Hair Salon

ระบบจัดการร้านทำผม Wikanda Hair Salon พัฒนาด้วย Pure PHP 8+ แบบ MVC มีทั้งหน้าเว็บสำหรับลูกค้า/พนักงาน/ผู้ดูแล ระบบจองคิว ระบบชำระเงิน รายงาน และ REST API

Wikanda Hair Salon is a Pure PHP 8+ MVC web application for salon management. It includes customer, staff, and admin pages, booking management, payments, reporting, and REST API endpoints.

## คุณสมบัติหลัก / Key Features

- ระบบสมาชิกและบทบาทผู้ใช้: admin, owner, staff, member
- User authentication and role-based access control: admin, owner, staff, member
- จองคิวบริการทำผม พร้อมเลือกบริการและช่าง
- Hair service booking with service and staff selection
- จัดการบริการ ช่าง ลูกค้า การจอง และการชำระเงิน
- Manage services, staff, customers, bookings, and payments
- ตรวจสอบ/อนุมัติการชำระเงิน และรองรับ Slip2Go API
- Payment verification/approval workflow with Slip2Go integration support
- รองรับ LINE Messaging API สำหรับแจ้งเตือน
- LINE Messaging API support for notifications
- Dashboard และรายงานรายได้/บริการยอดนิยม
- Dashboards and reports for revenue and top services
- รองรับฐานข้อมูล JSON สำหรับ local และ MySQL สำหรับ hosting
- Supports JSON storage for local development and MySQL for hosting

## เทคโนโลยี / Tech Stack

- PHP 8.0+
- Pure PHP MVC architecture
- MySQL / JSON file storage
- Bootstrap 5
- Bootstrap Icons
- Custom CSS/JavaScript
- Apache `.htaccess` rewrite rules

## โครงสร้างโปรเจกต์ / Project Structure

```text
Wikanda_Hair_Salon/
├── api/                 REST API front controller and v1 endpoints
├── app/                 MVC application code
│   ├── Controllers/     Web controllers
│   ├── Core/            Router, View, Database, Session
│   ├── Models/          Data models
│   ├── Repositories/    Data access layer
│   ├── Services/        Business logic
│   └── Views/           PHP view templates
├── config/              Application, database, routes, integrations
├── data/                JSON seed/local data
├── database/            SQL schema and migration tools
├── docs/                Documentation and diagrams
├── public/              Local web root and assets
├── storage/             Logs and uploaded files
├── index.php            Hosting root entry point
└── README.md
```

## การติดตั้งบนเครื่อง / Local Installation

1. วางโฟลเดอร์โปรเจกต์ไว้ใน `htdocs` ของ XAMPP หรือโฟลเดอร์เว็บเซิร์ฟเวอร์

   Place the project folder inside XAMPP `htdocs` or your web server directory.

2. เปิด Apache และเปิด `mod_rewrite`

   Enable Apache and `mod_rewrite`.

3. เข้าใช้งานผ่าน URL นี้

   Open the app with:

```text
http://localhost/Wikanda_Hair_Salon/public/
```

หรือถ้าใช้ dev server ที่โปรเจกต์เตรียมไว้:

Or use the prepared development server route:

```text
http://127.0.0.1:8087/Wikanda_Hair_Salon/public/
```

## การตั้งค่าฐานข้อมูล / Database Configuration

ค่าเริ่มต้นบน local จะใช้ JSON storage จากโฟลเดอร์ `data/` อัตโนมัติ

By default, local development uses JSON storage from the `data/` directory.

สำหรับ hosting ให้ตั้งค่า MySQL ผ่าน environment variables หรือแก้ `config/database.php` บน server เท่านั้น ห้าม commit รหัสผ่านจริงขึ้น GitHub

For hosting, configure MySQL through environment variables or edit `config/database.php` directly on the server. Never commit real passwords to GitHub.

```text
DB_HOST=YOUR_MYSQL_HOST
DB_PORT=3306
DB_DATABASE=YOUR_MYSQL_DATABASE
DB_USERNAME=YOUR_MYSQL_USERNAME
DB_PASSWORD=YOUR_MYSQL_PASSWORD
```

## บัญชีทดสอบ / Demo Accounts

รหัสผ่านของบัญชีตัวอย่างทั้งหมดคือ `password123`

All demo accounts use `password123` as the password.

| Role | Username | Password |
| --- | --- | --- |
| Admin | admin | password123 |
| Owner | owner | password123 |
| Staff | staff01 | password123 |
| Staff | staff02 | password123 |
| Member | member01 | password123 |
| Member | member02 | password123 |

## API Endpoints

Base API URL:

```text
/api/v1
```

| Resource | Methods | Description |
| --- | --- | --- |
| `/auth` | GET, POST | Register, login, logout, current user |
| `/users` | GET, POST, PUT, DELETE | User management |
| `/services` | GET, POST, PUT, DELETE | Service management |
| `/staff` | GET, POST, PUT, DELETE | Staff management |
| `/bookings` | GET, POST, PUT, DELETE | Booking management |
| `/payments` | GET, POST, PUT, DELETE | Payment management |
| `/reviews` | GET, POST, PUT, DELETE | Review management |
| `/reports` | GET | Revenue and operational reports |

## การ Deploy / Deployment

สำหรับ free hosting ที่ใช้ document root เป็น root ของโดเมน ให้อัปโหลดโครงสร้างนี้ขึ้น server:

For free hosting where the domain root is the document root, upload this structure to the server:

```text
.htaccess
index.php
assets/
api/
app/
config/
data/
database/
storage/
```

หลัง migrate ข้อมูลเข้า MySQL สำเร็จแล้ว ให้ลบไฟล์ migration ออกจาก server ทันที

After a successful MySQL migration, remove migration files from the server immediately.

## หมายเหตุความปลอดภัย / Security Notes

- ห้าม commit รหัสผ่านฐานข้อมูล, LINE token, Slip2Go key หรือ secret จริงขึ้น GitHub
- Do not commit database passwords, LINE tokens, Slip2Go keys, or real secrets to GitHub
- โฟลเดอร์ `deploy/`, ไฟล์ zip, debug scripts, และ runtime uploads/logs ถูก ignore แล้ว
- `deploy/`, zip files, debug scripts, and runtime uploads/logs are ignored
- ตั้งค่า integration token ผ่านหน้า Admin Settings หรือ config บน server เท่านั้น
- Configure integration tokens through Admin Settings or server-side config only

## ผู้พัฒนา / Developer

พัฒนาเพื่อโปรเจกต์ Wikanda Hair Salon

Developed for the Wikanda Hair Salon project.