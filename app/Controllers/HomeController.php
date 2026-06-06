<?php
/**
 * ============================================================
 *  HomeController.php — ควบคุมหน้าสาธารณะ / Public Pages Controller
 * ============================================================
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Repositories\ServiceRepository;

class HomeController extends Controller
{
    /**
     * หน้าแรก / Homepage
     */
    public function index(): void
    {
        $serviceRepo = new ServiceRepository();
        $services    = $serviceRepo->findActive();

        View::render('home/index', [
            'title'    => 'หน้าแรก',
            'services' => $services,
        ]);
    }

    /**
     * หน้ารายการบริการ / Services page
     */
    public function services(): void
    {
        $serviceRepo = new ServiceRepository();
        $services    = $serviceRepo->findActive();

        View::render('home/services', [
            'title'    => 'บริการของเรา',
            'services' => $services,
        ]);
    }

    /**
     * หน้าเกี่ยวกับร้าน / About page
     */
    public function about(): void
    {
        View::render('home/about', [
            'title' => 'เกี่ยวกับร้าน',
        ]);
    }
}
