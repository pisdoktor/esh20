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

    //aktif hastalar
    public function countAllActive($search = '') {
    $where = "WHERE h.pasif = 0";
    // JOIN her zaman olmalı ki a1.adi sütununa erişebilelim
    $sql = "SELECT COUNT(h.id) FROM esh_hastalar as h 
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id";
    
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    return $this->db->setQuery($sql . " " . $where)->loadResult();
}

    public function getAllActive($limit = 20, $offset = 0, $ordering = 'h.isim ASC', $search = '') {
    $where = "WHERE h.pasif = 0";
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    $sql = "SELECT h.*, a1.adi as ilce_adi, a2.adi as mahalle_adi, a3.adi AS sokak_adi, a4.adi AS kapino,
            (SELECT izlemtarihi FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1 ORDER BY izlemtarihi DESC LIMIT 1) as sonizlemtarihi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1) as izlemsayisi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 0) as yizlemsayisi,
            (SELECT COUNT(id) FROM esh_pizlemler WHERE hastatckimlik = h.tckimlik) as totalplanli
            FROM esh_hastalar as h
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id
            LEFT JOIN esh_adrestablosu as a2 ON h.mahalle = a2.id
            LEFT JOIN esh_adrestablosu as a3 ON h.sokak = a3.id
            LEFT JOIN esh_adrestablosu as a4 ON h.kapino = a4.id
            {$where}
            GROUP BY h.id
            ORDER BY {$ordering}";
    
    return $this->db->setQuery($sql, $offset, $limit)->loadObjectList();
}
    //pasif hastaları getir
    public function countAllPassive($search = '', $reason = '', $startDate = '', $endDate = '') {
    $where = "WHERE h.pasif = 1";
    // JOIN her zaman olmalı ki a1.adi sütununa erişebilelim
    $sql = "SELECT COUNT(h.id) FROM esh_hastalar as h 
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id";
    
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }
    
    if (!empty($reason)) {
        $where .= " AND h.pasifnedeni = " . $this->db->quote($reason);
    }
    
    // Tarih dönüşüm fonksiyonunu yardımcı bir değişkenle yönetelim
    if (!empty($startDate) || !empty($endDate)) {
    
    // Sadece başlangıç tarihi varsa
    if (!empty($startDate) && empty($endDate)) {
        $sDate = date('Y-m-d', strtotime(str_replace('.', '-', $startDate)));
        $where .= " AND h.pasiftarihi >= " . $this->db->quote($sDate);
    } 
    // Sadece bitiş tarihi varsa
    elseif (empty($startDate) && !empty($endDate)) {
        $eDate = date('Y-m-d', strtotime(str_replace('.', '-', $endDate)));
        $where .= " AND h.pasiftarihi <= " . $this->db->quote($eDate);
    } 
    // Her ikisi de varsa (Senin senaryon)
    else {
        $sDate = date('Y-m-d', strtotime(str_replace('.', '-', $startDate)));
        $eDate = date('Y-m-d', strtotime(str_replace('.', '-', $endDate)));
        $where .= " AND h.pasiftarihi BETWEEN " . $this->db->quote($sDate) . " AND " . $this->db->quote($eDate);
    }
}

    return $this->db->setQuery($sql . " " . $where)->loadResult();
}

    public function getAllPassive($limit = 20, $offset = 0, $ordering = 'h.isim ASC', $search = '', $reason = '', $startDate = '', $endDate = '') {
    $where = "WHERE h.pasif = 1";
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }
    
    if (!empty($reason)) {
        $where .= " AND h.pasifnedeni = " . $this->db->quote($reason);
    }
    
    // Tarih dönüşüm fonksiyonunu yardımcı bir değişkenle yönetelim
    if (!empty($startDate) || !empty($endDate)) {
    
    // Sadece başlangıç tarihi varsa
    if (!empty($startDate) && empty($endDate)) {
        $sDate = date('Y-m-d', strtotime(str_replace('.', '-', $startDate)));
        $where .= " AND h.pasiftarihi >= " . $this->db->quote($sDate);
    } 
    // Sadece bitiş tarihi varsa
    elseif (empty($startDate) && !empty($endDate)) {
        $eDate = date('Y-m-d', strtotime(str_replace('.', '-', $endDate)));
        $where .= " AND h.pasiftarihi <= " . $this->db->quote($eDate);
    } 
    // Her ikisi de varsa (Senin senaryon)
    else {
        $sDate = date('Y-m-d', strtotime(str_replace('.', '-', $startDate)));
        $eDate = date('Y-m-d', strtotime(str_replace('.', '-', $endDate)));
        $where .= " AND h.pasiftarihi BETWEEN " . $this->db->quote($sDate) . " AND " . $this->db->quote($eDate);
    }
}

    $sql = "SELECT DISTINCT h.*, a1.adi as ilce_adi, a2.adi as mahalle_adi, a3.adi AS sokak_adi, a4.adi AS kapino,
            (SELECT izlemtarihi FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1 ORDER BY izlemtarihi DESC LIMIT 1) as sonizlemtarihi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1) as izlemsayisi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 0) as yizlemsayisi,
            (SELECT COUNT(id) FROM esh_pizlemler WHERE hastatckimlik = h.tckimlik) as totalplanli
            FROM esh_hastalar as h
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id
            LEFT JOIN esh_adrestablosu as a2 ON h.mahalle = a2.id
            LEFT JOIN esh_adrestablosu as a3 ON h.sokak = a3.id
            LEFT JOIN esh_adrestablosu as a4 ON h.kapino = a4.id
            {$where}
            GROUP BY h.id
            ORDER BY {$ordering}";
    
    return $this->db->setQuery($sql, $offset, $limit)->loadObjectList();
}
    //bekleyen hastalar
    public function countAllWaiting($search = '') {
        $where = "WHERE h.pasif = -3";
    // JOIN her zaman olmalı ki a1.adi sütununa erişebilelim
    $sql = "SELECT COUNT(h.id) FROM esh_hastalar as h 
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id";
    
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    return $this->db->setQuery($sql . " " . $where)->loadResult();
    }
    
    public function getAllWaiting($limit = 20, $offset = 0, $ordering = 'h.isim ASC', $search = '') {
        $where = "WHERE h.pasif = -3";
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }
        $query = "SELECT h.*, a1.adi as ilce_adi, a2.adi as mahalle_adi, a3.adi AS sokak_adi, a4.adi AS kapino 
                  FROM esh_hastalar h
                  LEFT JOIN esh_adrestablosu a1 ON h.ilce = a1.id
                  LEFT JOIN esh_adrestablosu a2 ON h.mahalle = a2.id
                  LEFT JOIN esh_adrestablosu a3 ON h.sokak = a3.id
                  LEFT JOIN esh_adrestablosu a4 ON h.kapino = a4.id
                  {$where} 
                  ORDER BY {$ordering}";
        
        return $this->db->setQuery($query, $offset, $limit)->loadObjectList();
    }
    
    //ölen hastalar
    public function countAllDied($search = '') {
    $where = "WHERE h.pasif = '-1'";
    // JOIN her zaman olmalı ki a1.adi sütununa erişebilelim
    $sql = "SELECT COUNT(h.id) FROM esh_hastalar as h 
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id";
    
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    return $this->db->setQuery($sql . " " . $where)->loadResult();
    }
    
    public function getAllDied($limit = 20, $offset = 0, $ordering = 'h.isim ASC', $search = '') {
     $where = "WHERE h.pasif = '-1'";
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    $sql = "SELECT h.*, a1.adi as ilce_adi, a2.adi as mahalle_adi, a3.adi AS sokak_adi, a4.adi AS kapino,
            (SELECT izlemtarihi FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1 ORDER BY izlemtarihi DESC LIMIT 1) as sonizlemtarihi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1) as izlemsayisi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 0) as yizlemsayisi,
            (SELECT COUNT(id) FROM esh_pizlemler WHERE hastatckimlik = h.tckimlik) as totalplanli
            FROM esh_hastalar as h
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id
            LEFT JOIN esh_adrestablosu as a2 ON h.mahalle = a2.id
            LEFT JOIN esh_adrestablosu as a3 ON h.sokak = a3.id
            LEFT JOIN esh_adrestablosu as a4 ON h.kapino = a4.id
            {$where}
            GROUP BY h.id
            ORDER BY {$ordering}";
    
    return $this->db->setQuery($sql, $offset, $limit)->loadObjectList();
    }
    //silinen hastalar
    public function countAllDeleted($search = '') {
    $where = "WHERE h.pasif = 5";
    // JOIN her zaman olmalı ki a1.adi sütununa erişebilelim
    $sql = "SELECT COUNT(h.id) FROM esh_hastalar as h 
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id";
    
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    return $this->db->setQuery($sql . " " . $where)->loadResult();
    }
    
    public function getAllDeleted($limit = 20, $offset = 0, $ordering = 'h.isim ASC', $search = '') {
    $where = "WHERE h.pasif = 5";
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    $sql = "SELECT h.*, a1.adi as ilce_adi, a2.adi as mahalle_adi, a3.adi AS sokak_adi, a4.adi AS kapino,
            (SELECT izlemtarihi FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1 ORDER BY izlemtarihi DESC LIMIT 1) as sonizlemtarihi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1) as izlemsayisi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 0) as yizlemsayisi,
            (SELECT COUNT(id) FROM esh_pizlemler WHERE hastatckimlik = h.tckimlik) as totalplanli
            FROM esh_hastalar as h
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id
            LEFT JOIN esh_adrestablosu as a2 ON h.mahalle = a2.id
            LEFT JOIN esh_adrestablosu as a3 ON h.sokak = a3.id
            LEFT JOIN esh_adrestablosu as a4 ON h.kapino = a4.id
            {$where}
            GROUP BY h.id
            ORDER BY {$ordering}";
    
    return $this->db->setQuery($sql, $offset, $limit)->loadObjectList();
    }
    //muhtemel ölenler
    public function countAllAraf($search = '') {
    $where = "WHERE h.pasif = 4";
    // JOIN her zaman olmalı ki a1.adi sütununa erişebilelim
    $sql = "SELECT COUNT(h.id) FROM esh_hastalar as h 
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id";
    
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    return $this->db->setQuery($sql . " " . $where)->loadResult();
    }
    
    public function getAllAraf($limit = 20, $offset = 0, $ordering = 'h.isim ASC', $search = '') {
    $where = "WHERE h.pasif = 4";
    if (!empty($search)) {
        $where .= " AND (h.isim LIKE '%{$search}%' 
                    OR h.soyisim LIKE '%{$search}%' 
                    OR h.tckimlik LIKE '%{$search}%' 
                    OR a1.adi LIKE '%{$search}%')";
    }

    $sql = "SELECT h.*, a1.adi as ilce_adi, a2.adi as mahalle_adi, a3.adi AS sokak_adi, a4.adi AS kapino,
            (SELECT izlemtarihi FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1 ORDER BY izlemtarihi DESC LIMIT 1) as sonizlemtarihi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 1) as izlemsayisi,
            (SELECT COUNT(id) FROM esh_izlemler WHERE hastatckimlik = h.tckimlik AND yapildimi = 0) as yizlemsayisi,
            (SELECT COUNT(id) FROM esh_pizlemler WHERE hastatckimlik = h.tckimlik) as totalplanli
            FROM esh_hastalar as h
            LEFT JOIN esh_adrestablosu as a1 ON h.ilce = a1.id
            LEFT JOIN esh_adrestablosu as a2 ON h.mahalle = a2.id
            LEFT JOIN esh_adrestablosu as a3 ON h.sokak = a3.id
            LEFT JOIN esh_adrestablosu as a4 ON h.kapino = a4.id
            {$where}
            GROUP BY h.id
            ORDER BY {$ordering}";
    
    return $this->db->setQuery($sql, $offset, $limit)->loadObjectList();
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
    
    //Hastayı pasife alma
    public function setPassive($reason, $type, $date = null) {
        $this->pasif = $type;
        $this->pasifnedeni = (int)$reason;
        $this->pasiftarihi = $date ?? date('Y-m-d');
        return $this->store(); // BaseModel'deki kaydetme yeteneğini kullanır
    }
    /**
     * Raporun süresinin dolmak üzere olup olmadığını kontrol eder.
     * * @param string $type Rapor türü: 'mama', 'bez' veya 'sonda'
     * @param int $days Kaç gün kala uyarı verileceği (Varsayılan: 15)
     * @return bool
     */
    public function isReportExpiring($type = 'mama', $days = 15) {
        // 1. Tip bazlı özellik ismini belirleyelim
        if ($type == 'mama') {
            $prop = 'mamaraporbitis';
        } elseif ($type == 'bez') {
            $prop = 'bezraporbitis';
        } elseif ($type == 'sonda') {
            $prop = 'sondatarihi';
        } else {
            return false; // Bilinmeyen bir tip gelirse false dön
        }

        // 2. Veritabanındaki tarih boş mu kontrol edelim
        if (empty($this->$prop)) return false;

        // 3. Bitiş tarihini hesaplayalım
        if ($type == 'sonda') {
            // Sonda için: sondatarihi + 30 gün
            $expiryDate = strtotime($this->$prop . " +30 days");
        } else {
            // Mama ve Bez için: Doğrudan ilgili sütun tarihi
            $expiryDate = strtotime($this->$prop);
        }

        // 4. Uyarı limitini hesaplayalım (Bugün + $days)
        $warningLimit = strtotime("+$days days");

        // Bitiş tarihi uyarı limitinin içindeyse true döner
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
    
    /**
    * Ölüm kontrolünde kullanılan fonksiyon
    */
    public function died($tc) {
    
        $sql = "SELECT 
                isim, soyisim, anneAdi, babaAdi 
                FROM esh_hastalar 
                WHERE tckimlik = {$tc}";
                
        return $this->db->setQuery($sql)->loadObject();
    
    }
    
    // 20'şerli paket çekme (Sıralama tckimlik ASC)
    public function getPatientsForScan($offset = 0, $limit = 20) {
        $query = "SELECT id, tckimlik, isim, soyisim, anneAdi, babaAdi 
                  FROM esh_hastalar 
                  WHERE pasif = '0' 
                  ORDER BY tckimlik ASC 
                  LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->db->setQuery($query)->loadObjectList();
    }
    
    public function countPatientsForScan() {
        $query = "SELECT COUNT(id) FROM esh_hastalar WHERE pasif = '0'";
        
        return $this->db->setQuery($query)->loadResult();
    }
}