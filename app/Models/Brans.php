<?php
namespace App\Models;

/**
 * Tıbbi Branşlar Modeli
 */
class Brans extends BaseModel {
    
    public $id = null;
    public $bransadi = null;

    public function __construct() {
        // Tablo ismi 'esh_branslar', birincil anahtar 'id'
        parent::__construct('esh_branslar', 'id');
    }

    /**
     * Tüm branşları alfabetik olarak listeler
     */
    public function getList() {
        $sql = "SELECT * FROM esh_branslar ORDER BY bransadi ASC";
        return $this->db->setQuery($sql)->loadObjectList();
    }
}