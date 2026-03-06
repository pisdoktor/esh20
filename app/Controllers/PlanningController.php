<?php
namespace App\Controllers;

use App\Models\Planning;

class PlanningController {
    
    public function index() {
    // UUID olduğu için (int) cast işlemini sildik, string olarak aldık
    $ilce_id = isset($_GET['ilce']) ? $_GET['ilce'] : ''; 
    $page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    $limit   = 20;
    $offset  = ($page - 1) * $limit;

    $model = new Planning();
    $rows  = $model->getPlanningList($ilce_id, $limit, $offset);
    $total = $model->getPlanningCount($ilce_id);
    $districts = $model->getDistricts();
    
    $totalPages = ceil($total / $limit);
    $gunler = [1=>'Pzt', 2=>'Sal', 3=>'Çar', 4=>'Per', 5=>'Cum', 6=>'Cmt', 7=>'Paz'];

    include '../views/partials/header.php';
    include '../views/admin/planning/index.php';
    include '../views/partials/footer.php';
}

public function table() {
    $model = new \App\Models\Planning();
    $rows = $model->getMasterPlanData();

    // Eski kodundaki gün gruplama mantığı
    $gruplar = [
        'P.TESİ - PERŞEMBE' => ['1', '4'],
        'SALI - CUMA'       => ['2', '5'],
        'ÇARŞAMBA'          => ['3'],
        'HAFTA SONU'        => ['6', '0']
    ];

    $matris = [];
    $doluBolgeler = [];

    if ($rows) {
        foreach ($rows as $row) {
            $m_gunler = explode(',', $row->gun);
            foreach ($gruplar as $g_isim => $g_kodlar) {
                // Mahallenin günü bu grupta var mı?
                if (count(array_intersect($m_gunler, $g_kodlar)) > 0) {
                    $matris[$g_isim][$row->bolge][] = $row;
                    $doluBolgeler[$g_isim][$row->bolge] = true;
                }
            }
        }
    }

    include '../views/partials/header.php';
    include '../views/admin/planning/table.php'; // Yeni view dosyası
    include '../views/partials/footer.php';
}

    public function save() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $model = new \App\Models\Planning();
        
        $id = $_POST['id'] ?? ''; // UUID (String)
        
        if (empty($id)) {
            die("Hata: Geçersiz ID.");
        }

        // Form verilerini hazırla
        $gunlerStr = isset($_POST['gun']) ? implode(',', $_POST['gun']) : '';
        $bolgeNo   = isset($_POST['bolge']) ? $_POST['bolge'] : '0';

        // BaseModel'in save metoduna doğrudan dizi gönderiyoruz
        // Bu yöntem load() hatasını bypass eder
        $data = [
            'id'    => $id,
            'bolge' => $bolgeNo,
            'gun'   => $gunlerStr
        ];

        // Eğer BaseModel::save($data) yapısını destekliyorsa:
        if ($model->save($data)) {
            $ilce = $_POST['current_ilce'] ?? '0';
            $page = $_POST['current_page'] ?? '1';
            header("Location: index.php?controller=Planning&action=index&ilce=$ilce&page=$page&msg=ok");
            exit();
        } else {
            // Hata varsa ekrana bas (Beyaz sayfa kalmasın)
            echo "Kayıt sırasında bir hata oluştu. Veritabanı UUID formatını desteklemiyor olabilir veya sütun isimleri hatalı.";
            exit();
        }
    }
}
}