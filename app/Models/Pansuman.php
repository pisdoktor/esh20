<?php
namespace App\Models;

class Pansuman extends BaseModel {
    
    public function __construct() {
        // Hastalar tablosu üzerinden pansuman filtresiyle çalışacağız
        parent::__construct('esh_hastalar', 'id');
    }

    /**
     * Pansuman hastalarını filtreli, sayfalı ve gün bazlı getirir
     */
    public function getPansumanList($search = '', $filter_day = '', $limit = 20, $offset = 0) {
        $where = ["pasif = 0", "pansuman = 1"];
        
        // İsim/TC Araması
        if (!empty($search)) {
            $searchStr = $this->db->quote('%' . $search . '%');
            $where[] = "(isim LIKE $searchStr OR soyisim LIKE $searchStr OR tckimlik LIKE $searchStr)";
        }

        // GÜN FİLTRESİ (FIND_IN_SET kullanımı)
        if (!empty($filter_day)) {
            $day = (int)$filter_day;
            // pgunleri '1,2,5' gibi saklandığı için FIND_IN_SET ile süzüyoruz
            $where[] = "FIND_IN_SET('$day', pgunleri)";
        }

        $query = "SELECT h.*, m.adi AS mahalle, il.adi AS ilce 
                  FROM {$this->_tbl} AS h
                  LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce
                  LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle
                  WHERE " . implode(' AND ', $where) . "
                  ORDER BY h.isim ASC
                  LIMIT $limit OFFSET $offset";
        
        return $this->db->setQuery($query)->loadObjectList();
    }

    /**
     * Toplam sayı (Pagination için)
     */
    public function getPansumanCount($search = '', $filter_day = '') {
        $where = ["pasif = 0", "pansuman = 1"];
        
        if (!empty($search)) {
            $searchStr = $this->db->quote('%' . $search . '%');
            $where[] = "(isim LIKE $searchStr OR soyisim LIKE $searchStr OR tckimlik LIKE $searchStr)";
        }

        if (!empty($filter_day)) {
            $day = (int)$filter_day;
            $where[] = "FIND_IN_SET('$day', pgunleri)";
        }

        $query = "SELECT COUNT(id) FROM {$this->_tbl} WHERE " . implode(' AND ', $where);
        return $this->db->setQuery($query)->loadResult();
    }
}