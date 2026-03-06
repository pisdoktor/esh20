<?php
namespace App\Models;

class Planning extends BaseModel {
    
    // Tablo sütunlarını model mülkü olarak ekleyelim
    public $id;
    public $bolge;
    public $gun;

    public function __construct() {
        parent::__construct('esh_adrestablosu', 'id');
    }

    public function getPlanningList($ilce_id = '', $limit = 20, $offset = 0) {
        $where = ["m.tip = 'mahalle'"];
        
        // UUID olduğu için (int) zorlamasını kaldırdık, quote ile güvenli hale getirdik
        if (!empty($ilce_id) && $ilce_id !== '0') {
            $where[] = "m.ust_id = " . $this->db->quote($ilce_id);
        }

        $whereSql = " WHERE " . implode(" AND ", $where);

        $query = "SELECT m.*, i.adi AS ilce_adi 
                  FROM esh_adrestablosu AS m
                  LEFT JOIN esh_adrestablosu AS i ON i.id = m.ust_id
                  $whereSql
                  ORDER BY i.adi ASC, m.adi ASC
                  LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->db->setQuery($query)->loadObjectList();
    }

    public function getPlanningCount($ilce_id = '') {
        $where = ["tip = 'mahalle'"];
        if (!empty($ilce_id) && $ilce_id !== '0') {
            $where[] = "ust_id = " . $this->db->quote($ilce_id);
        }
        
        $whereSql = " WHERE " . implode(" AND ", $where);
        return $this->db->setQuery("SELECT COUNT(id) FROM esh_adrestablosu $whereSql")->loadResult();
    }

    public function getDistricts() {
        return $this->db->setQuery("SELECT id, adi FROM esh_adrestablosu WHERE tip='ilce' ORDER BY adi ASC")->loadObjectList();
    }
    
    /**
 * Haftalık Planlama Çizelgesi (Matris) için verileri getirir
 */
public function getMasterPlanData() {
    $query = "SELECT m.*, i.adi AS ilce_adi, 
             (SELECT COUNT(h.id) FROM esh_hastalar AS h WHERE h.mahalle = m.id AND h.pasif = 0) AS hastasayisi 
             FROM esh_adrestablosu AS m 
             LEFT JOIN esh_adrestablosu AS i ON i.id = m.ust_id 
             WHERE m.bolge > 0 AND m.gun != '' AND m.tip = 'mahalle'
             ORDER BY m.bolge ASC, m.adi ASC";
    
    return $this->db->setQuery($query)->loadObjectList();
}
}