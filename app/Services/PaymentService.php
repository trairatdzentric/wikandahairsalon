<?php
/**
 * ============================================================
 *  PaymentService.php — บริการจัดการการชำระเงิน / Payment Service
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - สร้างรายการชำระเงิน (createPayment)
 *   - แนบรูปสลิป (attachSlip)
 *   - ตรวจสอบการชำระเงิน (verifyPayment → เรียก Slip2GoService)
 *   - อนุมัติด้วยมือ (approveManually)
 *
 *  ใช้ร่วมกับ: PaymentRepository, BookingRepository, Slip2GoService
 * ============================================================
 */

namespace App\Services;

use App\Models\Payment;
use App\Repositories\BookingRepository;
use App\Repositories\PaymentRepository;

class PaymentService
{
    private PaymentRepository $paymentRepo;
    private BookingRepository $bookingRepo;
    private Slip2GoService    $slip2go;

    public function __construct()
    {
        $this->paymentRepo = new PaymentRepository();
        $this->bookingRepo = new BookingRepository();
        $this->slip2go    = new Slip2GoService();
    }

    /**
     * สร้างรายการชำระเงินใหม่
     * Create a new payment record
     *
     * @param array $data ข้อมูลการชำระเงิน / Payment data
     * @return array ['success' => bool, 'payment' => array|null, 'message' => string]
     */
    public function createPayment(array $data): array
    {
        $required = ['booking_id', 'payment_type', 'amount', 'method'];
        foreach ($required as $field) {
            if (empty($data[$field] ?? '')) {
                return ['success' => false, 'payment' => null, 'message' => "กรุณากรอก {$field}"];
            }
        }

        // ตรวจสอบการจอง / Check booking exists
        $booking = $this->bookingRepo->find((int) $data['booking_id']);
        if (!$booking) {
            return ['success' => false, 'payment' => null, 'message' => 'ไม่พบการจอง'];
        }

        // ตรวจสอบประเภทและวิธีการ / Validate type and method
        if (!Payment::isValidType($data['payment_type'])) {
            return ['success' => false, 'payment' => null, 'message' => 'ประเภทการชำระเงินไม่ถูกต้อง'];
        }
        if (!Payment::isValidMethod($data['method'])) {
            return ['success' => false, 'payment' => null, 'message' => 'วิธีการชำระเงินไม่ถูกต้อง'];
        }

        $paymentData = [
            'booking_id'       => (int) $data['booking_id'],
            'payment_type'     => $data['payment_type'],
            'amount'           => (float) $data['amount'],
            'method'           => $data['method'],
            'slip_image'       => $data['slip_image'] ?? null,
            'slip2go_ref'      => null,
            'slip2go_verified' => false,
            'status'           => Payment::STATUS_PENDING,
            'verified_by'      => null,
            'verified_at'      => null,
            'note'             => $data['note'] ?? null,
        ];

        $payment = $this->paymentRepo->create($paymentData);

        return [
            'success' => true,
            'payment' => $payment,
            'message' => 'สร้างรายการชำระเงินสำเร็จ',
        ];
    }

    /**
     * แนบรูปสลิปเข้ากับรายการชำระเงิน
     * Attach slip image to a payment
     *
     * @param int    $paymentId
     * @param string $imagePath path ของรูปสลิปที่อัปโหลดแล้ว
     * @return array ['success' => bool, 'message' => string]
     */
    public function attachSlip(int $paymentId, string $imagePath): array
    {
        $payment = $this->paymentRepo->find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'ไม่พบรายการชำระเงิน'];
        }

        $filename = basename($imagePath);
        $this->paymentRepo->update($paymentId, [
            'slip_image' => $filename,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'แนบสลิปสำเร็จ'];
    }

    /**
     * ตรวจสอบการชำระเงินด้วย Slip2Go
     * Verify payment using Slip2Go API
     *
     * @param int $paymentId
     * @return array ['success' => bool, 'verified' => bool, 'message' => string]
     */
    public function verifyPayment(int $paymentId): array
    {
        $payment = $this->paymentRepo->find($paymentId);
        if (!$payment) {
            return ['success' => false, 'verified' => false, 'message' => 'ไม่พบรายการชำระเงิน'];
        }

        // ถ้าไม่มีสลิป / No slip attached
        $slipImage = $payment['slip_image'] ?? null;
        if (!$slipImage) {
            return ['success' => false, 'verified' => false, 'message' => 'ยังไม่มีสลิปแนบมา'];
        }

        $uploadPath = config('app.slip_upload_path', __DIR__ . '/../../storage/uploads/slips');
        $imagePath  = $uploadPath . '/' . $slipImage;

        $expectedAmount = (float) ($payment['amount'] ?? 0);
        $result         = $this->slip2go->verifySlip($imagePath, $expectedAmount);

        if (!$result['success']) {
            return [
                'success'  => false,
                'verified' => false,
                'message'  => $result['message'],
            ];
        }

        // อัปเดตสถานะตามผลตรวจสอบ / Update status based on result
        $status = $result['verified'] ? Payment::STATUS_VERIFIED : Payment::STATUS_PENDING;

        $this->paymentRepo->update($paymentId, [
            'slip2go_verified' => $result['verified'],
            'status'           => $status,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        return [
            'success'  => true,
            'verified' => $result['verified'],
            'message'  => $result['message'],
        ];
    }

    /**
     * อนุมัติการชำระเงินด้วยมือ (แอดมิน)
     * Manually approve a payment (admin only)
     *
     * @param int $paymentId
     * @param int $adminId   รหัสแอดมินที่อนุมัติ
     * @return array ['success' => bool, 'message' => string]
     */
    public function approveManually(int $paymentId, int $adminId): array
    {
        $payment = $this->paymentRepo->find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'ไม่พบรายการชำระเงิน'];
        }

        $this->paymentRepo->update($paymentId, [
            'status'       => Payment::STATUS_VERIFIED,
            'verified_by'    => $adminId,
            'verified_at'    => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'อนุมัติการชำระเงินสำเร็จ'];
    }

    /**
     * ปฏิเสธการชำระเงิน (แอดมิน)
     * Reject a payment (admin only)
     *
     * @param int    $paymentId
     * @param int    $adminId รหัสแอดมินที่ปฏิเสธ
     * @param string $reason  เหตุผล
     * @return array ['success' => bool, 'message' => string]
     */
    public function rejectPayment(int $paymentId, int $adminId, string $reason = ''): array
    {
        $payment = $this->paymentRepo->find($paymentId);
        if (!$payment) {
            return ['success' => false, 'message' => 'ไม่พบรายการชำระเงิน'];
        }

        $note = $payment['note'] ?? '';
        if ($reason !== '') {
            $note .= ($note !== '' ? "\n" : '') . 'เหตุผลการปฏิเสธ: ' . $reason;
        }

        $this->paymentRepo->update($paymentId, [
            'status'       => Payment::STATUS_REJECTED,
            'verified_by'  => $adminId,
            'verified_at'  => date('Y-m-d H:i:s'),
            'note'         => $note,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'ปฏิเสธการชำระเงินแล้ว'];
    }
}
