# คู่มืออัปโหลดโปรเจกต์ขึ้น InfinityFree (File Manager)

## ขั้นตอนที่ 1: เตรียมไฟล์บนเครื่อง

### 1.1 สร้าง ZIP ไฟล์

บนเครื่องคุณ ให้ ZIP โฟลเดอร์ทั้งหมดยกเว้น `docs/` และ `data/` (ไม่จำเป็นต้องอัปโหลด)

**ไฟล์ที่ต้องรวม:**

```
Wikanda_Hair_Salon.zip
├── .htaccess
├── index.php
├── dev-router.php
├── temp_hash.php
├── README.md
├── PROJECT_STRUCTURE.md
├── api/
├── app/
├── config/
├── database/
├── public/
└── storage/
```

**ไฟล์ที่ไม่ต้องอัปโหลด:**

- `docs/` (เอกสาร)
- `data/` (JSON legacy)
- `*.pdf` (ถ้ามี)

---

## ขั้นตอนที่ 2: เข้า File Manager

1. เปิด [File Manager](https://filemanager.ai/new3/index.php)
2. เข้าสู่ระบบด้วยบัญชี InfinityFree
3. เลือกโดเมนของคุณ

---

## ขั้นตอนที่ 3: อัปโหลดไฟล์

### วิธีที่ 1: Upload & Unzip (แนะนำ)

1. ใน File Manager คลิก **"Upload & Unzip"** (ซ้ายมือ)
2. เลือกไฟล์ `Wikanda_Hair_Salon.zip` จากเครื่อง
3. รอให้อัปโหลดเสร็จ → ระบบจะแตกไฟล์อัตโนมัติ

### วิธีที่ 2: Upload ทีละไฟล์

1. คลิก **"Upload Files"**
2. เลือกไฟล์/โฟลเดอร์ที่ต้องการ
3. รอให้อัปโหลดเสร็จ

---

## ขั้นตอนที่ 4: จัดเรียงไฟล์ใน `htdocs/`

หลังจากอัปโหลด โครงสร้างต้องเป็นแบบนี้:

```
/htdocs/                    ← นี่คือ root ของเว็บ
├── .htaccess              ← ต้องมี!
├── index.php              ← Entry point
├── api/                   ← REST API
├── app/                   ← Application
├── config/                ← Config files
├── database/              ← SQL files (ไม่จำเป็นต้องมีบน host)
├── public/                ← Assets
└── storage/               ← ต้องสร้างโฟลเดอร์ย่อย
    ├── logs/              ← สร้างโฟลเดอร์ว่าง
    └── uploads/           ← สร้างโฟลเดอร์ว่าง
        └── slips/         ← สร้างโฟลเดอร์ว่าง
```

### สร้างโฟลเดอร์ที่ขาด:

1. ใน File Manager คลิก **"New Folder"**
2. สร้างโฟลเดอร์ตามลำดับ:
   - `storage/`
   - `storage/logs/`
   - `storage/uploads/`
   - `storage/uploads/slips/`

---

## ขั้นตอนที่ 5: ตั้งค่า Permissions

1. คลิกขวาที่โฟลเดอร์ `storage/`
2. เลือก **"Permissions"** หรือ **"Change Permissions"**
3. ตั้งค่าเป็น **755** (rwxr-xr-x)
4. ทำซ้ำกับ:
   - `storage/logs/` → 755
   - `storage/uploads/` → 755
   - `storage/uploads/slips/` → 755

---

## ขั้นตอนที่ 6: ตรวจสอบไฟล์สำคัญ

### ต้องมีใน `htdocs/`:

| ไฟล์                  | สถานะ     | คำอธิบาย        |
| --------------------- | --------- | --------------- |
| `.htaccess`           | ✅ ต้องมี | Rewrite rules   |
| `index.php`           | ✅ ต้องมี | Entry point     |
| `config/database.php` | ✅ ต้องมี | ตั้งค่า MySQL   |
| `app/Core/`           | ✅ ต้องมี | Framework core  |
| `app/Repositories/`   | ✅ ต้องมี | Database access |

---

## ขั้นตอนที่ 7: ทดสอบเว็บไซต์

1. เปิด browser
2. เข้า `https://wikandahairsalon.free.nf/`
3. ควรเห็นหน้าแรกของเว็บ

### ถ้าเจอ Error:

**Error 500:**

- ตรวจสอบ `config/database.php` ว่าตั้งค่าถูกต้อง
- ตรวจสอบว่าไฟล์ใน `app/` ครบถ้วน

**Error 404:**

- ตรวจสอบว่า `.htaccess` อยู่ใน `htdocs/`
- ตรวจสอบว่า `index.php` อยู่ใน `htdocs/`

**Database Connection Error:**

- เข้า phpMyAdmin ตรวจสอบว่า import schema แล้ว
- ตรวจสอบ username/password ใน `config/database.php`

---

## Checklist ก่อนเปิดใช้งาน

- [ ] อัปโหลดไฟล์ครบถ้วน
- [ ] สร้างโฟลเดอร์ `storage/logs/`
- [ ] สร้างโฟลเดอร์ `storage/uploads/slips/`
- [ ] ตั้งค่า permissions 755 สำหรับ storage/
- [ ] Import database schema ใน phpMyAdmin
- [ ] Import data ใน phpMyAdmin
- [ ] ทดสอบหน้าแรกโหลดได้
- [ ] ทดสอบ login ได้

---

## โครงสร้างหลังอัปโหลด (สรุป)

```
[InfinityFree File Manager]
    └── htdocs/
        ├── .htaccess              ✅
        ├── index.php              ✅
        ├── api/                   ✅
        ├── app/                   ✅
        ├── config/                ✅
        ├── public/                ✅
        └── storage/               ✅
            ├── logs/              ✅ (สร้างใหม่)
            └── uploads/           ✅
                └── slips/         ✅ (สร้างใหม่)
```

---

## ข้อควรระวัง

1. **อย่าลบ `.htaccess`** - ไฟล์นี้ซ่อนอยู่ ต้องตั้งค่าให้แสดงไฟล์ซ่อน
2. **อย่าลืมสร้าง `storage/`** - ถ้าไม่มีจะอัปโหลดสลิปไม่ได้
3. **ตรวจสอบ case sensitive** - Linux server ตัวพิมพ์ใหญ่-เล็กมีผล

---

**เอกสารนี้อัปเดตล่าสุด:** 2026-06-06
