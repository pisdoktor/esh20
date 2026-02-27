<?php
namespace App\Models;

/**
 * Sağlık Güvencesi Modeli
 */
class Guvence extends BaseModel {
    
    public $id = null;
    public $guvenceadi = null;

    public function __construct() {
        // Tablo ismi 'esh_guvence', birincil anahtar 'id'
        parent::__construct('esh_guvence', 'id');
    }

    /**
     * Tüm güvence türlerini alfabetik listeler
     */
    public function getList() {
        $sql = "SELECT * FROM esh_guvence ORDER BY guvenceadi ASC";
        return $this->db->setQuery($sql)->loadObjectList();
    }
}