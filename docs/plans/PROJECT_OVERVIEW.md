# 📋 อธิบายโปรเจกต์ / Project Overview

## Wikanda Hair Salon — ระบบจัดการร้านทำผม

---

## 🎯 วัตถุประสงค์ / Objectives

โปรเจกต์นี้พัฒนาเพื่อช่วยร้านทำผม **Wikanda Hair Salon** จัดการการจองคิว การชำระเงิน และข้อมูลลูกค้าแบบครบวงจร โดยไม่ต้องพึ่ง Framework หนัก ๆ ใช้ PHP แบบ Pure ร่วมกับ JSON เป็นฐานข้อมูล

---

## 🏗️ สถาปัตยกรรม / Architecture

โปรเจกต์ใช้สถาปัตยกรรมแบบ **Layered Architecture** แบ่งเป็น 5 ชั้น:

```
┌─────────────────────────────────────────┐
│  Presentation Layer (Views + Public)    │  ← HTML, CSS, JS
├─────────────────────────────────────────┤
│  Controller Layer (Web + API)           │  ← รับ Request, ส่ง Response
├─────────────────────────────────────────┤
│  Service Layer (Business Logic)         │  ← ตรวจสอบ, คำนวณ, จัดการ
├─────────────────────────────────────────┤
│  Repository Layer (Data Access)         │  ← อ่าน/เขียน JSON
├─────────────────────────────────────────┤
│  Model Layer (Data Structure)           │  ← โครงสร้างข้อมูล
└─────────────────────────────────────────┘
```

---

## 🧩 ส่วนประกอบหลัก / Core Components

### 1. Models (โมเดลข้อมูล)

เก็บโครงสร้างข้อมูลและ Constants:

| ไฟล์          | หน้าที่                                          |
| ------------- | ------------------------------------------------ |
| `User.php`    | ผู้ใช้งานทุกประเภท (admin, owner, staff, member) |
| `Service.php` | บริการของร้าน (ตัดผม, ทำสี, ดัด, ยืด)            |
| `Staff.php`   | ข้อมูลช่าง/พนักงาน                               |
| `Booking.php` | การจองคิว                                        |
| `Payment.php` | การชำระเงิน                                      |
| `Review.php`  | รีวิวจากลูกค้า                                   |

### 2. Repositories (การเข้าถึงข้อมูล)

ห่อหุ้มการอ่าน/เขียน JSON:

| ไฟล์                    | หน้าที่                                     |
| ----------------------- | ------------------------------------------- |
| `BaseRepository.php`    | CRUD พื้นฐาน (Create, Read, Update, Delete) |
| `UserRepository.php`    | ค้นหาด้วย email, username, role             |
| `ServiceRepository.php` | ค้นหาตามหมวดหมู่, สถานะ active              |
| `StaffRepository.php`   | ค้นหาตาม user_id, ความเชี่ยวชาญ             |
| `BookingRepository.php` | ตรวจสอบช่วงเวลาซ้อนทับ                      |
| `PaymentRepository.php` | คำนวณยอดรวมการชำระเงิน                      |
| `ReviewRepository.php`  | คำนวณคะแนนเฉลี่ย                            |

### 3. Services (ตรรกะธุรกิจ)

จัดการกฎและกระบวนการทำงาน:

| ไฟล์                    | หน้าที่                               |
| ----------------------- | ------------------------------------- |
| `AuthService.php`       | ลงทะเบียน, เข้าสู่ระบบ, hash รหัสผ่าน |
| `BookingService.php`    | สร้างการจอง, ตรวจซ้อนทับ, ยกเลิก      |
| `PaymentService.php`    | สร้างรายการชำระเงิน, ตรวจสลิป         |
| `Slip2GoService.php`    | เชื่อมต่อ API ตรวจสอบสลิป             |
| `LineNotifyService.php` | ส่งข้อความแจ้งเตือนผ่าน LINE          |
| `ReportService.php`     | สรุปรายได้, บริการยอดนิยม             |

### 4. Controllers (ควบคุมการทำงาน)

รับ Request และส่ง Response:

| ไฟล์                    | หน้าที่                                |
| ----------------------- | -------------------------------------- |
| `HomeController.php`    | หน้าแรก, บริการ, เกี่ยวกับเรา          |
| `AuthController.php`    | หน้าเข้าสู่ระบบ, ลงทะเบียน, ออกจากระบบ |
| `MemberController.php`  | แดชบอร์ดสมาชิก, การจอง, โปรไฟล์        |
| `BookingController.php` | สร้างการจอง, ดูรายละเอียด              |
| `StaffController.php`   | แดชบอร์ดพนักงาน                        |
| `AdminController.php`   | แดชบอร์ดผู้ดูแล, รายงาน, จัดการทั้งหมด |

### 5. API Endpoints (REST API)

ให้ Frontend เรียกใช้ข้อมูล:

| Endpoint           | หน้าที่           |
| ------------------ | ----------------- |
| `/api/v1/auth`     | ยืนยันตัวตน       |
| `/api/v1/users`    | จัดการผู้ใช้      |
| `/api/v1/services` | จัดการบริการ      |
| `/api/v1/staff`    | จัดการช่าง        |
| `/api/v1/bookings` | จัดการการจอง      |
| `/api/v1/payments` | จัดการการชำระเงิน |
| `/api/v1/reviews`  | จัดการรีวิว       |
| `/api/v1/reports`  | ดูรายงาน          |

---

## 🗄️ ฐานข้อมูล / Database

ใช้ **JSON Files** แทน MySQL (สามารถย้ายไป MySQL ได้ในอนาคต):

| ไฟล์            | ข้อมูล           | จำนวนแถวตัวอย่าง |
| --------------- | ---------------- | ---------------- |
| `users.json`    | ผู้ใช้งานทั้งหมด | 6                |
| `services.json` | รายการบริการ     | 7                |
| `staff.json`    | ข้อมูลช่าง       | 2                |
| `bookings.json` | การจองคิว        | 5                |
| `payments.json` | การชำระเงิน      | 3                |
| `reviews.json`  | รีวิว            | 2                |

### โครงสร้าง JSON

```json
{
  "_meta": {
    "description": "คำอธิบาย",
    "next_id": 7
  },
  "data": [
    { "id": 1, "uuid": "...", ... },
    { "id": 2, "uuid": "...", ... }
  ]
}
```

---

## 🎨 ดีไซน์ UI / UI Design

ใช้สไตล์ **Lovable** — นุ่มนวล ทันสมัย:

| องค์ประกอบ    | รายละเอียด               |
| ------------- | ------------------------ |
| **ฟอนต์**     | Plus Jakarta Sans        |
| **พื้นหลัง**  | ครีม (#FFF8F3)           |
| **สีหลัก**    | Gradient ชมพู-ม่วง       |
| **การ์ด**     | Glassmorphism + มุมโค้ง  |
| **ปุ่ม**      | Gradient + hover ลอยขึ้น |
| **Framework** | Bootstrap 5              |

---

## 🔐 ระบบสิทธิ์ / Role System

| บทบาท      | สิทธิ์                               |
| ---------- | ------------------------------------ |
| **Admin**  | จัดการทุกอย่างได้                    |
| **Owner**  | เหมือน Admin                         |
| **Staff**  | ดูการจองของตัวเอง                    |
| **Member** | จองคิว, ดูการจองตัวเอง, แก้ไขโปรไฟล์ |

---

## 🔄 กระบวนการทำงานหลัก / Main Workflows

### 1. การจองคิว (Booking Flow)

```
สมาชิก → เลือกบริการ → เลือกช่าง → เลือกวัน/เวลา
→ ตรวจสอบซ้อนทับ → สร้างรหัสจอง → บันทึก JSON
```

### 2. การชำระเงิน (Payment Flow)

```
สมาชิก → อัปโหลดสลิป → รอตรวจสอบ
→ แอดมินอนุมัติ/ปฏิเสธ → อัปเดตสถานะ
```

### 3. การแจ้งเตือน (Notification Flow)

```
ระบบ → ส่งข้อความ LINE → ลูกค้าได้รับแจ้งเตือน
(ถ้า LINE ปิดใช้งาน → บันทึก log แทน)
```

---

## 🛠️ เทคโนโลยีที่ใช้ / Tech Stack

| ประเภท        | เทคโนโลยี                         |
| ------------- | --------------------------------- |
| ภาษา          | PHP 8.0+                          |
| Frontend      | HTML5, CSS3, JavaScript (Vanilla) |
| CSS Framework | Bootstrap 5.3                     |
| Database      | JSON Files (future: MySQL)        |
| Web Server    | Apache + mod_rewrite              |
| API Style     | RESTful JSON                      |

---

## 📁 โครงสร้างโฟลเดอร์ / Directory Structure

```
Wikanda_Hair_Salon/
├── api/                    ← REST API
│   ├── index.php           ← API Router
│   └── v1/                 ← API Version 1
│       ├── auth.php
│       ├── bookings.php
│       ├── payments.php
│       ├── reports.php
│       ├── reviews.php
│       ├── services.php
│       ├── staff.php
│       └── users.php
│
├── app/
│   ├── Controllers/        ← Web Controllers (6 ไฟล์)
│   ├── Core/               ← Framework Core (7 ไฟล์)
│   │   ├── autoload.php    ← โหลดคลาสอัตโนมัติ
│   │   ├── Controller.php  ← คลาสฐาน Controller
│   │   ├── Database.php    ← จัดการ JSON
│   │   ├── Request.php     ← อ่าน HTTP Request
│   │   ├── Router.php      ← จับคู่ URL
│   │   ├── Session.php     ← จัดการ Session
│   │   └── View.php        ← Render HTML
│   ├── Models/             ← โมเดลข้อมูล (6 ไฟล์)
│   ├── Repositories/       ← เข้าถึงข้อมูล (7 ไฟล์)
│   ├── Services/           ← ตรรกะธุรกิจ (6 ไฟล์)
│   └── Views/              ← HTML Templates (16 ไฟล์)
│       ├── layouts/        ← Layout หลัก
│       ├── partials/       ← ส่วนย่อย (navbar, footer)
│       ├── helpers.php     ← Helper functions
│       ├── admin/          ← หน้าผู้ดูแล (7 ไฟล์)
│       ├── auth/           ← หน้าเข้าสู่ระบบ (2 ไฟล์)
│       ├── booking/        ← หน้าการจอง (2 ไฟล์)
│       ├── home/           ← หน้าสาธารณะ (3 ไฟล์)
│       ├── member/         ← หน้าสมาชิก (3 ไฟล์)
│       └── staff/          ← หน้าพนักงาน (2 ไฟล์)
│
├── config/                 ← ไฟล์ตั้งค่า
│   ├── app.php             ← ตั้งค่าทั่วไป
│   ├── line.php            ← ตั้งค่า LINE
│   ├── routes.php          ← เส้นทาง URL
│   └── slip2go.php         ← ตั้งค่า Slip2Go
│
├── data/                   ← ฐานข้อมูล JSON
│   ├── bookings.json
│   ├── payments.json
│   ├── reviews.json
│   ├── services.json
│   ├── staff.json
│   └── users.json
│
├── public/                 ← จุดเข้าเว็บ
│   ├── assets/
│   │   ├── css/style.css   ← สไตล์ Lovable
│   │   └── js/main.js      ← JavaScript หลัก
│   ├── .htaccess           ← URL Rewrite
│   └── index.php           ← Web Front Controller
│
├── storage/                ← ไฟล์อัปโหลดและ log
│   ├── logs/
│   └── uploads/
│       └── slips/
│
├── HANDOFF.md              ← เอกสารส่งต่องาน
├── PLAN.md                 ← แผนงาน
├── PROJECT_ANALYSIS.md    ← วิเคราะห์ธุรกิจ
├── README.md               ← คู่มือติดตั้ง
└── USER_GUIDE.md           ← คู่มือใช้งาน
```

---

## 🚀 จุดเด่น / Highlights

1. **ไม่ใช้ Framework** — เข้าใจง่าย แก้ไขสะดวก
2. **JSON Database** — ไม่ต้องติดตั้ง MySQL ตอนพัฒนา
3. **REST API** — พร้อมสำหรับ Mobile App ในอนาคต
4. **Lovable UI** — สวยงาม ทันสมัย
5. **Role-based Access** — ควบคุมสิทธิ์ชัดเจน
6. **Slip Verification** — ตรวจสอบสลิปอัตโนมัติ
7. **LINE Integration** — แจ้งเตือนผ่าน LINE

---

## 🔮 แผนพัฒนาในอนาคต / Future Plans

- [ ] ย้ายฐานข้อมูลจาก JSON ไป MySQL
- [ ] เพิ่มระบบส่งอีเมลยืนยันการจอง
- [ ] พัฒนา Mobile App (Flutter/React Native)
- [ ] เพิ่มระบบคะแนนสะสม (Loyalty Points)
- [ ] เชื่อมต่อระบบบัญชี (Accounting)
- [ ] รองรับหลายสาขา (Multi-branch)

---

## 👨‍💻 ผู้พัฒนา / Developer

พัฒนาโดย AI Assistant สำหรับ **Wikanda Hair Salon**

---

_อัปเดตล่าสุด: 18 พฤษภาคม 2026_
