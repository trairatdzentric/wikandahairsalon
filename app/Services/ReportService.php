<?php
/**
 * ============================================================
 *  ReportService.php — บริการรายงาน / Report Service
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - รายงานรายได้ตามวัน (revenueByDay)
 *   - รายงานรายได้ตามเดือน (revenueByMonth)
 *   - รายงานรายได้ตามปี (revenueByYear)
 *   - บริการยอดนิยม (topServices)
 *
 *  ใช้ร่วมกับ: PaymentRepository, BookingRepository, ServiceRepository
 * ============================================================
 */

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ServiceRepository;

class ReportService
{
    private PaymentRepository $paymentRepo;
    private BookingRepository $bookingRepo;
    private ServiceRepository $serviceRepo;

    public function __construct()
    {
        $this->paymentRepo = new PaymentRepository();
        $this->bookingRepo = new BookingRepository();
        $this->serviceRepo = new ServiceRepository();
    }

    /**
     * รายงานรายได้ตามวัน
     * Revenue report by day
     *
     * @param string $date รูปแบบ YYYY-MM-DD
     * @return array ['total' => float, 'count' => int, 'payments' => array]
     */
    public function revenueByDay(string $date): array
    {
        $payments = $this->paymentRepo->findByStatus('verified');
        $filtered = [];
        $total    = 0.0;

        foreach ($payments as $p) {
            // ดึงวันที่จาก created_at / Extract date from created_at
            $paymentDate = substr($p['created_at'] ?? '', 0, 10);
            if ($paymentDate === $date) {
                $filtered[] = $p;
                $total     += (float) ($p['amount'] ?? 0);
            }
        }

        return [
            'date'     => $date,
            'total'    => round($total, 2),
            'count'    => count($filtered),
            'payments' => $filtered,
        ];
    }

    /**
     * รายงานรายได้ตามเดือน
     * Revenue report by month
     *
     * @param string $month รูปแบบ YYYY-MM
     * @return array ['total' => float, 'count' => int, 'daily' => array]
     */
    public function revenueByMonth(string $month): array
    {
        $payments = $this->paymentRepo->findByStatus('verified');
        $daily    = [];
        $total    = 0.0;
        $count    = 0;

        foreach ($payments as $p) {
            $paymentMonth = substr($p['created_at'] ?? '', 0, 7);
            if ($paymentMonth !== $month) {
                continue;
            }

            $day   = substr($p['created_at'] ?? '', 0, 10);
            $amount = (float) ($p['amount'] ?? 0);

            if (!isset($daily[$day])) {
                $daily[$day] = ['total' => 0.0, 'count' => 0];
            }

            $daily[$day]['total']  += $amount;
            $daily[$day]['count']  += 1;
            $total                 += $amount;
            $count                 += 1;
        }

        // เรียงตามวัน / Sort by day
        ksort($daily);

        return [
            'month' => $month,
            'total' => round($total, 2),
            'count' => $count,
            'daily' => $daily,
        ];
    }

    /**
     * รายงานรายได้ตามปี
     * Revenue report by year
     *
     * @param string $year รูปแบบ YYYY
     * @return array ['total' => float, 'count' => int, 'monthly' => array]
     */
    public function revenueByYear(string $year): array
    {
        $payments = $this->paymentRepo->findByStatus('verified');
        $monthly  = [];
        $total    = 0.0;
        $count    = 0;

        foreach ($payments as $p) {
            $paymentYear = substr($p['created_at'] ?? '', 0, 4);
            if ($paymentYear !== $year) {
                continue;
            }

            $month  = substr($p['created_at'] ?? '', 0, 7);
            $amount = (float) ($p['amount'] ?? 0);

            if (!isset($monthly[$month])) {
                $monthly[$month] = ['total' => 0.0, 'count' => 0];
            }

            $monthly[$month]['total'] += $amount;
            $monthly[$month]['count'] += 1;
            $total                    += $amount;
            $count                    += 1;
        }

        // เรียงตามเดือน / Sort by month
        ksort($monthly);

        return [
            'year'    => $year,
            'total'   => round($total, 2),
            'count'   => $count,
            'monthly' => $monthly,
        ];
    }

    /**
     * บริการยอดนิยม
     * Top services by booking count
     *
     * @param int $limit จำนวนบริการที่ต้องการ
     * @return array
     */
    public function topServices(int $limit = 5): array
    {
        $bookings = $this->bookingRepo->all();
        $counts   = [];

        // นับจำนวนการจองต่อบริการ / Count bookings per service
        foreach ($bookings as $b) {
            $sid = (int) ($b['service_id'] ?? 0);
            if ($sid <= 0) {
                continue;
            }

            if (!isset($counts[$sid])) {
                $counts[$sid] = [
                    'service_id' => $sid,
                    'count'      => 0,
                    'revenue'    => 0.0,
                ];
            }

            $counts[$sid]['count']   += 1;
            $counts[$sid]['revenue'] += (float) ($b['total_price'] ?? 0);
        }

        // ดึงชื่อบริการ / Get service names
        foreach ($counts as $sid => &$info) {
            $service = $this->serviceRepo->find($sid);
            $info['name']    = $service['name'] ?? 'ไม่ทราบชื่อ';
            $info['name_en'] = $service['name_en'] ?? 'Unknown';
        }
        unset($info);

        // เรียงตามจำนวนการจองมาก → น้อย / Sort by count desc
        usort($counts, fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        return array_slice($counts, 0, $limit);
    }

    /**
     * สรุปภาพรวมของวันนี้
     * Today's summary dashboard
     *
     * @return array
     */
    public function todaySummary(): array
    {
        $today     = date('Y-m-d');
        $bookings  = $this->bookingRepo->findByDate($today);
        $revenue   = $this->revenueByDay($today);

        $pending   = 0;
        $confirmed = 0;
        $completed = 0;
        $cancelled = 0;

        foreach ($bookings as $b) {
            match ($b['status'] ?? '') {
                'pending'    => $pending++,
                'confirmed'  => $confirmed++,
                'completed'  => $completed++,
                'cancelled'  => $cancelled++,
                default      => null,
            };
        }

        return [
            'date'      => $today,
            'bookings'  => [
                'total'     => count($bookings),
                'pending'   => $pending,
                'confirmed' => $confirmed,
                'completed' => $completed,
                'cancelled' => $cancelled,
            ],
            'revenue'   => $revenue['total'],
            'payments'  => $revenue['count'],
        ];
    }
}
