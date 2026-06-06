<?php
/**
 * ============================================================
 *  ตารางเส้นทาง URL ของหน้าเว็บ / Web Routes Configuration
 * ============================================================
 *
 *  รูปแบบ: 'METHOD /url' => 'ControllerName@methodName'
 *
 *  ระบบจะอ่านตารางนี้ใน Router แล้วจับคู่ URL
 *  กับ Controller ที่ต้องเรียกใช้
 *
 *  หมายเหตุ: ส่วน API จะอยู่ในไฟล์ api/index.php แยกต่างหาก
 *           ไฟล์นี้ใช้สำหรับ Web Routes (หน้า HTML) เท่านั้น
 * ============================================================
 */

return [

    // ------------------------------------------------------------
    // หน้าสาธารณะ / Public pages
    // ------------------------------------------------------------
    'GET /'                  => 'HomeController@index',
    'GET /services'          => 'HomeController@services',
    'GET /about'             => 'HomeController@about',

    // ------------------------------------------------------------
    // หน้า Authentication / Auth pages
    // ------------------------------------------------------------
    'GET /login'             => 'AuthController@showLogin',
    'GET /register'          => 'AuthController@showRegister',
    'GET /logout'            => 'AuthController@logout',

    // ------------------------------------------------------------
    // หน้าสมาชิก / Member pages (ต้องล็อกอินด้วย role=member)
    // ------------------------------------------------------------
    'GET /member'            => 'MemberController@dashboard',
    'GET /member/bookings'   => 'MemberController@bookings',
    'GET /member/profile'    => 'MemberController@profile',

    // ------------------------------------------------------------
    // หน้าจองคิว / Booking pages
    // ------------------------------------------------------------
    'GET /booking/new'       => 'BookingController@create',
    'GET /booking/{id}'      => 'BookingController@show',

    // ------------------------------------------------------------
    // หน้าพนักงาน / Staff pages (role=staff)
    // ------------------------------------------------------------
    'GET /staff'             => 'StaffController@dashboard',
    'GET /staff/bookings'    => 'StaffController@bookings',

    // ------------------------------------------------------------
    // หน้าผู้ดูแล / Admin pages (role=admin, owner)
    // ------------------------------------------------------------
    'GET /admin'             => 'AdminController@dashboard',
    'GET /admin/users'       => 'AdminController@users',
    'GET /admin/services'    => 'AdminController@services',
    'GET /admin/staff'       => 'AdminController@staff',
    'GET /admin/bookings'    => 'AdminController@bookings',
    'GET /admin/payments'    => 'AdminController@payments',
    'GET /admin/report'      => 'AdminController@report',
    'GET /admin/settings'    => 'AdminController@settings',
    'POST /admin/settings'   => 'AdminController@saveSettings',
];
