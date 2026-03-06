<?php
namespace App\Controllers;

use App\Models\Stats;

class StatsController {

    public function index() {
    $model = new Stats();
    $task = $_GET['task'] ?? 'dashboard';

    
    
    
    switch($task) {
        case 'patient_list': // Eski StatsHTML::bir
            $filters = [
                'ilce' => $_GET['ilce'] ?? '',
                'mahalle' => $_GET['mahalle'] ?? ''
            ];
            $data['rows'] = $model->getDetailedPatientList($filters);
            $view = 'patient_list';
            break;

        case 'charts': // Grafiksel dökümler
            $data['mahalleler'] = $model->getMahalleCinsiyetStats();
            $data['hastaliklar'] = $model->getHastalikStats();
            $data['yas_gruplari'] = $model->getYasGrubuStats();
            $view = 'charts';
            break;

        default:
            $data['summary'] = $model->getGeneralSummary();
            $view = 'dashboard';
            break;
    }
    
    
    include '../views/partials/header.php'; 
    include '../views/admin/stats/'.$view.'.php';
    include '../views/partials/footer.php'; 
}
}