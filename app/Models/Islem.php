<?php
namespace App\Models;

/**
 * Yapılan İşlemler Modeli
 */
class Islem extends BaseModel {
    
    public $id = null;
    public $islemadi = null;

    public function __construct() {
        // Tablo ismi 'esh_islemler', birincil anahtar 'id'
        parent::__construct('esh_islemler', 'id');
    }

    /**
     * Tüm işlemleri listeler
     */
    public function getList() {
        $sql = "SELECT * FROM esh_islemler ORDER BY islemadi ASC";
        return $this->db->setQuery($sql)->loadObjectList();
    }
}