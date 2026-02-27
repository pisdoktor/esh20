<?php
namespace App\Controllers;

use App\Models\Ekip;
use App\Models\User;

class EkipController {
    
    public function index() {
    $model = new Ekip();
    // getDailyTeams yerine yeni yazdığımız getDailyTeamsList'i çağırıyoruz
    $items = $model->getDailyTeamsList(); 
    
    include '../views/partials/header.php';
    include '../views/admin/ekip/index.php';
    include '../views/partials/footer.php';
}

    public function edit() {
        $date = $_GET['tarih'] ?? date('Y-m-d');
        $userModel = new User();
        $users = $userModel->db->setQuery("SELECT id, name FROM esh_users WHERE activated=1 ORDER BY name ASC")->loadObjectList();
        
        $model = new Ekip();
        $mevcutlar = $model->db->setQuery("SELECT * FROM esh_ekipler WHERE tarih = '$date' ORDER BY ekip_no ASC")->loadObjectList();
        
        include '../views/partials/header.php';
        include '../views/admin/ekip/edit.php';
        include '../views/partials/footer.php';
    }

    public function saveDaily() {
        $db = \App\Core\Database::getInstance();
        $tarih_ham = $_POST['tarih'] ?? date('d.m.Y');
        $tarih = date('Y-m-d', strtotime($tarih_ham));
        $gelen_ekipler = $_POST['ekipler'] ?? []; 
        $gelen_saatler = $_POST['saatler'] ?? []; 
        
        $db->query("DELETE FROM esh_ekipler WHERE tarih = '$tarih'");

        foreach($gelen_ekipler as $vID => $ekipler_list) {
            $eSayac = 1;
            foreach($ekipler_list as $eNo => $userIDs) {
                if(!empty($userIDs)) {
                    $ekip = new Ekip();
                    $ekip->tarih = $tarih;
                    $ekip->vardiya = $vID;
                    $ekip->ekip_no = $eSayac;
                    $ekip->user_ids = implode(',', (array)$userIDs);
                    $ekip->baslangic_saati = $gelen_saatler[$vID];
                    $ekip->kayit_tarihi = date('Y-m-d H:i:s');
                    $ekip->store();
                    $eSayac++;
                }
            }
        }
        header("Location: index.php?controller=Ekip&action=index");
        exit;
    }

    // PDF Motorun için gereken JSON verisi
    public function getEkiplerJSON() {
        $db = \App\Core\Database::getInstance();
        $mod = $_GET['mod'] ?? 'gunluk';
        $date = $_GET['date'] ?? date('Y-m-d');
        $bas = date('Y-m-d', strtotime($date));
        
        if ($mod == 'haftalik') { $bit = date('Y-m-d', strtotime($bas . ' + 6 days')); }
        elseif ($mod == 'aylik') { $bas = date('Y-m-01', strtotime($bas)); $bit = date('Y-m-t', strtotime($bas)); }
        else { $bit = $bas; }

        $q = "SELECT e.* FROM esh_ekipler as e WHERE e.tarih BETWEEN '$bas' AND '$bit' ORDER BY e.tarih, e.vardiya, e.ekip_no";
        $rows = $db->setQuery($q)->loadObjectList();
        
        $data = [];
        foreach($rows as $r) {
            $p_names = $db->setQuery("SELECT name FROM esh_users WHERE id IN ($r->user_ids)")->loadResultArray();
            $v_ad = ($r->vardiya == 0) ? 'SABAH' : (($r->vardiya == 1) ? 'ÖĞLE' : 'AKŞAM');
            $data[] = [
                'tarih' => date('d.m.Y', strtotime($r->tarih)),
                'vardiya_label' => $v_ad,
                'saat' => $r->baslangic_saati,
                'ekip' => $r->ekip_no . '. Ekip',
                'personeller' => implode(', ', (array)$p_names)
            ];
        }
        echo json_encode($data);
        exit;
    }
}