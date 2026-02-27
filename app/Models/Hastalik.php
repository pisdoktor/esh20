<?php
namespace App\Models;

/**
 * Hastalık Kütüphanesi Modeli
 */
class Hastalik extends BaseModel {
    
    // Veritabanı sütunları
    public $id = null;
    public $cat = 0;
    public $hastalikadi = null;
    public $icd = null;

    public function __construct() {
        // 'esh_hastaliklar' tablosunu kullan, birincil anahtar 'id'
        parent::__construct('esh_hastaliklar', 'id');
    }

    /**
     * Tüm hastalıkları alfabetik sırayla getirir
     * Formlardaki select/dropdown listeleri için kullanılır.
     */
    public function getList() {
        $query = "SELECT * FROM esh_hastaliklar ORDER BY cat ASC, hastalikadi ASC";
        return $this->db->setQuery($query)->loadObjectList();
    }
    
    public function getListWithCategory() {
    $query = "SELECT h.*, c.name as kategori_adi 
              FROM esh_hastaliklar h
              LEFT JOIN esh_hastalikcat c ON h.cat = c.id
              ORDER BY c.name, h.hastalikadi ASC";
    return $this->db->setQuery($query)->loadObjectList();
}

/**
 * Hastalıkları bağlı oldukları kategorilerle birlikte getirir
 */
public function getDetailedList() {
    $sql = "SELECT h.*, c.name as kategori_adi, c.icd_range 
            FROM esh_hastaliklar h
            LEFT JOIN esh_hastalikcat c ON h.cat = c.id
            ORDER BY c.id ASC, h.hastalikadi ASC";
    
    return $this->db->setQuery($sql)->loadObjectList();
}
}