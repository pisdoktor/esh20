<?php
namespace App\Controllers;

use App\Models\PlannedVisit;
use App\Models\Patient;

class PlannedVisitController {
    
    public function create() {
        $tc = $_GET['tc'] ?? null; // URL'den gelen TC
        if (!$tc) {
            $_SESSION['error'] = "Geçersiz hasta bilgisi!";
            header("Location: index.php?action=patient");
            exit;
        }

        $patientModel = new Patient();
        $patient = $patientModel->getByTc($tc); 

        include '../views/partials/header.php';
        include '../views/site/izlem/planla.php';
        include '../views/partials/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new PlannedVisit();
            if ($model->store($_POST)) {
                $_SESSION['success'] = "İzlem planı başarıyla kaydedildi.";
                header("Location: index.php?action=hasta_liste");
            } else {
                $_SESSION['error'] = "Plan kaydedilirken bir hata oluştu.";
                header("Location: index.php?action=pizlem&tc=" . $_POST['hastatckimlik']);
            }
            exit;
        }
    }
}