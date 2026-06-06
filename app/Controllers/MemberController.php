<?php
/**
 * ============================================================
 *  MemberController.php — ควบคุมหน้าสมาชิก / Member Pages Controller
 * ============================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Middleware\AuthMiddleware;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;

class MemberController extends Controller
{
    /**
     * แดชบอร์ดสมาชิก / Member dashboard
     */
    public function dashboard(): void
    {
        AuthMiddleware::requireRole('member', 'admin', 'owner');

        $userId      = (int) Session::get('user_id');
        $bookingRepo = new BookingRepository();
        $bookings    = $bookingRepo->findByMember($userId);

        View::render('member/dashboard', [
            'title'    => 'หน้าหลักสมาชิก',
            'bookings' => $bookings,
        ]);
    }

    /**
     * รายการจองของสมาชิก / Member bookings list
     */
    public function bookings(): void
    {
        AuthMiddleware::requireRole('member', 'admin', 'owner');

        $userId      = (int) Session::get('user_id');
        $bookingRepo = new BookingRepository();
        $bookings    = $bookingRepo->findByMember($userId);

        View::render('member/bookings', [
            'title'    => 'การจองของฉัน',
            'bookings' => $bookings,
        ]);
    }

    /**
     * หน้าโปรไฟล์สมาชิก / Member profile page
     */
    public function profile(): void
    {
        AuthMiddleware::requireRole('member', 'admin', 'owner');

        $userId   = (int) Session::get('user_id');
        $userRepo = new UserRepository();
        $user     = $userRepo->find($userId);

        View::render('member/profile', [
            'title' => 'โปรไฟล์ของฉัน',
            'user'  => $user,
        ]);
    }

    /**
     * ตรวจสอบว่าเป็นสมาชิกหรือไม่
     * Ensure user is a member
     */
}
