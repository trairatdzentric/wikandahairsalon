<?php
/**
 * ============================================================
 *  BookingController.php — ควบคุมหน้าการจอง / Booking Pages Controller
 * ============================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Middleware\AuthMiddleware;
use App\Repositories\BookingRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\StaffRepository;

class BookingController extends Controller
{
    /**
     * แสดงหน้าสร้างการจองใหม่ / Show create booking page
     */
    public function create(): void
    {
        AuthMiddleware::requireRole('member', 'admin', 'owner');

        $serviceRepo = new ServiceRepository();
        $staffRepo   = new StaffRepository();

        View::render('booking/create', [
            'title'    => 'จองคิวใหม่',
            'services' => $serviceRepo->findActive(),
            'staff'    => $staffRepo->findActive(),
        ]);
    }

    /**
     * แสดงรายละเอียดการจอง / Show booking details
     *
     * @param int $id รหัสการจอง
     */
    public function show(int $id): void
    {
        AuthMiddleware::requireRole('member', 'admin', 'owner');

        $bookingRepo = new BookingRepository();
        $booking     = $bookingRepo->find($id);

        if (!$booking) {
            $this->notFound('ไม่พบการจอง');
            return;
        }

        View::render('booking/show', [
            'title'   => 'รายละเอียดการจอง',
            'booking' => $booking,
        ]);
    }
}
