<?php
/**
 * ============================================================
 *  StaffController.php — ควบคุมหน้าพนักงาน / Staff Pages Controller
 * ============================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\View;
use App\Middleware\AuthMiddleware;
use App\Repositories\BookingRepository;
use App\Repositories\StaffRepository;

class StaffController extends Controller
{
    /**
     * แดชบอร์ดพนักงาน / Staff dashboard
     */
    public function dashboard(): void
    {
        AuthMiddleware::requireRole('staff', 'admin', 'owner');

        $userId      = (int) Session::get('user_id');
        $staffRepo   = new StaffRepository();
        $staff       = $staffRepo->findByUserId($userId);
        $staffId     = $staff ? $staff['id'] : 0;

        $bookingRepo = new BookingRepository();
        $today       = date('Y-m-d');
        $bookings    = $staffId ? $bookingRepo->findByStaffAndDate($staffId, $today) : [];

        View::render('staff/dashboard', [
            'title'    => 'หน้าหลักพนักงาน',
            'bookings' => $bookings,
            'staff'    => $staff,
        ]);
    }

    /**
     * รายการจองของพนักงาน / Staff bookings list
     */
    public function bookings(): void
    {
        AuthMiddleware::requireRole('staff', 'admin', 'owner');

        $userId      = (int) Session::get('user_id');
        $staffRepo   = new StaffRepository();
        $staff       = $staffRepo->findByUserId($userId);
        $staffId     = $staff ? $staff['id'] : 0;

        $bookingRepo = new BookingRepository();
        $bookings    = $staffId ? $bookingRepo->findByStaff($staffId) : [];

        View::render('staff/bookings', [
            'title'    => 'การจองของฉัน',
            'bookings' => $bookings,
            'staff'    => $staff,
        ]);
    }

    /**
     * ตรวจสอบว่าเป็นพนักงานหรือไม่
     * Ensure user is staff
     */
}
