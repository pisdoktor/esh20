<?php
namespace App\Models;

class Ekip extends BaseModel {
    public $id = null;
    public $tarih = null;
    public $vardiya = null;
    public $ekip_no = null;
    public $user_ids = null;
    public $baslangic_saati = null;
    public $kayit_tarihi = null;

    public function __construct() {
        parent::__construct('esh_ekipler', 'id');
    }

    // Seçilen tarihteki tüm ekipleri getirir
    public function getDailyTeamsList() {
    // Burada user_ids alanlarını "all_user_ids" ismiyle birleştiriyoruz
    $sql = "SELECT tarih, COUNT(id) as ekip_sayisi, 
            GROUP_CONCAT(DISTINCT baslangic_saati SEPARATOR ' / ') as saatler,
            GROUP_CONCAT(user_ids SEPARATOR ',') as all_user_ids
            FROM esh_ekipler 
            GROUP BY tarih 
            ORDER BY tarih DESC";
    
    return $this->db->setQuery($sql)->loadObjectList();
}

    // Virgüllü ID listesini isimlere çevirir
    public function getTeamMemberNames() {
        if (empty($this->user_ids)) return "Personel Atanmamış";
        $cleanIds = preg_replace('/[^0-9,]/', '', $this->user_ids);
        if (empty($cleanIds)) return "Personel Atanmamış";
        
        $sql = "SELECT GROUP_CONCAT(name SEPARATOR ', ') as names FROM esh_users WHERE id IN ($cleanIds)";
        return $this->db->setQuery($sql)->loadResult();
    }
}