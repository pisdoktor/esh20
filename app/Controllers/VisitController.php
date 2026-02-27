<?php
namespace App\Controllers;

use App\Models\Izlem;
use App\Models\PlannedVisit;

class VisitController {

    /**
     * Planlanmış bir izlemi "Gerçekleşti" olarak kaydeder
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $izlem = new Visit();
            
            // Form verilerini hazırla
            $data = $_POST;
            $data['personel_id'] = $_SESSION['user_id'];
            $data['islem_tarihi'] = date('Y-m-d H:i:s');

            // 1. esh_izlemler tablosuna kaydı yap
            if ($izlem->save($data)) {
                
                // 2. Eğer bir plandan geliyorsa (plan_id varsa), o planı kapat
                if (!empty($_POST['plan_id'])) {
                    $plan = new PlannedVisit();
                    if ($plan->load($_POST['plan_id'])) {
                        $plan->yapildimi = 1;
                        $plan->store();
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