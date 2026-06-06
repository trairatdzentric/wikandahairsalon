<?php
/**
 * ============================================================
 *  ตั้งค่า LINE Messaging API / LINE OA Configuration
 * ============================================================
 *
 *  วิธีรับ Token:
 *  1) เข้า https://developers.line.biz/console/
 *  2) สร้าง Provider + Messaging API channel
 *  3) คัดลอก Channel Access Token (long-lived) มาวางใน key 'channel_access_token'
 *  4) สร้าง Rich Menu / Webhook ตามต้องการ
 *
 *  ⚠️  ห้าม commit ค่า token จริงขึ้น git
 *      แนะนำให้ copy ไฟล์นี้เป็น line.local.php แล้วใส่ค่าจริง
 *      จากนั้น add line.local.php ใน .gitignore
 * ============================================================
 */

return [

    // เปิด/ปิดการส่งข้อความ LINE / Enable LINE notification
    // ถ้า false ระบบจะ log ข้อความลง file แทน (ใช้ตอน dev)
    'enabled' => false,

    // โทเค็นสำหรับเรียก Messaging API / Channel Access Token
    'channel_access_token' => 'YOUR_LINE_CHANNEL_ACCESS_TOKEN_HERE',

    // Channel Secret (ใช้ตอนรับ webhook)
    'channel_secret' => 'YOUR_LINE_CHANNEL_SECRET_HERE',

    // Endpoint สำหรับส่งข้อความแบบ Push / Push message endpoint
    'push_url'      => 'https://api.line.me/v2/bot/message/push',

    // Endpoint สำหรับ Broadcast ไปทุก follower
    'broadcast_url' => 'https://api.line.me/v2/bot/message/broadcast',

    // ที่อยู่ log สำหรับโหมด disabled / Log path when disabled
    'log_file'      => __DIR__ . '/../storage/logs/line.log',
];
