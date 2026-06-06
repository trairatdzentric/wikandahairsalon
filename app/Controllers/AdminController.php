<?php
/**
 * ============================================================
 *  AdminController.php — ควบคุมหน้าผู้ดูแล / Admin Pages Controller
 * ============================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Middleware\AuthMiddleware;
use App\Repositories\BookingRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\StaffRepository;
use App\Repositories\UserRepository;
use App\Services\IntegrationSettingsService;
use App\Services\ReportService;

class AdminController extends Controller
{
    /**
     * แดชบอร์ดผู้ดูแล / Admin dashboard
     */
    public function dashboard(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $report = new ReportService();
        $summary = $report->todaySummary();

        View::render('admin/dashboard', [
            'title'   => 'แดชบอร์ดผู้ดูแล',
            'summary' => $summary,
        ]);
    }

    /**
     * รายการผู้ใช้งาน / Users list
     */
    public function users(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $userRepo = new UserRepository();

        View::render('admin/users', [
            'title' => 'จัดการผู้ใช้งาน',
            'users' => $userRepo->all(),
        ]);
    }

    /**
     * รายการบริการ / Services list
     */
    public function services(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $serviceRepo = new ServiceRepository();

        View::render('admin/services', [
            'title'    => 'จัดการบริการ',
            'services' => $serviceRepo->all(),
        ]);
    }

    /**
     * รายการช่าง / Staff list
     */
    public function staff(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $staffRepo = new StaffRepository();

        View::render('admin/staff', [
            'title' => 'จัดการช่าง',
            'staff' => $staffRepo->all(),
        ]);
    }

    /**
     * รายการจอง / Bookings list
     */
    public function bookings(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $bookingRepo = new BookingRepository();

        View::render('admin/bookings', [
            'title'    => 'จัดการการจอง',
            'bookings' => $bookingRepo->all(),
        ]);
    }

    /**
     * รายการชำระเงิน / Payments list
     */
    public function payments(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $paymentRepo = new PaymentRepository();

        View::render('admin/payments', [
            'title'    => 'จัดการการชำระเงิน',
            'payments' => $paymentRepo->all(),
        ]);
    }

    /**
     * รายงาน / Reports page
     */
    public function report(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $report = new ReportService();
        $today  = date('Y-m-d');
        $month  = date('Y-m');

        View::render('admin/report', [
            'title'        => 'รายงาน',
            'todayRevenue' => $report->revenueByDay($today),
            'monthRevenue' => $report->revenueByMonth($month),
            'topServices'  => $report->topServices(5),
        ]);
    }

    /**
     * ตรวจสอบว่าเป็น admin หรือ owner
     * Ensure user is admin or owner
     */

    /**
     * Integration settings page.
     */
    public function settings(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $settings = new IntegrationSettingsService();

        View::render('admin/settings', [
            'title'    => 'ตั้งค่าการเชื่อมต่อ',
            'settings' => $settings->all(),
            'status'   => $settings->status(),
        ]);
    }

    /**
     * Save integration settings to JSON database.
     */
    public function saveSettings(): void
    {
        AuthMiddleware::requireRole('admin', 'owner');

        $settings = new IntegrationSettingsService();
        $settings->save($this->request->all());

        \App\Core\Session::flash('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
        $this->redirect('/admin/settings');
    }
}


