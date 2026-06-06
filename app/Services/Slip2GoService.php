<?php
/**
 * ============================================================
 *  Slip2GoService.php — บริการตรวจสอบสลิป / Slip Verification Service
 * ============================================================
 *
 *  หน้าที่ / Responsibilities:
 *   - ส่งรูปสลิปไปตรวจสอบกับ Slip2Go API
 *   - เปรียบเทียบจำนวนเงินกับที่คาดหวัง
 *   - บันทึก log สำหรับ debug
 *
 *  ใช้ร่วมกับ: PaymentService
 * ============================================================
 */

namespace App\Services;

class Slip2GoService
{
    /** @var array ตั้งค่า Slip2Go / Slip2Go configuration */
    private array $config;

    public function __construct()
    {
        $settings = new IntegrationSettingsService();
        $this->config = $settings->slip2goConfig(config('slip2go', []));
    }

    /**
     * ตรวจสอบสลิปโอนเงิน
     * Verify a payment slip
     *
     * @param string $imagePath     path ของรูปสลิป / Path to slip image
     * @param float  $expectedAmount จำนวนเงินที่คาดหวัง / Expected amount
     * @return array ['success' => bool, 'verified' => bool, 'data' => array, 'message' => string]
     */
    public function verifySlip(string $imagePath, float $expectedAmount): array
    {
        // ถ้าปิดใช้งาน → คืนค่าไม่ผ่าน / If disabled, return not verified
        if (!($this->config['enabled'] ?? false)) {
            $this->log('Slip2Go disabled — manual verification required');
            return [
                'success'  => true,
                'verified' => false,
                'data'     => [],
                'message'  => 'ระบบตรวจสลิปปิดใช้งาน รอแอดมินตรวจสอบ',
            ];
        }

        // ตรวจสอบว่ามีไฟล์หรือไม่ / Check file exists
        if (!file_exists($imagePath)) {
            return [
                'success'  => false,
                'verified' => false,
                'data'     => [],
                'message'  => 'ไม่พบไฟล์สลิป',
            ];
        }

        $apiKey   = $this->config['api_key'] ?? '';
        $endpoint = $this->config['endpoint'] ?? 'https://api.slip2go.com/api/v1/qr/verify';
        $timeout  = (int) ($this->config['timeout'] ?? 15);

        // อ่านรูปภาพเป็น base64 / Read image as base64
        $imageData = base64_encode(file_get_contents($imagePath));

        // ส่ง request ไป Slip2Go / Send request to Slip2Go
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'image' => 'data:image/jpeg;base64,' . $imageData,
            ]),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr !== '') {
            $this->log('CURL Error: ' . $curlErr);
            return [
                'success'  => false,
                'verified' => false,
                'data'     => [],
                'message'  => 'ไม่สามารถเชื่อมต่อกับ Slip2Go ได้: ' . $curlErr,
            ];
        }

        $data = json_decode($response, true);
        if ($httpCode !== 200 || !is_array($data)) {
            $this->log('Slip2Go HTTP ' . $httpCode . ': ' . $response);
            return [
                'success'  => false,
                'verified' => false,
                'data'     => [],
                'message'  => 'Slip2Go ตอบกลับผิดพลาด (HTTP ' . $httpCode . ')',
            ];
        }

        // ตรวจสอบจำนวนเงิน / Check amount
        $tolerance = (float) ($this->config['amount_tolerance'] ?? 1.00);
        $amount    = (float) ($data['amount'] ?? 0);
        $diff      = abs($amount - $expectedAmount);

        $isVerified = $diff <= $tolerance;

        $this->log(sprintf(
            'Slip2Go: amount=%.2f, expected=%.2f, diff=%.2f, verified=%s',
            $amount,
            $expectedAmount,
            $diff,
            $isVerified ? 'yes' : 'no'
        ));

        return [
            'success'  => true,
            'verified' => $isVerified,
            'data'     => $data,
            'message'  => $isVerified
                ? 'ตรวจสอบสลิปสำเร็จ'
                : 'จำนวนเงินไม่ตรงกัน (สลิป: ' . number_format($amount, 2) . ' บาท)',
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
        $logFile = $this->config['log_file'] ?? __DIR__ . '/../../storage/logs/slip2go.log';
        $dir     = dirname($logFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $line = sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $message);
        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
