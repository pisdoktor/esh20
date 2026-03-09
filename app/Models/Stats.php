<?php
namespace App\Models;

class Stats extends BaseModel {
    
    public function __construct() {
        parent::__construct('esh_hastalar', 'id');
    }

    /**
     * Mahalle bazlı hasta sayısı dökümü
     */
    public function getMahalleStats() {
        $query = "SELECT m.adi as mahalle_adi, il.adi as ilce_adi, 
                  COUNT(h.id) as toplam_hasta,
                  SUM(CASE WHEN h.cinsiyet = 'E' THEN 1 ELSE 0 END) as erkek_sayisi,
                  SUM(CASE WHEN h.cinsiyet = 'K' THEN 1 ELSE 0 END) as kadin_sayisi
                  FROM esh_hastalar as h
                  LEFT JOIN esh_adrestablosu as m ON m.id = h.mahalle
                  LEFT JOIN esh_adrestablosu as il ON il.id = h.ilce
                  WHERE h.pasif = '0'
                  GROUP BY h.mahalle
                  ORDER BY il.adi ASC, m.adi ASC";
        return $this->db->setQuery($query)->loadObjectList();
    }

    /**
     * Kayıt yılına göre hasta dağılımı
     */
    public function getKayitYiliStats() {
    $query = "SELECT YEAR(kayittarihi) as kayityili, 
              SUM(CASE WHEN cinsiyet = 'E' THEN 1 ELSE 0 END) as erkek_sayisi,
              SUM(CASE WHEN cinsiyet = 'K' THEN 1 ELSE 0 END) as kadin_sayisi,
              COUNT(id) as toplam_sayi
              FROM esh_hastalar 
              WHERE pasif = '0' AND kayittarihi IS NOT NULL AND kayittarihi != '0000-00-00'
              GROUP BY YEAR(kayittarihi) 
              ORDER BY YEAR(kayittarihi) ASC";
    return $this->db->setQuery($query)->loadObjectList();
}

    /**
     * Kayıt ayına göre döküm
     */
    public function getKayitAyiStats() {
    // Hem yıl hem ay bilgisi alarak grupluyoruz
    $query = "SELECT YEAR(kayittarihi) as kayityili, MONTH(kayittarihi) as kayitay, 
              SUM(CASE WHEN cinsiyet = 'E' THEN 1 ELSE 0 END) as erkek_sayisi,
              SUM(CASE WHEN cinsiyet = 'K' THEN 1 ELSE 0 END) as kadin_sayisi,
              COUNT(id) as toplam_sayi
              FROM esh_hastalar 
              WHERE pasif = '0' AND kayittarihi IS NOT NULL AND kayittarihi != '0000-00-00'
              GROUP BY YEAR(kayittarihi), MONTH(kayittarihi) 
              ORDER BY YEAR(kayittarihi) DESC, MONTH(kayittarihi) DESC";
    return $this->db->setQuery($query)->loadObjectList();
}

    /**
     * Genel özet (Toplam hasta, Aktif, Pasif, Erkek, Kadın)
     */
    public function getGeneralSummary() {
        $query = "SELECT 
                  COUNT(id) as toplam,
                  SUM(CASE WHEN pasif = '0' THEN 1 ELSE 0 END) as aktif,
                  SUM(CASE WHEN pasif = '1' THEN 1 ELSE 0 END) as pasif,
                  SUM(CASE WHEN cinsiyet = 'E' AND pasif = '0' THEN 1 ELSE 0 END) as erkek,
                  SUM(CASE WHEN cinsiyet = 'K' AND pasif = '0' THEN 1 ELSE 0 END) as kadin
                  FROM esh_hastalar";
        return $this->db->setQuery($query)->loadObject();
    }
    
     public function getDetailedPatientList($filters = [], $limit = 50, $offset = 0) {
        $where = ["h.pasif = '0'"];
        if (!empty($filters['ilce'])) $where[] = "h.ilce = " . $this->db->quote($filters['ilce']);
        if (!empty($filters['mahalle'])) $where[] = "h.mahalle = " . $this->db->quote($filters['mahalle']);
        
        $whereSql = " WHERE " . implode(' AND ', $where);
        $query = "SELECT h.*, m.adi as mahalle, il.adi as ilce 
                  FROM esh_hastalar as h
                  LEFT JOIN esh_adrestablosu as m ON m.id = h.mahalle
                  LEFT JOIN esh_adrestablosu as il ON il.id = h.ilce
                  $whereSql 
                  ORDER BY h.isim ASC LIMIT $limit OFFSET $offset";
        return $this->db->setQuery($query)->loadObjectList();
    }

    // TASK: hMahalle -> Mahalle/Cinsiyet Dağılımı
    public function getMahalleCinsiyetStats() {
        return $this->db->setQuery("SELECT m.adi as mahalle_adi, il.adi as ilce_adi, 
            SUM(CASE WHEN h.cinsiyet = 'E' THEN 1 ELSE 0 END) as erkek,
            SUM(CASE WHEN h.cinsiyet = 'K' THEN 1 ELSE 0 END) as kadin,
            COUNT(h.id) as toplam
            FROM esh_hastalar as h
            LEFT JOIN esh_adrestablosu as m ON m.id = h.mahalle
            LEFT JOIN esh_adrestablosu as il ON il.id = h.ilce
            WHERE h.pasif = '0' GROUP BY h.mahalle ORDER BY il.adi, m.adi")->loadObjectList();
    }

    // TASK: hHastalik -> Hastalık Grupları (Yeni Eklenen)
    public function getHastalikStats() {
        return $this->db->setQuery("SELECT hastalik, COUNT(id) as sayi 
            FROM esh_hastalar WHERE pasif = '0' AND hastalik != '' 
            GROUP BY hastalik ORDER BY sayi DESC")->loadObjectList();
    }

    // Yardımcı: Yaş hesaplama mantığını SQL'e gömelim
    public function getYasGrubuStats() {
        $query = "SELECT 
            SUM(CASE WHEN (YEAR(CURDATE()) - YEAR(dogumtarihi)) < 18 THEN 1 ELSE 0 END) as cocuk,
            SUM(CASE WHEN (YEAR(CURDATE()) - YEAR(dogumtarihi)) BETWEEN 18 AND 65 THEN 1 ELSE 0 END) as yetiskin,
            SUM(CASE WHEN (YEAR(CURDATE()) - YEAR(dogumtarihi)) > 65 THEN 1 ELSE 0 END) as yasli
            FROM esh_hastalar WHERE pasif = '0'";
        return $this->db->setQuery($query)->loadObject();
    }
}