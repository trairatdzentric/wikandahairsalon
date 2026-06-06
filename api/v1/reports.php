<?php
/**
 * ============================================================
 *  api/v1/reports.php — Reports API
 * ============================================================
 *
 *  Endpoints:
 *   GET /api/v1/reports/revenue/day?date=YYYY-MM-DD
 *   GET /api/v1/reports/revenue/month?month=YYYY-MM
 *   GET /api/v1/reports/revenue/year?year=YYYY
 *   GET /api/v1/reports/top-services?limit=5
 *   GET /api/v1/reports/today
 * ============================================================
 */

use App\Services\ReportService;

$method = $_SERVER['REQUEST_METHOD'];
$report = new ReportService();

if ($method !== 'GET') {
    jsonResponse(false, null, 'Method not allowed', 405);
}

requireRole(['admin', 'owner']);

$subResource = $parts[2] ?? '';
$subAction   = $parts[3] ?? '';

switch ($subResource) {

    // --------------------------------------------------------
    // /api/v1/reports/revenue/day?date=YYYY-MM-DD
    // /api/v1/reports/revenue/month?month=YYYY-MM
    // /api/v1/reports/revenue/year?year=YYYY
    // --------------------------------------------------------
    case 'revenue':
        switch ($subAction) {
            case 'day':
                $date = $_GET['date'] ?? date('Y-m-d');
                jsonResponse(true, $report->revenueByDay($date));
                break;

            case 'month':
                $month = $_GET['month'] ?? date('Y-m');
                jsonResponse(true, $report->revenueByMonth($month));
                break;

            case 'year':
                $year = $_GET['year'] ?? date('Y');
                jsonResponse(true, $report->revenueByYear($year));
                break;

            default:
                jsonResponse(false, null, 'ระบุประเภทรายงานไม่ถูกต้อง (day/month/year)', 400);
        }
        break;

    // --------------------------------------------------------
    // /api/v1/reports/top-services?limit=5
    // --------------------------------------------------------
    case 'top-services':
        $limit = (int) ($_GET['limit'] ?? 5);
        jsonResponse(true, $report->topServices($limit));
        break;

    // --------------------------------------------------------
    // /api/v1/reports/today
    // --------------------------------------------------------
    case 'today':
        jsonResponse(true, $report->todaySummary());
        break;

    // --------------------------------------------------------
    // อื่น ๆ
    // --------------------------------------------------------
    default:
        jsonResponse(false, null, 'ไม่พบรายงานที่ระบุ', 404);
}
