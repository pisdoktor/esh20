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
        $sql = "SELECT name_surname FROM esh_users WHERE id = " . (int)$this->izlemiyapan;
        return $this->db->setQuery($sql)->loadResult();
    }
}