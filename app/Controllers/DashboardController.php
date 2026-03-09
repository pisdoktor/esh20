<?php
namespace App\Controllers;

use App\Models\PlannedVisit;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Islem;

class DashboardController {
    
    public function index() {
        $plannedModel = new PlannedVisit();
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');

        $calendarData = $plannedModel->getMonthPlans($year, $month);
        $calendarHtml = $this->renderCalendar($year, $month, $calendarData);
        
        $currentMonthName = ["", "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"][(int)$month];
        
        // Navigasyon hesaplama
        $prevMonth = $month - 1; $prevYear = $year;
        if ($prevMonth == 0) { $prevMonth = 12; $prevYear--; }
        $nextMonth = $month + 1; $nextYear = $year;
        if ($nextMonth == 13) { $nextMonth = 1; $nextYear++; }   

        $pageTitle = "Planlı İşlem Takvimi";
        include '../views/partials/header.php';
        include '../views/site/dashboard.php';
        include '../views/partials/footer.php';
    }

    private function renderCalendar($year, $month, $plans) {
        $firstDay = date('N', strtotime("$year-$month-01"));
        $daysInMonth = date('t', strtotime("$year-$month-01"));
        $daysOfWeek = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];

        $html = '<table class="table table-bordered m-0"><thead><tr class="text-center">';
        foreach ($daysOfWeek as $d) $html .= "<th>$d</th>";
        $html .= '</tr></thead><tbody><tr>';

        for ($i = 1; $i < $firstDay; $i++) $html .= '<td class="bg-light"></td>';

        for ($day = 1; $day <= $daysInMonth; $day++) {
            if (($day + $firstDay - 2) % 7 == 0 && $day != 1) $html .= '</tr><tr>';
            
            $currentDate = sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day);
            $isToday = ($currentDate == date('Y-m-d')) ? 'table-primary' : '';

            $html .= "<td class='calendar-day $isToday' onclick='getDailyTasks(\"$currentDate\")' style='cursor:pointer; height:110px; vertical-align:top;'>";
            $html .= "<strong>$day</strong><div class='mt-1'>";

            // ROZET KONTROLLERİ (P, N, Y, +)
            if (isset($plans['resProc'][$currentDate])) {
                $p = $plans['resProc'][$currentDate];
             
                if ($p->normal_total > 0) $html .= "<div class='badge bg-primary w-100 mb-1 x-small'>$p->normal_total İzlem</div>";
                if ($p->ozel_total > 0) $html .= "<div class='badge bg-info text-dark w-100 mb-1 x-small'>$p->ozel_total Nakil</div>";
            }
            
            if (isset($plans['resPansuman'][$currentDate])) {
                $html .= "<div class='badge bg-warning w-100 mb-1 x-small'>+ {$plans['resPansuman'][$currentDate]->total} Pansuman</div>";
            }
            
            if (isset($plans['resFirst'][$currentDate])) {
                $html .= "<div class='badge bg-danger w-100 mb-1 x-small'>+ {$plans['resFirst'][$currentDate]->total} İlk Kayıt</div>";
            }
            
            
            
            if (isset($plans['resDone'][$currentDate])) {
                $html .= "<div class='badge bg-success w-100 mb-1 x-small'>{$plans['resDone'][$currentDate]->total} Yapılan</div>";
            }

            $html .= "</div></td>";
        }
        $html .= '</tr></tbody></table>';
        return $html;
    }

    public function getDailyEvents() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $model = new PlannedVisit();
        header('Content-Type: application/json');
        echo json_encode($model->getDailyPlans($date));
        exit;
    }

    
    // AJAX: Rota Hesaplama (Eski case 'rota')
    public function getRoute() {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $date = $_GET['date'] ?? date('Y-m-d');
    
    $visit = new \App\Models\PlannedVisit();

    // DİKKAT: Artık düz getShiftsByDate değil, 
    // Süper-Formüllü calculateSmartRoute metodunu çağırıyoruz.
    $smartData = $visit->calculateSmartRoute($date);
    
  
    $configs = [
        0 => ['color' => '#FFC107', 'label' => 'Sabah'], // Sabah vardiyası anahtarı 0 ise
        1 => ['color' => '#17A2B8', 'label' => 'Öğle'],
        2 => ['color' => '#343A40', 'label' => 'Akşam']
    ];

  $formattedData = [];

// Çıktıda smartData doğrudan ekipleri barındırıyor (0: Sabah, 1: Öğle, 2: Akşam)
foreach ($smartData as $ekipNo => $ekip) {
    // Eğer o ekibin hastası yoksa atla
    if (empty($ekip['hastalar'])) continue;

    $points = [];
    
    // 1. Merkez Noktası (START_LAT, START_LNG gibi sabitler tanımlı olmalı)
    $points[] = [
        'name' => START_NAME,
        'lat'  => (float)START_LAT,
        'lng'  => (float)START_LNG,
        'is_center' => true
    ];

    // 2. Hastaları ekle
    foreach ($ekip['hastalar'] as $h) {
        $coords = trim($h->coords);
        if (empty($coords)) continue;

        $parts = explode(',', $coords);
        $lat = isset($parts[0]) ? (float)trim($parts[0]) : 0;
        $lng = isset($parts[1]) ? (float)trim($parts[1]) : 0;

        if ($lat != 0 && $lng != 0) {
            $points[] = [
                'name' => htmlspecialchars($h->isim . ' ' . $h->soyisim),
                'lat'  => $lat,
                'lng'  => $lng,
                'is_center' => false,
                'varis_saati' => $h->varis_saati
            ];
        }
    }

    // Vardiya ismini ve rengini ekipNo'ya (0,1,2) göre belirle
    $formattedData[] = [
        'key'   => 'ekip_' . $ekipNo,
        'label' => $ekip['isim'], // "Sabah Ekibi 1" gibi
        'color' => $configs[$ekipNo]['color'] ?? '#007bff',
        'points' => $points
    ];
}

echo json_encode(['success' => true, 'data' => $formattedData]);
exit;
}
    
    public function admin() {
        $pageTitle = "Yönetim Paneli";
        include '../views/partials/header.php';
        include '../views/admin/dashboard/index.php';
        include '../views/partials/footer.php';
    }
    
    public function showRoute() {
    $date = $_GET['date'] ?? date('Y-m-d');
    $pageTitle = "Günlük Ekip Rotaları";
    
    // Header, yeni view ve footer birleştiriliyor
    include '../views/partials/header.php';
    include '../views/site/rota_detay.php';
    include '../views/partials/footer.php';
}
}