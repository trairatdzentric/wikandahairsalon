<?php
/**
 * ============================================================
 *  ตั้งค่า Slip2Go API / Slip2Go Configuration
 * ============================================================
 *
 *  Slip2Go คือบริการตรวจสอบสลิปโอนเงินอัตโนมัติของไทย
 *  ลงทะเบียน: https://slip2go.com
 *
 *  วิธีใช้:
 *   - ระบบส่งรูปสลิป (หรือข้อความ QR) ไปที่ API
 *   - Slip2Go ตรวจสอบกับธนาคารและคืนข้อมูล (จำนวนเงิน, เวลา, ผู้รับ)
 *   - ถ้าตรงกับใบเรียกชำระของเรา → ผ่านอัตโนมัติ
 *
 *  ⚠️  ห้าม commit ค่า api_key จริง
 *      แนะนำให้ copy ไฟล์นี้เป็น slip2go.local.php
 * ============================================================
 */

return [

    // เปิด/ปิดการตรวจสลิปอัตโนมัติ / Enable auto verify
    // ถ้า false: ระบบจะให้แอดมินตรวจสลิปด้วยมือแทน
    'enabled' => false,

    // API Key จากผู้ให้บริการ / API Key
    'api_key' => 'YOUR_SLIP2GO_API_KEY_HERE',

    // Endpoint ของ Slip2Go API (อ้างอิงตามเอกสาร)
    'endpoint' => 'https://api.slip2go.com/api/v1/qr/verify',

    // Timeout สำหรับเรียก API (วินาที) / API timeout in seconds
    'timeout' => 15,

    // ความคลาดเคลื่อนของจำนวนเงินที่ยอมรับ (บาท)
    // เช่น 1.00 หมายถึง รับได้ภายใน ±1 บาท
    'amount_tolerance' => 1.00,

    // ข้อมูลบัญชีรับเงินของร้าน (ใช้เปรียบเทียบกับสลิป)
    'receiver_account' => [
        'bank'          => 'KBANK',
        'account_no'    => '000-0-00000-0',
        'account_name'  => 'WIKANDA HAIR SALON',
        'promptpay_id'  => '0000000000',
    ],

    // log file สำหรับ debug / Log file
    'log_file' => __DIR__ . '/../storage/logs/slip2go.log',
];
