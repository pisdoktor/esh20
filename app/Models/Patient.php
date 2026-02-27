<?php
namespace App\Models;

/**
 * Hasta Modeli
 * BaseModel'den miras alarak bind, store ve save yeteneklerini kullanır.
 */
class Patient extends BaseModel {
    // Veritabanı tablo sütunları (Özellikler)
    // Kimlik Bilgileri
    public $id = null;
    public $tckimlik = null;
    public $isim = null;
    public $soyisim = null;
    public $anneAdi = null;
    public $babaAdi = null;
    public $dogumtarihi = null;
    public $cinsiyet = null;

    // Fiziksel Bilgiler
    public $kilo = null;
    public $boy = null;

    // İletişim ve Adres Bilgileri
    public $kayittarihi = null;
    public $ceptel1 = null;
    public $ceptel2 = null;
    public $guvence = null;
    public $yupasno = null;
    public $ilce = null;
    public $mahalle = null;
    public $sokak = null;
    public $kapino = null;
    public $adres_aciklama = null;
    public $diger_adres = null;
    public $coords = null;

    // Sağlık Durumu ve Bağımlılık (Barthel İndeksi vb.)
    public $bagimlilik = null;
    public $barbeslenme = null;
    public $barbanyo = null;
    public $barbakim = null;
    public $bargiyinme = null;
    public $barbarsak = null;
    public $barmesane = null;
    public $bartuvalet = null;
    public $bartransfer = null;
    public $barmobilite = null;
    public $barmerdiven = null;

    // Durum Bilgileri
    public $pasif = 0;
    public $pasiftarihi = null;
    public $pasifnedeni = null;
    public $gecici = 0;

    // Tıbbi Cihaz ve Destek Bilgileri
    public $ng = 0; // Nasogastrik tüp
    public $peg = 0; // Perkütan Endoskopik Gastrostomi
    public $port = 0;
    public $o2bagimli = 0;
    public $ventilator = 0;
    public $kolostomi = 0;
    public $sonda = 0;
    public $sondatarihi = null;

    // Bakım ve Sarf Malzeme
    public $pansuman = 0;
    public $pgunleri = null;
    public $pzaman = null;
    public $mama = 0;
    public $mamacesit = null;
    public $mamaraporbitis = null;
    public $mamaraporyeri = null;
    public $bez = 0;
    public $bezrapor = 0;
    public $bezraporbitis = null;
    public $yatak = 0;

    // Genel Notlar ve Randevu
    public $hastaliklar = null;
    public $erapor = null;
    public $randevutarihi = null;
    public $zaman = null;
    public $notes = null;

    public function __construct() {
        // 'esh_hastalar' tablosunu kullan, birincil anahtar 'id'
        parent::__construct('esh_hastalar', 'id');
    }

    /**
     * Hastaları Adres Bilgileriyle Birlikte Listeler
     * Senin alıştığın setQuery ve loadObjectList yapısını kullanır.
     */
    public function getAllActive($limit = 10, $offset = 0) {
        $query = "SELECT h.*, i.adi as ilce_adi, m.adi as mahalle_adi 
                  FROM esh_hastalar h
                  LEFT JOIN esh_adrestablosu i ON h.ilce = i.id
                  LEFT JOIN esh_adrestablosu m ON h.mahalle = m.id
                  WHERE pasif=0 ORDER BY h.id DESC";
        
        return $this->db->setQuery($query, $offset, $limit)->loadObjectList();
    }
    
    //pasif hastaları getir
    public function getAllPassive($limit = 10, $offset = 0) {
        $query = "SELECT h.*, i.adi as ilce_adi, m.adi as mahalle_adi 
                  FROM esh_hastalar h
                  LEFT JOIN esh_adrestablosu i ON h.ilce = i.id
                  LEFT JOIN esh_adrestablosu m ON h.mahalle = m.id
                  WHERE pasif=1 ORDER BY h.id DESC";
        
        return $this->db->setQuery($query, $offset, $limit)->loadObjectList();
    }
    
    public function getAllWaiting($limit = 10, $offset = 0) {
        $query = "SELECT h.*, i.adi as ilce_adi, m.adi as mahalle_adi 
                  FROM esh_hastalar h
                  LEFT JOIN esh_adrestablosu i ON h.ilce = i.id
                  LEFT JOIN esh_adrestablosu m ON h.mahalle = m.id
                  WHERE pasif='-3' ORDER BY h.id DESC";
        
        return $this->db->setQuery($query, $offset, $limit)->loadObjectList();
    }
    
    public function getAllDied($limit = 10, $offset = 0) {
        $query = "SELECT h.*, i.adi as ilce_adi, m.adi as mahalle_adi 
                  FROM esh_hastalar h
                  LEFT JOIN esh_adrestablosu i ON h.ilce = i.id
                  LEFT JOIN esh_adrestablosu m ON h.mahalle = m.id
                  WHERE pasif='-1' ORDER BY h.id DESC";
        
        return $this->db->setQuery($query, $offset, $limit)->loadObjectList();
    }
    
    public function getAllDeleted($limit = 10, $offset = 0) {
        $query = "SELECT h.*, i.adi as ilce_adi, m.adi as mahalle_adi 
                  FROM esh_hastalar h
                  LEFT JOIN esh_adrestablosu i ON h.ilce = i.id
                  LEFT JOIN esh_adrestablosu m ON h.mahalle = m.id
                  WHERE pasif='5' ORDER BY h.id DESC";
        
        return $this->db->setQuery($query, $offset, $limit)->loadObjectList();
    }
    
    public function getAllAraf($limit = 10, $offset = 0) {
        $query = "SELECT h.*, i.adi as ilce_adi, m.adi as mahalle_adi 
                  FROM esh_hastalar h
                  LEFT JOIN esh_adrestablosu i ON h.ilce = i.id
                  LEFT JOIN esh_adrestablosu m ON h.mahalle = m.id
                  WHERE pasif='4' ORDER BY h.id DESC";
        
        return $this->db->setQuery($query, $offset, $limit)->loadObjectList();
    }

    /**
     * Toplam aktif hasta sayısını döndürür
     */
    public function countAllActive() {
        $query = "SELECT COUNT(*) FROM esh_hastalar WHERE pasif=0";
        return $this->db->setQuery($query)->loadResult();
    }
    
    // pasif hasta sayısı
    public function countAllPassive() {
        $query = "SELECT COUNT(*) FROM esh_hastalar WHERE pasif=1";
        return $this->db->setQuery($query)->loadResult();
    }
    
    public function countAllWaiting() {
        $query = "SELECT COUNT(*) FROM esh_hastalar WHERE pasif='-3'";
        return $this->db->setQuery($query)->loadResult();
    }
    
    public function countAllDied() {
        $query = "SELECT COUNT(*) FROM esh_hastalar WHERE pasif='-1'";
        return $this->db->setQuery($query)->loadResult();
    }
    
    public function countAllDeleted() {
        $query = "SELECT COUNT(*) FROM esh_hastalar WHERE pasif='5'";
        return $this->db->setQuery($query)->loadResult();
    }
    
    public function countAllAraf() {
        $query = "SELECT COUNT(*) FROM esh_hastalar WHERE pasif='4'";
        return $this->db->setQuery($query)->loadResult();
    }
    

    /**
     * TC Kimlik numarasına göre tek bir hasta nesnesi döndürür
     */
    public function findByTc($tc) {
        $query = "SELECT * FROM esh_hastalar WHERE tckimlik = " . $this->db->quote($tc);
        return $this->db->setQuery($query)->loadObject();
    }

    /**
     * ID'ye göre tek bir hasta nesnesi döndürür
     */
    public function getById($id) {
        $query = "SELECT * FROM esh_hastalar WHERE id = " . (int)$id;
        return $this->db->setQuery($query)->loadObject();
    }
    
    // ÖNERİ: Toplam Barthel Puanını ve Durumunu Hesapla
    public function getBarthelScore() {
        $total = $this->barbeslenme + $this->barbanyo + $this->barbakim + $this->bargiyinme + 
                 $this->barbarsak + $this->barmesane + $this->bartuvalet + $this->bartransfer + 
                 $this->barmobilite + $this->barmerdiven;
        
        $status = "Bağımsız";
        if($total <= 20) $status = "Tam Bağımlı";
        elseif($total <= 60) $status = "Ağır Bağımlı";
        elseif($total <= 90) $status = "Orta Bağımlı";
        
        return ['score' => $total, 'status' => $status];
    }
    
    public function setPassive($reason, $date = null) {
        $this->pasif = 1;
        $this->pasifnedeni = (int)$reason;
        $this->pasiftarihi = $date ?? date('Y-m-d');
        return $this->store(); // BaseModel'deki kaydetme yeteneğini kullanır
    }
    
    /**
     * Rapor süresi yaklaşan hastaları kontrol eder (Mama veya Bez)
     * @param string $type 'mama' veya 'bez'
     * @param int $days Kaç gün kala uyarı versin?
     */
    public function isReportExpiring($type = 'mama', $days = 15) {
        $prop = ($type == 'mama') ? 'mamaraporbitis' : 'bezraporbitis';
        if (empty($this->$prop)) return false;

        $expiryDate = strtotime($this->$prop);
        $warningLimit = strtotime("+$days days");

        return $expiryDate <= $warningLimit;
    }

    /**
     * TC Kimlik numarası geçerlilik kontrolü (Algoritmik)
     */
    public function validateTc($tc) {
    // Parametre olarak gelen $tc'yi kullanıyoruz
    $tc = (string)$tc;
    
    // Temel kontroller: 11 hane, sadece rakam, ilk hane 0 olamaz
    if (strlen($tc) != 11 || !ctype_digit($tc) || $tc[0] == '0') return false;
    
    $digits = str_split($tc);
    $oddSum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8];
    $evenSum = $digits[1] + $digits[3] + $digits[5] + $digits[7];
    
    // Negatif sonuç ihtimaline karşı +10 ekleyip tekrar mod alıyoruz
    $digit10 = (($oddSum * 7) - $evenSum) % 10;
    if ($digit10 < 0) $digit10 += 10; 
    
    $digit11 = (array_sum(array_slice($digits, 0, 10))) % 10;

    return ($digits[9] == $digit10 && $digits[10] == $digit11);
}
    
    /**
     * Hastanın hastalık ID'lerini dizi (array) olarak döndürür
     */
    public function getDiseaseArray() {
        return !empty($this->hastaliklar) ? explode(',', $this->hastaliklar) : [];
    }

    /**
     * Hastaya ait hastalık isimlerini veritabanından çekerek döndürür
     */
    public function getDiseaseNames() {
        if (empty($this->hastaliklar)) return 'Belirtilmemiş';

        $ids = $this->hastaliklar; // "1,4,5"
        $query = "SELECT GROUP_CONCAT(hastalikadi SEPARATOR ', ') as isimler 
                  FROM esh_hastaliklar 
                  WHERE id IN ($ids)";
        
        return $this->db->setQuery($query)->loadResult();
    }
}