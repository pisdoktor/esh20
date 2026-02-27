<?php
namespace App\Models;

/**
 * Hastalık Kategori Modeli
 */
class HastalikCat extends BaseModel {
    
    public $id = null;
    public $name = null;
    public $icd_range = null;

    public function __construct() {
        parent::__construct('esh_hastalikcat', 'id');
    }

    /**
     * Tüm kategorileri listeler
     */
    public function getList() {
        $query = "SELECT * FROM esh_hastalikcat ORDER BY id ASC";
        return $this->db->setQuery($query)->loadObjectList();
    }
}