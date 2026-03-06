<?php
namespace App\Controllers;

use App\Models\Pansuman;
use App\Models\Patient;

class PansumanController {
    
    public function index() {
        // Parametreleri yakala
        $search     = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filter_day = isset($_GET['filter_day']) ? $_GET['filter_day'] : '';
        $page       = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        $limit      = 20;
        $offset     = ($page - 1) * $limit;

        $model = new Pansuman();
        
        // Verileri Modelden yeni filtreyle çek
        $rows       = $model->getPansumanList($search, $filter_day, $limit, $offset);
        $total      = $model->getPansumanCount($search, $filter_day);
        $totalPages = ceil($total / $limit);

        // Arayüz için gerekli sabit diziler
        $gunler   = [1=>'Pzt', 2=>'Sal', 3=>'Çar', 4=>'Per', 5=>'Cum', 6=>'Cmt', 7=>'Paz'];
        $zamanlar = [0=>'Sabah', 1=>'Öğle', 2=>'Akşam'];

        include '../views/partials/header.php';
        include '../views/site/pansuman/index.php';
        include '../views/partials/footer.php';
    }

    /**
     * Hastanın pansuman günlerini kaydeder
     */
    public function saveDays() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $model = new Patient();
            
            if ($model->load($id)) {
                // Günler dizi olarak gelir (1,2,3...), veritabanına virgüllü string kaydederiz
                $days = isset($_POST['pgunleri']) ? implode(',', $_POST['pgunleri']) : '';
                $pzaman = isset($_POST['pzaman']) ? $_POST['pzaman'] : '0';

                $data = [
                    'id' => $id,
                    'pgunleri' => $days,
                    'pzaman' => $pzaman
                ];
            
                
                if ($model->save($data)) {
                    $_SESSION['success'] = "Pansuman planı güncellendi.";
                } else {
                    $_SESSION['error'] = "Pansuman planı kaydedilemedi"; 
                }
            }
            header("Location: index.php?controller=Pansuman&action=index");
            exit();
        }
    }
}