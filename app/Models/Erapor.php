<?php
namespace App\Models;

/**
 * Elektronik Rapor (e-Rapor) Modeli
 */
class Erapor extends BaseModel {
    
    // Veritabanı sütunları
    public $id = null;
    public $hastatckimlik = null;
    public $isim = null;
    public $soyisim = null;
    public $ceptel1 = null;
    public $basvurutarihi = null;
    public $brans = null;
    public $kayitlimi = 0;
    public $yenilendimi = 0;
    public $neden = null;

    public function __construct() {
        // Tablo ismi 'esh_eraporlar', birincil anahtar 'id'
        parent::__construct('esh_erapor', 'id');
    }

    /**
     * Tüm e-raporları tarih sırasına göre getirir
     */
    public function getAllReports() {
        $sql = "SELECT * FROM esh_erapor ORDER BY id DESC";
        return $this->db->setQuery($sql)->loadObjectList();
    }

    /**
     * Gelen raporun T.C. Kimlik numarasının sistemde (esh_hastalar) 
     * kayıtlı olup olmadığını kontrol eder.
     */
    public function matchWithSystem() {
    if (!empty($this->hastatckimlik)) {
        // esh_hastalar tablosunda bu TC var mı kontrol et
        $sql = "SELECT id FROM esh_hastalar WHERE tckimlik = " . $this->db->quote($this->hastatckimlik);
        $exists = $this->db->setQuery($sql)->loadResult();
        
        // Varsa kayitlimi alanını 1 yap ve kaydet
        $this->kayitlimi = $exists ? 1 : 0;
        return $this->store(); // BaseModel'deki store metodunu kullanır
    }
    return false;
}
    
    // Erapor.php içine eklenebilir
public function getReportsWithBrans() {
    $sql = "SELECT e.*, b.bransadi 
            FROM esh_erapor e
            LEFT JOIN esh_branslar b ON e.brans = b.id
            ORDER BY e.basvurutarihi DESC";
    return $this->db->setQuery($sql)->loadObjectList();
}
}