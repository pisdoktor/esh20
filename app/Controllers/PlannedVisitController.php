<?php
namespace App\Controllers;

use App\Models\PlannedVisit;
use App\Models\Patient;
use App\Models\Islem;
use App\Models\User;

class PlannedVisitController {
    
    public function index() {
        $limit    = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset   = ($page - 1) * $limit;
        $search   = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status   = isset($_GET['status']) ? $_GET['status'] : '0'; // Varsayılan: Bekleyenler

        $model = new PlannedVisit();
        
        $totalItems = $model->countAllPlanned($search, $status);
        $plans      = $model->getAllPlanned($limit, $offset, $search, $status);
        $totalPages = ceil($totalItems / $limit);

        include '../views/partials/header.php';
        include '../views/site/izlem/pindex.php'; // Plan listesi view dosyası
        include '../views/partials/footer.php';
    }
    
    public function create() {
        $tc = $_GET['tc'] ?? null; // URL'den gelen TC
        if (!$tc) {
            $_SESSION['error'] = "Geçersiz hasta bilgisi!";
            header("Location: index.php?&action=listactive");
            exit;
        }

        $patientModel = new Patient();
        $patient = $patientModel->findByTc($tc); 
        
        $islemler = (new Islem())->getList();
        
        $list['islem'] = \App\Helpers\FormHelper::selectList($islemler, 'yapilacak[]', 'multiple="multiple"', 'id', 'islemadi');
        
        $planlayan = (new User())->getList();
        
        $list['personel'] = \App\Helpers\FormHelper::selectList($planlayan, 'planiyapan[]', 'multiple="multiple"', 'id', 'name', $_SESSION['user_id']);

        include '../views/partials/header.php';
        include '../views/site/izlem/planla.php';
        include '../views/partials/footer.php';
    }

    public function store() {

            $pizlem = new PlannedVisit();
            
            $data = $_POST;
            
            $patient = (new Patient())->findByTc($data['hastatckimlik']);  

            $data['yapilacak'] = implode(',', $data['yapilacak']);
            $data['planiyapan'] = implode(',', $data['planiyapan']);

            unset($data['id']);
            
            $pizlem->bind($data);

            if ($pizlem->store()) {
                $_SESSION['success'] = "İzlem planı başarıyla kaydedildi.";
                header("Location: index.php?controller=Visit&action=history&tc=".$data['hastatckimlik']);
            } else {
                $_SESSION['error'] = "Plan kaydedilirken bir hata oluştu.";
                header("Location: index.php?controller=Visit&action=history&tc=".$data['hastatckimlik']);
            }

    }
}