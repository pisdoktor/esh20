<?php
namespace App\Models;

/**
 * Ziyaret Modeli
 * Gerçekleşen ziyaretlerin (esh_izlemler) kaydı için kullanılır.
 */
class Visit extends BaseModel {
    
    // Veritabanı sütunları
    public $id = null;
    public $hastatckimlik = null;
    public $izlemtarihi = null;
    public $yapilan = null;
    public $yapildimi = 0;
    public $neden = null;
    public $izlemiyapan = null;
    public $zaman = null;
    public $aciklama = null;

    public function __construct() {
        // 'esh_izlemler' tablosunu kullan, birincil anahtar 'id'
        parent::__construct('esh_izlemler', 'id');
    }
    
    public function getAllVisits($limit = 20, $offset = 0, $search = '', $filterYapildi = '', $ordering = 'i.izlemtarihi DESC') {
        $where = [];
        
        // Arama filtresi (TC Kimlik veya İsim)
        if (!empty($search)) {
            $search = $this->db->quote('%' . $search . '%');
            $where[] = "(i.hastatckimlik LIKE $search OR h.isim LIKE $search OR h.soyisim LIKE $search)";
        }

        // Yapıldı/Yapılmadı Filtresi
        if ($filterYapildi !== '') {
            $where[] = "i.yapildimi = " . (int)$filterYapildi;
        }

        $whereSql = count($where) ? " WHERE " . implode(" AND ", $where) : "";

        $query = "SELECT i.*, h.isim, h.soyisim, h.cinsiyet, isl.islemadi, 
                         il.adi AS ilce, m.adi AS mahalle 
                  FROM {$this->_tbl} AS i 
                  LEFT JOIN esh_hastalar AS h ON h.tckimlik = i.hastatckimlik 
                  LEFT JOIN esh_islemler AS isl ON isl.id = i.yapilan 
                  LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce 
                  LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle 
                  $whereSql 
                  ORDER BY $ordering 
                  LIMIT $limit OFFSET $offset";
        
        return $this->db->setQuery($query)->loadObjectList();
    }

    public function countAllVisits($search = '', $filterYapildi = '') {
        $where = [];
        if (!empty($search)) {
            $search = $this->db->quote('%' . $search . '%');
            $where[] = "(i.hastatckimlik LIKE $search OR h.isim LIKE $search OR h.soyisim LIKE $search)";
        }
        if ($filterYapildi !== '') {
            $where[] = "i.yapildimi = " . (int)$filterYapildi;
        }

        $whereSql = count($where) ? " WHERE " . implode(" AND ", $where) : "";

        $query = "SELECT COUNT(i.id) FROM {$this->_tbl} AS i 
                  LEFT JOIN esh_hastalar AS h ON h.tckimlik = i.hastatckimlik 
                  $whereSql";
        return $this->db->setQuery($query)->loadResult();
    }

    
    /**
 * Ziyarette yapılan işlemleri (tek veya çoklu) isim olarak getirir.
 * Veritabanında '1,4,7' gibi saklandığını varsayar.
 */
public function getYapilanIslemler() {
    if (empty($this->yapilan)) {
        return "İşlem belirtilmemiş";
    }

    // ID'leri güvenli hale getirelim (Örn: "1,4,7")
    $ids = $this->yapilan; 
    
    $sql = "SELECT GROUP_CONCAT(islemadi SEPARATOR ', ') as islem_listesi 
            FROM esh_islemler 
            WHERE id IN ($ids)";
            
    return $this->db->setQuery($sql)->loadResult();
}

// ÖNERİ: Virgüllü işlem ID'lerini isimlere dönüştür
    public function getYapilanIslemIsimleri() {
        if (empty($this->yapilan)) return "İşlem Yok";
        $sql = "SELECT GROUP_CONCAT(islemadi SEPARATOR ' + ') FROM esh_islemler WHERE id IN ({$this->yapilan})";
        return $this->db->setQuery($sql)->loadResult();
    }

    // ÖNERİ: İzlemi yapan personelin adını getir
    public function getYapanPersonel() {
        $sql = "SELECT name FROM esh_users WHERE id = " . (int)$this->izlemiyapan;
        return $this->db->setQuery($sql)->loadResult();
    }
}