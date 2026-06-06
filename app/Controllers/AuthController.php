<?php
/**
 * ============================================================
 *  AuthController.php — ควบคุมหน้าเข้าสู่ระบบ / Auth Pages Controller
 * ============================================================
 *
 *  หมายเหตุ: action ที่เปลี่ยน state (login, register) ให้ frontend
 *           เรียก API แทน ไฟล์นี้แสดงหน้าเท่านั้น
 * ============================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;

class AuthController extends Controller
{
    /**
     * แสดงหน้าเข้าสู่ระบบ / Show login page
     */
    public function showLogin(): void
    {
        View::render('auth/login', [
            'title' => 'เข้าสู่ระบบ',
        ]);
    }

    /**
     * แสดงหน้าลงทะเบียน / Show register page
     */
    public function showRegister(): void
    {
        View::render('auth/register', [
            'title' => 'ลงทะเบียน',
        ]);
    }

    /**
     * ออกจากระบบ (Web) — ล้าง session แล้ว redirect
     * Logout and redirect
     */
    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/');
    }
}
