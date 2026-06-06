<?php
/**
 * ============================================================
 *  PaymentRepository.php — จัดการข้อมูลการชำระเงิน / Payment Repository
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - CRUD การชำระเงิน
   *   - ค้นหาตาม booking_id, status, method
 *   - ใช้ร่วมกับ PaymentService และ Slip2GoService
 *
 *  ตาราง: payments.json
 * ============================================================
 */

namespace App\Repositories;

class PaymentRepository extends BaseRepository
{
    /** @var string ชื่อตาราง / Table name */
    protected string $table = 'payments';

    /**
     * ค้นหาการชำระเงินตามการจอง
     * Find payments by booking id
     *
     * @param int $bookingId
     * @return array
     */
    public function findByBooking(int $bookingId): array
    {
        return $this->where('booking_id', $bookingId);
    }

    /**
     * ค้นหาการชำระเงินตามสถานะ
     * Find payments by status
     *
     * @param string $status เช่น 'pending', 'verified', 'rejected'
     * @return array
     */
    public function findByStatus(string $status): array
    {
        return $this->where('status', $status);
    }

    /**
     * ค้นหาการชำระเงินตามวิธีการ
     * Find payments by method
     *
     * @param string $method เช่น 'promptpay', 'bank_transfer', 'cash'
     * @return array
     */
    public function findByMethod(string $method): array
    {
        return $this->where('method', $method);
    }

    /**
     * ค้นหาการชำระเงินที่รอการตรวจสอบ
     * Find pending payments that need verification
     *
     * @return array
     */
    public function findPending(): array
    {
        return $this->findByStatus('pending');
    }

    /**
     * คำนวณยอดรวมการชำระเงินของการจองหนึ่งรายการ
     * Calculate total paid amount for a booking
     *
     * @param int $bookingId
     * @return float
     */
    public function totalPaidByBooking(int $bookingId): float
    {
        $total = 0.0;
        foreach ($this->findByBooking($bookingId) as $row) {
            if (($row['status'] ?? '') === 'verified') {
                $total += (float) ($row['amount'] ?? 0);
            }
        }
        return $total;
    }
}
