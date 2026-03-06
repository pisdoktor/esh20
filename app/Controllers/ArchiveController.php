<?php
namespace App\Controllers;

use App\Models\Archive;

class ArchiveController {
    
    public function index() {
        $model = new Archive();
        
        // Filtreleri Yakala
        $filters = [
            'isim'    => trim($_GET['isim'] ?? ''),
            'soyisim' => trim($_GET['soyisim'] ?? ''),
            'mahalle' => $_GET['mahalle'] ?? []
        ];

        $page   = (int)($_GET['page'] ?? 1);
        $limit  = 20;
        $offset = ($page - 1) * $limit;

        $rows       = $model->getArchivedPatients($filters, $limit, $offset);
        $total      = $model->getCount($filters);
        $locations  = $model->getLocationHierarchy();
        $totalPages = ceil($total / $limit);
        $alfabe = ['A','B','C','Ç','D','E','F','G','H','I','İ','J','K','L','M','N','O','Ö','P','R','S','Ş','T','U','Ü','V','Y','Z']; 

        include '../views/partials/header.php';
        include '../views/admin/archive/index.php';
        include '../views/partials/footer.php';
    }
}