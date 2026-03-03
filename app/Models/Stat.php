<?php
namespace App\Models;

/**
 * İstatistik ve Dashboard Veri Modeli
 * BaseModel'den miras alır.
 */
class Stat extends BaseModel {

    public function __construct() {
        // İstatistikler genelde hastalar üzerinden yürüdüğü için hastalar tablosu baz alındı
        parent::__construct('#__hastalar', 'id');
    }

    /**
     * Bugün doğum günü olan hastaların ham verisini döner.
     * dogumtarihi 'Y-m-d' formatında olduğu için doğrudan MONTH() ve DAY() fonksiyonları kullanılır.
     */
    public function getTodaysBirthdays() {
        $query = "SELECT 
                    h.id, 
                    h.isim, 
                    h.soyisim, 
                    h.cinsiyet, 
                    h.tckimlik, 
                    h.dogumtarihi, 
                    m.adi AS mahalle, 
                    ilc.adi AS ilce 
                  FROM {$this->_tbl} AS h 
                  LEFT JOIN #__adrestablosu AS m ON m.id = h.mahalle 
                  LEFT JOIN #__adrestablosu AS ilc ON ilc.id = h.ilce 
                  WHERE h.pasif = 0 
                  AND MONTH(h.dogumtarihi) = MONTH(NOW()) 
                  AND DAY(h.dogumtarihi) = DAY(NOW())";
                  
        return $this->db->setQuery($query)->loadObjectList();
    }
    
    /**
 * Yaş gruplarına göre cinsiyet bazlı hasta sayılarını döner
 */
public function getAgeGroups() {
    $query = "SELECT 
        SUM(CASE WHEN dogumtarihi >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) THEN 1 ELSE 0 END) as g01,
        SUM(CASE WHEN dogumtarihi >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) AND dogumtarihi < DATE_SUB(CURDATE(), INTERVAL 2 MONTH) THEN 1 ELSE 0 END) as g22,
        SUM(CASE WHEN dogumtarihi >= DATE_SUB(CURDATE(), INTERVAL 18 YEAR) AND dogumtarihi < DATE_SUB(CURDATE(), INTERVAL 2 YEAR) THEN 1 ELSE 0 END) as g318,
        SUM(CASE WHEN dogumtarihi >= DATE_SUB(CURDATE(), INTERVAL 45 YEAR) AND dogumtarihi < DATE_SUB(CURDATE(), INTERVAL 18 YEAR) THEN 1 ELSE 0 END) as g1945,
        SUM(CASE WHEN dogumtarihi >= DATE_SUB(CURDATE(), INTERVAL 65 YEAR) AND dogumtarihi < DATE_SUB(CURDATE(), INTERVAL 45 YEAR) THEN 1 ELSE 0 END) as g4665,
        SUM(CASE WHEN dogumtarihi >= DATE_SUB(CURDATE(), INTERVAL 85 YEAR) AND dogumtarihi < DATE_SUB(CURDATE(), INTERVAL 65 YEAR) THEN 1 ELSE 0 END) as g6685,
        SUM(CASE WHEN dogumtarihi < DATE_SUB(CURDATE(), INTERVAL 85 YEAR) THEN 1 ELSE 0 END) as g86,
        cinsiyet
        FROM {$this->_tbl} 
        WHERE pasif = 0 
        GROUP BY cinsiyet";

    return $this->db->setQuery($query)->loadObjectList();
}

/**
 * Bu ayki izlem istatistiklerini (toplam hasta, toplam izlem) döner.
 */
public function getMonthlyFollowUpStats() {
    // SQL: Cari ayın ilk gününden son gününe kadar yapılmış (yapildimi=1) izlemleri sayar.
    $query = "SELECT 
                COUNT(DISTINCT i.hastatckimlik) AS toplamhasta, 
                COUNT(i.id) AS toplamizlem 
              FROM #__izlemler AS i 
              WHERE i.izlemtarihi >= DATE_FORMAT(NOW() ,'%Y-%m-01') 
              AND i.izlemtarihi <= LAST_DAY(NOW())
              AND i.yapildimi = 1";

    $this->db->setQuery($query);
    return $this->db->loadObject(); // Tek satır nesne döner
}

/**
 * Bu ayki hastaların takipten çıkarılma nedenlerini ve sayılarını döner.
 */
public function getExitReasons() {
    $query = "SELECT pasifnedeni, COUNT(id) AS sayi 
              FROM {$this->_tbl} 
              WHERE pasif = 1 
              AND pasiftarihi >= DATE_FORMAT(NOW() ,'%Y-%m-01') 
              AND pasiftarihi <= LAST_DAY(NOW()) 
              GROUP BY pasifnedeni";

    return $this->db->setQuery($query)->loadObjectList();
}

/**
 * Bu ay izlemi yapılan hastaların yaş gruplarına göre dağılımını döner.
 */
public function getMonthlyFollowUpAgeGroups() {
    // 1. Önce bu ay izlemi yapılan benzersiz TC kimlikleri alt sorgu ile alıyoruz.
    // 2. Ardından bu hastaların dogumtarihi üzerinden yaşlarını hesaplayıp grupluyoruz.
    $query = "SELECT 
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(h.dogumtarihi, '%Y.%m.%d'), CURDATE()) <= 1 THEN 1 ELSE 0 END) as g01,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(h.dogumtarihi, '%Y.%m.%d'), CURDATE()) = 2 THEN 1 ELSE 0 END) as g22,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(h.dogumtarihi, '%Y.%m.%d'), CURDATE()) BETWEEN 3 AND 18 THEN 1 ELSE 0 END) as g318,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(h.dogumtarihi, '%Y.%m.%d'), CURDATE()) BETWEEN 19 AND 45 THEN 1 ELSE 0 END) as g1945,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(h.dogumtarihi, '%Y.%m.%d'), CURDATE()) BETWEEN 46 AND 65 THEN 1 ELSE 0 END) as g4665,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(h.dogumtarihi, '%Y.%m.%d'), CURDATE()) BETWEEN 66 AND 85 THEN 1 ELSE 0 END) as g6685,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, STR_TO_DATE(h.dogumtarihi, '%Y.%m.%d'), CURDATE()) >= 86 THEN 1 ELSE 0 END) as g86
    FROM (
        SELECT DISTINCT i.hastatckimlik 
        FROM #__izlemler AS i 
        WHERE STR_TO_DATE(i.izlemtarihi, '%d.%m.%Y') BETWEEN DATE_FORMAT(NOW() ,'%Y-%m-01') AND LAST_DAY(NOW()) 
        AND i.yapildimi = 1
    ) as sub
    LEFT JOIN #__hastalar AS h ON h.tckimlik = sub.hastatckimlik 
    WHERE h.dogumtarihi IS NOT NULL";

    return $this->db->setQuery($query)->loadObject();
}

/**
 * Dashboard için genel istatistik sayılarını döner
 */
public function getGeneralStats() {
    $thisyear  = date('Y');
    $thismonth = date('m');

    // Boş şablonlar (Eğer sorgu başarısız olursa sistem çökmesin diye)
    $emptyGeneral = (object)['total_reached' => 0, 'active_total' => 0, 'active_male' => 0, 'active_female' => 0, 'fully_dependent' => 0];
    $emptyNew     = (object)['new_male' => 0, 'new_female' => 0];
    $emptyExit    = (object)['exit_male' => 0, 'exit_female' => 0];

    // 1. Genel Sorgu
    $query1 = "SELECT COUNT(id) as total_reached, ... (Sorgunun Devamı) ...";
    $stats = $this->db->setQuery($query1)->loadObject() ?: $emptyGeneral;

    // 2. Yeni Hasta Sorgusu
    $query2 = "SELECT ... FROM {$this->_tbl} WHERE pasif = '0' AND kayityili = '$thisyear' AND kayitay = '$thismonth'";
    $newPatients = $this->db->setQuery($query2)->loadObject() ?: $emptyNew;

    // 3. Çıkarılan Hasta Sorgusu
    $query3 = "SELECT ... FROM {$this->_tbl} WHERE pasif = '1' AND pasiftarihi >= ...";
    $exitPatients = $this->db->setQuery($query3)->loadObject() ?: $emptyExit;

    return (object)[
        'general' => $stats,
        'new'     => $newPatients,
        'exit'    => $exitPatients
    ];
}

    /**
     * Örnek: Toplam aktif hasta sayısını döner
     */
    public function getActivePatientCount() {
        $query = "SELECT COUNT(id) FROM {$this->_tbl} WHERE pasif = 0";
        return $this->db->setQuery($query)->loadResult();
    }
}