<?php
namespace App\Models;

class Archive extends BaseModel {
    
    public function __construct() {
        // Ana tablomuz hastalar tablosu
        parent::__construct('esh_hastalar', 'id');
    }

    /**
     * Arşivlenmiş hastaları izlem istatistikleriyle birlikte getirir
     */
    public function getArchivedPatients($filters = [], $limit = 20, $offset = 0) {
        $where = ["h.pasif = 0"];

        // Alfabetik isim filtresi
        if (!empty($filters['isim'])) {
            $where[] = "h.isim LIKE " . $this->db->quote($filters['isim'] . '%');
        }

        // Soyisim filtresi
        if (!empty($filters['soyisim'])) {
            $where[] = "h.soyisim LIKE " . $this->db->quote('%' . $filters['soyisim'] . '%');
        }

        // Mahalle filtresi (Dizi olarak gelir)
        if (!empty($filters['mahalle'])) {
            $mahalleler = is_array($filters['mahalle']) ? $filters['mahalle'] : [$filters['mahalle']];
            $mahalleIds = array_map('intval', $mahalleler);
            $where[] = "h.mahalle IN (" . implode(',', $mahalleIds) . ")";
        }

        $whereSql = " WHERE " . implode(' AND ', $where);

        // SQL Sorgusu: Select kısmına izlemsayisi ve sonizlemtarihi'ni ekliyoruz
        $query = "SELECT h.*, m.adi AS mahalleadi, ilc.adi AS ilceadi,
                 (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik) AS izlemsayisi,
                 (SELECT MAX(izlemtarihi) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik) AS sonizlemtarihi
                 FROM esh_hastalar AS h
                 LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle
                 LEFT JOIN esh_adrestablosu AS ilc ON ilc.id = h.ilce
                 $whereSql
                 ORDER BY h.isim ASC, h.soyisim ASC
                 LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->db->setQuery($query)->loadObjectList();
    }

    /**
     * Sayfalama için toplam kayıt sayısı
     */
    public function getCount($filters = []) {
        $where = ["pasif = 0"];
        
        if (!empty($filters['isim'])) $where[] = "isim LIKE " . $this->db->quote($filters['isim'] . '%');
        if (!empty($filters['soyisim'])) $where[] = "soyisim LIKE " . $this->db->quote('%' . $filters['soyisim'] . '%');
        if (!empty($filters['mahalle'])) {
            $mahalleler = is_array($filters['mahalle']) ? $filters['mahalle'] : [$filters['mahalle']];
            $where[] = "mahalle IN (" . implode(',', array_map('intval', $mahalleler)) . ")";
        }
        
        $whereSql = " WHERE " . implode(' AND ', $where);
        $query = "SELECT COUNT(id) FROM esh_hastalar $whereSql";
        
        return $this->db->setQuery($query)->loadResult();
    }

    /**
     * Filtre paneli için mahalle hiyerarşisi
     */
    public function getLocationHierarchy() {
        $query = "SELECT m.id, m.adi AS mahalle, i.adi AS ilce, i.id AS ilce_id 
                  FROM esh_adrestablosu AS m 
                  LEFT JOIN esh_adrestablosu AS i ON i.id = m.ust_id 
                  WHERE m.tip='mahalle' 
                  ORDER BY i.adi ASC, m.adi ASC";
        $rows = $this->db->setQuery($query)->loadObjectList();
        
        $hierarchy = [];
        if ($rows) {
            foreach ($rows as $row) {
                $hierarchy[$row->ilce][] = $row;
            }
        }
        return $hierarchy;
    }
}