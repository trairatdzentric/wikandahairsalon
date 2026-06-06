<?php
/**
 * ============================================================
 *  LineNotifyService.php — บริการส่งข้อความ LINE / LINE Messaging Service
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - ส่งข้อความ Push ไปยังผู้ใช้คนหนึ่ง (pushMessage)
 *   - ส่งข้อความ Broadcast ไปยังทุก follower (broadcast)
 *   - ถ้า config.enabled=false → log ข้อความแทน (ใช้ตอน dev)
 *
 *  ใช้ร่วมกับ: BookingService, PaymentService
 * ============================================================
 */

namespace App\Services;

class LineNotifyService
{
    /** @var array ตั้งค่า LINE / LINE configuration */
    private array $config;

    public function __construct()
    {
        $settings = new IntegrationSettingsService();
        $this->config = $settings->lineConfig(config('line', []));
    }

    /**
     * ส่งข้อความ Push ไปยังผู้ใช้คนหนึ่ง
     * Push message to a specific user
     *
     * @param string $userId LINE user ID
     * @param string $text   ข้อความที่ต้องการส่ง
     * @return array ['success' => bool, 'message' => string]
     */
    public function pushMessage(string $userId, string $text): array
    {
        if (!($this->config['enabled'] ?? false)) {
            $this->log("[PUSH to {$userId}] {$text}");
            return [
                'success' => true,
                'message' => 'LINE ปิดใช้งาน — บันทึก log แทน',
            ];
        }

        $token = $this->config['channel_access_token'] ?? '';
        $url   = $this->config['push_url'] ?? 'https://api.line.me/v2/bot/message/push';

        $payload = [
            'to'       => $userId,
            'messages' => [
                ['type' => 'text', 'text' => $text],
            ],
        ];

        return $this->sendRequest($url, $token, $payload);
    }

    /**
     * ส่งข้อความ Broadcast ไปยังทุก follower
     * Broadcast message to all followers
     *
     * @param string $text ข้อความที่ต้องการส่ง
     * @return array ['success' => bool, 'message' => string]
     */
    public function broadcast(string $text): array
    {
        if (!($this->config['enabled'] ?? false)) {
            $this->log("[BROADCAST] {$text}");
            return [
                'success' => true,
                'message' => 'LINE ปิดใช้งาน — บันทึก log แทน',
            ];
        }

        $token = $this->config['channel_access_token'] ?? '';
        $url   = $this->config['broadcast_url'] ?? 'https://api.line.me/v2/bot/message/broadcast';

        $payload = [
            'messages' => [
                ['type' => 'text', 'text' => $text],
            ],
        ];

        return $this->sendRequest($url, $token, $payload);
    }

    /**
     * ส่งข้อความแจ้งเตือนการจอง
     * Send booking notification
     *
     * @param string $userId
     * @param array  $booking ข้อมูลการจอง
     * @return array
     */
    public function notifyBooking(string $userId, array $booking): array
    {
        $text = sprintf(
            "📅 การจองของคุณ\nรหัส: %s\nวันที่: %s\nเวลา: %s - %s\nสถานะ: %s",
            $booking['booking_code'] ?? '-',
            $booking['booking_date'] ?? '-',
            $booking['start_time'] ?? '-',
            $booking['end_time'] ?? '-',
            $this->thaiStatus($booking['status'] ?? '')
        );

        return $this->pushMessage($userId, $text);
    }

    /**
     * ส่งข้อความแจ้งเตือนการชำระเงิน
     * Send payment notification
     *
     * @param string $userId
     * @param array  $payment ข้อมูลการชำระเงิน
     * @return array
     */
    public function notifyPayment(string $userId, array $payment): array
    {
        $text = sprintf(
            "💰 การชำระเงิน\nจำนวน: %.2f บาท\nสถานะ: %s",
            (float) ($payment['amount'] ?? 0),
            $this->thaiPaymentStatus($payment['status'] ?? '')
        );

        return $this->pushMessage($userId, $text);
    }

    /**
     * ส่ง request ไป LINE API
     * Send HTTP request to LINE API
     *
     * @param string $url
     * @param string $token
     * @param array  $payload
     * @return array
     */
    private function sendRequest(string $url, string $token, array $payload): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr !== '') {
            return ['success' => false, 'message' => 'CURL Error: ' . $curlErr];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'message' => 'ส่งข้อความสำเร็จ'];
        }

        return [
            'success' => false,
            'message' => "LINE API Error (HTTP {$httpCode}): " . $response,
        ];
    }

    /**
     * บันทึก log ลงไฟล์
     * Write log to file
     *
     * @param string $message
     * @return void
     */
    private function log(string $message): void
    {
        $logFile = $this->config['log_file'] ?? __DIR__ . '/../../storage/logs/line.log';
        $dir     = dirname($logFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $line = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * แปลงสถานะการจองเป็นภาษาไทย
     * Translate booking status to Thai
     *
     * @param string $status
     * @return string
     */
    private function thaiStatus(string $status): string
    {
        return match ($status) {
            'pending'    => 'รอดำเนินการ',
            'confirmed'  => 'ยืนยันแล้ว',
            'in_service' => 'กำลังให้บริการ',
            'completed'  => 'เสร็จสิ้น',
            'cancelled'  => 'ยกเลิก',
            default      => $status,
        };
    }

    /**
     * แปลงสถานะการชำระเงินเป็นภาษาไทย
     * Translate payment status to Thai
     *
     * @param string $status
     * @return string
     */
    private function thaiPaymentStatus(string $status): string
    {
        return match ($status) {
            'pending'  => 'รอตรวจสอบ',
            'verified' => 'ยืนยันแล้ว',
            'rejected' => 'ปฏิเสธ',
            default    => $status,
        };
    }
}
