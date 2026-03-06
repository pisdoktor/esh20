<?php
namespace App\Controllers;

use App\Models\Visit;
use App\Models\PlannedVisit;
use App\Models\Patient;
use App\Models\Islem;
use App\Models\User;

class VisitController {
    
    public function index() {
        $limit    = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset   = ($page - 1) * $limit;
        $search   = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status   = isset($_GET['status']) ? $_GET['status'] : ''; // '', '1', '0'

        $model = new Visit();
        
        $totalVisits = $model->countAllVisits($search, $status);
        $visits      = $model->getAllVisits($limit, $offset, $search, $status);
        $totalPages  = ceil($totalVisits / $limit);

        // View dosyasına gönderilecek değişkenler
        include '../views/partials/header.php';
        include '../views/site/izlem/index.php'; // Liste sayfası
        include '../views/partials/footer.php';
    }
    
    public function history() {
        
        
        
        
        include '../views/partials/header.php';
        include '../views/site/izlem/history.php';
        include '../views/partials/footer.php';
    }
    
    public function create() {
    
    
        
        
        include '../views/partials/header.php';
        include '../views/site/izlem/create.php';
        include '../views/partials/footer.php';
    }
    
    public function missed() {
    
        
        
        
        include '../views/partials/header.php';
        include '../views/site/izlem/missed.php';
        include '../views/partials/footer.php';
    }

    /**
     * Planlanmış bir izlemi "Gerçekleşti" olarak kaydeder
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $izlem = new Visit();
            
            // Form verilerini hazırla
            $data = $_POST;
            $data['personel_id'] = $_POST['personel_id'] ? $_POST['personel_id'] : $_SESSION['user_id'];
            $data['izlemtarihi'] = $_POST['izlemtarihi'] ? $_POST['izlemtarihi'] :date('Y-m-d');

            // 1. esh_izlemler tablosuna kaydı yap
            if ($izlem->save($data)) {
                
                // 2. Eğer bir plandan geliyorsa (plan_id varsa), o planı sil
                if (!empty($_POST['plan_id'])) {
                    $plan = new PlannedVisit();
                    if ($plan->load($_POST['plan_id'])) {
                        $plan->delete();
                    }
                }
                
                $_SESSION['success'] = "Ziyaret kaydı başarıyla oluşturuldu.";
            } else {
                $_SESSION['error'] = "Ziyaret kaydedilirken bir hata oluştu!";
            }
            
            header("Location: index.php?controller=Dashboard&action=index");
            exit;
        }
    }
}