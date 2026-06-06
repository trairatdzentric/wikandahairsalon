<?php
/**
 * ============================================================
 *  BookingService.php — บริการจัดการการจอง / Booking Service
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - สร้างการจองใหม่ (createBooking)
 *   - ตรวจสอบช่วงเวลาซ้อนทับ (checkTimeConflict)
 *   - ยกเลิกการจอง (cancelBooking)
 *   - สร้างรหัสจอง (generateBookingCode)
 *   - อัปเดตสถานะการจอง
 *
 *  ใช้ร่วมกับ: BookingRepository, ServiceRepository, StaffRepository
 * ============================================================
 */

namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\StaffRepository;

class BookingService
{
    private BookingRepository $bookingRepo;
    private ServiceRepository $serviceRepo;
    private StaffRepository   $staffRepo;

    public function __construct()
    {
        $this->bookingRepo = new BookingRepository();
        $this->serviceRepo  = new ServiceRepository();
        $this->staffRepo    = new StaffRepository();
    }

    /**
     * สร้างการจองใหม่
     * Create a new booking
     *
     * @param array $data ข้อมูลการจอง / Booking data
     * @return array ['success' => bool, 'booking' => array|null, 'message' => string]
     */
    public function createBooking(array $data): array
    {
        // ตรวจสอบข้อมูลจำเป็น / Validate required fields
        $required = ['member_id', 'service_id', 'staff_id', 'booking_date', 'start_time'];
        foreach ($required as $field) {
            if (empty($data[$field] ?? '')) {
                return ['success' => false, 'booking' => null, 'message' => "กรุณากรอก {$field}"];
            }
        }

        // ตรวจสอบว่าบริการมีอยู่จริง / Check service exists
        $service = $this->serviceRepo->find((int) $data['service_id']);
        if (!$service) {
            return ['success' => false, 'booking' => null, 'message' => 'ไม่พบบริการที่เลือก'];
        }

        // ตรวจสอบว่าช่างมีอยู่จริง / Check staff exists
        $staff = $this->staffRepo->find((int) $data['staff_id']);
        if (!$staff) {
            return ['success' => false, 'booking' => null, 'message' => 'ไม่พบช่างที่เลือก'];
        }

        // คำนวณเวลาสิ้นสุด / Calculate end time
        $duration = (int) ($service['duration_minutes'] ?? 30);
        $endTime  = $this->addMinutes($data['start_time'], $duration);

        // ตรวจสอบช่วงเวลาซ้อนทับ / Check time conflict
        if ($this->checkTimeConflict(
            (int) $data['staff_id'],
            $data['booking_date'],
            $data['start_time'],
            $endTime
        )) {
            return [
                'success' => false,
                'booking' => null,
                'message' => 'ช่วงเวลานี้มีการจองซ้อนทับ กรุณาเลือกเวลาอื่น',
            ];
        }

        // สร้างรหัสจอง / Generate booking code
        $bookingCode = $this->generateBookingCode();

        // สร้างการจอง / Create booking
        $bookingData = [
            'booking_code' => $bookingCode,
            'member_id'    => (int) $data['member_id'],
            'service_id'   => (int) $data['service_id'],
            'staff_id'     => (int) $data['staff_id'],
            'booking_date' => $data['booking_date'],
            'start_time'   => $data['start_time'],
            'end_time'     => $endTime,
            'total_price'  => (float) ($service['price'] ?? 0),
            'status'       => $data['status'] ?? Booking::STATUS_PENDING,
            'note'         => $data['note'] ?? null,
        ];

        $booking = $this->bookingRepo->create($bookingData);

        return [
            'success' => true,
            'booking' => $booking,
            'message' => 'จองคิวสำเร็จ รหัสจอง: ' . $bookingCode,
        ];
    }

    /**
     * ตรวจสอบว่ามีการจองซ้อนทับหรือไม่
     * Check if there's a time conflict
     *
     * @param int    $staffId
     * @param string $date      YYYY-MM-DD
     * @param string $startTime HH:MM
     * @param string $endTime   HH:MM
     * @param int|null $excludeId ข้ามการจอง id นี้
     * @return bool true = มีซ้อนทับ
     */
    public function checkTimeConflict(
        int $staffId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        return $this->bookingRepo->hasTimeConflict($staffId, $date, $startTime, $endTime, $excludeId);
    }

    /**
     * ยกเลิกการจอง
     * Cancel a booking
     *
     * @param int    $bookingId
     * @param int    $memberId  ผู้ขอยกเลิก (ตรวจสอบสิทธิ์)
     * @param string $reason    เหตุผลการยกเลิก
     * @return array ['success' => bool, 'message' => string]
     */
    public function cancelBooking(int $bookingId, int $memberId, string $reason = ''): array
    {
        $booking = $this->bookingRepo->find($bookingId);

        if (!$booking) {
            return ['success' => false, 'message' => 'ไม่พบการจอง'];
        }

        // ตรวจสอบสิทธิ์ / Check ownership
        if ((int) $booking['member_id'] !== $memberId) {
            return ['success' => false, 'message' => 'ไม่มีสิทธิ์ยกเลิกการจองนี้'];
        }

        // ตรวจสอบว่าสามารถยกเลิกได้ / Check if cancellable
        $status = $booking['status'] ?? '';
        if (!in_array($status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED], true)) {
            return ['success' => false, 'message' => 'ไม่สามารถยกเลิกการจองในสถานะนี้ได้'];
        }

        $note = $booking['note'] ?? '';
        if ($reason !== '') {
            $note .= ($note !== '' ? "\n" : '') . 'เหตุผลการยกเลิก: ' . $reason;
        }

        $this->bookingRepo->update($bookingId, [
            'status'     => Booking::STATUS_CANCELLED,
            'note'       => $note,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'ยกเลิกการจองสำเร็จ'];
    }

    /**
     * อัปเดตสถานะการจอง
     * Update booking status
     *
     * @param int    $bookingId
     * @param string $status    สถานะใหม่
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateStatus(int $bookingId, string $status): array
    {
        if (!Booking::isValidStatus($status)) {
            return ['success' => false, 'message' => 'สถานะไม่ถูกต้อง'];
        }

        $booking = $this->bookingRepo->find($bookingId);
        if (!$booking) {
            return ['success' => false, 'message' => 'ไม่พบการจอง'];
        }

        $this->bookingRepo->update($bookingId, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'อัปเดตสถานะสำเร็จ'];
    }

    /**
     * สร้างรหัสจองอัตโนมัติ
     * Generate unique booking code
     *
     * รูปแบบ: WK{YYYYMMDD}-{XXX}
     * ตัวอย่าง: WK20260518-006
     *
     * @return string
     */
    public function generateBookingCode(): string
    {
        $date   = date('Ymd');
        $prefix = 'WK' . $date;

        // นับจำนวนการจองวันนี้ / Count today's bookings
        $todayBookings = $this->bookingRepo->findByDate(date('Y-m-d'));
        $count         = count($todayBookings) + 1;

        return sprintf('%s-%03d', $prefix, $count);
    }

    /**
     * คำนวณเวลาสิ้นสุดจากเวลาเริ่มต้น + นาที
     * Calculate end time from start time + minutes
     *
     * @param string $startTime HH:MM
     * @param int    $minutes
     * @return string HH:MM
     */
    private function addMinutes(string $startTime, int $minutes): string
    {
        $time = strtotime($startTime);
        $end  = strtotime("+{$minutes} minutes", $time);
        return date('H:i', $end);
    }
}
