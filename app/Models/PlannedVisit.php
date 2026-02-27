<?php
namespace App\Models;

/**
 * Planlı İzlem Modeli
 */
class PlannedVisit extends BaseModel {
    public $id = null;
    public $hastatckimlik = null;
    public $planlanantarih = null;
    public $yapilacak = null;
    public $zaman = null;
    public $planiyapan = null;
    public $plantarihi = null;
    public $oncelik = 1;
    public $aciklama = null;

    public function __construct() {
        parent::__construct('esh_pizlemler', 'id');
    }
    
// app/Models/PlannedVisit.php içindeki getMonthPlans metodu:

    public function getMonthPlans($year, $month) {
    
        $startDate = sprintf('%04d-%02d-01', (int)$year, (int)$month);
        $endDate = date("Y-m-t", strtotime($startDate));
        
        $list = ['resProc' => [], 'resDone' => [], 'resFirst' => []];

        // 1. Planlı İzlemler (P ve N) - Anahtar: planlanantarih
        $query1 = "SELECT DATE_FORMAT(planlanantarih, '%Y-%m-%d') as tarih, 
                   SUM(CASE WHEN yapilacak = 23 THEN 1 ELSE 0 END) as ozel_total,
                   SUM(CASE WHEN yapilacak != 23 THEN 1 ELSE 0 END) as normal_total
                   FROM esh_pizlemler 
                   WHERE planlanantarih BETWEEN '{$startDate}' AND '{$endDate}' 
                   GROUP BY planlanantarih";
        $rawResults = $this->db->setQuery($query1)->loadObjectList();
        
        foreach($rawResults as $row) {
            $list['resProc'][$row->tarih] = $row;
        }

        // 2. Yapılan İzlemler (Y) - Anahtar: izlemtarihi
        $query2 = "SELECT DATE_FORMAT(izlemtarihi, '%Y-%m-%d') as tarih, COUNT(id) as total 
                   FROM esh_izlemler 
                   WHERE izlemtarihi BETWEEN '{$startDate}' AND '{$endDate}' AND yapildimi=1 
                   GROUP BY izlemtarihi";
        $rawResults2 = $this->db->setQuery($query2)->loadObjectList();
        
        foreach($rawResults2 as $row2) {
            $list['resDone'][$row2->tarih] = $row2;
        }

        // 3. İlk Ziyaretler (+) - Anahtar: randevutarihi
        $query3 = "SELECT DATE_FORMAT(randevutarihi, '%Y-%m-%d') as tarih, COUNT(id) as total 
                   FROM esh_hastalar 
                   WHERE randevutarihi BETWEEN '{$startDate}' AND '{$endDate}' AND pasif='-3' 
                   GROUP BY randevutarihi";
        $rawResults3 = $this->db->setQuery($query3)->loadObjectList();
        
        foreach($rawResults3 as $row3) {
            $list['resFirst'][$row3->tarih] = $row3;
        }
        
        return $list;
    }

    public function getDailyPlans($date) {
        $data = [[], [], []]; // Sabah, Öğle, Akşam
        $nakiller = [];

        for ($i = 0; $i < 3; $i++) {
            $sql = "SELECT p.*, h.id AS hastaid, h.isim, h.soyisim, h.tckimlik, il.adi AS ilce, m.adi AS mahalle, i.islemadi as islem_label
                    FROM esh_pizlemler AS p
                    LEFT JOIN esh_hastalar AS h ON p.hastatckimlik = h.tckimlik
                    LEFT JOIN esh_islemler AS i ON p.yapilacak = i.id
                    LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce
                    LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle
                    WHERE p.planlanantarih = " . $this->db->quote($date) . " 
                    AND p.zaman = $i AND p.yapilacak != 23
                    ORDER BY h.mahalle ASC";
            $data[$i]['planli'] = $this->db->setQuery($sql)->loadObjectList();
            
            $sql = "SELECT h.id AS hastaid, h.tckimlik, h.isim, h.soyisim, il.adi AS ilce, m.adi AS mahalle, 'İlk Kayıt' as islem_label 
                    FROM esh_hastalar AS h
                    LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce
                    LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle
                    WHERE h.zaman = $i AND h.pasif='-3' AND h.randevutarihi = " . $this->db->quote($date) . "
                    ORDER BY h.mahalle ASC";
            $data[$i]['ilkziyaret'] = $this->db->setQuery($sql)->loadObjectList();
            
        }

        $sqlNakil = "SELECT p.*, h.id AS hastaid, h.isim, h.soyisim, h.tckimlik, il.adi AS ilce, m.adi AS mahalle, 'Nakil' as islem_label
                     FROM esh_pizlemler AS p
                     LEFT JOIN esh_hastalar AS h ON p.hastatckimlik = h.tckimlik
                     LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce
                     LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle
                     WHERE p.planlanantarih = " . $this->db->quote($date) . " AND p.yapilacak = 23";
        $nakiller = $this->db->setQuery($sqlNakil)->loadObjectList();

        return [
            'sabah' => $data[0],
            'ogle' => $data[1],
            'aksam' => $data[2],
            'nakiller' => $nakiller
        ];
    }
    
    public function calculateSmartRoute($date) {
    // 1. AYARLAR & KATSAYILAR (Config'den çekilebilir)
    $merkez = ['lat' => 37.783291, 'lng' => 29.079663];
    $is_yuku_cezasi = 10; // Her eklenen hasta için maliyet artışı
    $mahalle_bonusu = 40; // Aynı mahalledeki hastalar için öncelik
    
    // 2. VERİLERİ ÇEK (Pansuman, İlk Ziyaret ve İzlemler)
    // Not: Bu kısımdaki SQL'leri senin getDailyPlans metodundaki JOIN'ler ile birleştirebilirsin
    $hastalar = $this->getRawRouteData($date); // Tüm aktif işleri getiren yardımcı metod
    
    // 3. EKİPLERİ BELİRLE (Veritabanından veya varsayılan)
    $ekipler = [
        0 => ['isim' => 'Sabah Ekibi 1', 'baslangic' => '09:00', 'hastalar' => []],
        1 => ['isim' => 'Öğle Ekibi 1', 'baslangic' => '13:00', 'hastalar' => []]
    ];

    foreach ($hastalar as $zk => $vardiyaHastalar) {
        if (!isset($ekipler[$zk])) continue;
        
        $kalanlar = $vardiyaHastalar;
        $ekipKonum = ['lat' => $merkez['lat'], 'lng' => $merkez['lng']];
        $ekipSaat = strtotime($date . ' ' . $ekipler[$zk]['baslangic']);

        while (count($kalanlar) > 0) {
            $best_idx = -1;
            $min_maliyet = 999999;
            
            // TOMTOM MATRIX VERİSİ (Senin getTomTomMatrixData fonksiyonun)
            $matrix = $this->getTomTomMatrixData($ekipKonum['lat'], $ekipKonum['lng'], $kalanlar);

            for ($k = 0; $k < count($kalanlar); $k++) {
                $h = $kalanlar[$k];
                $s_sure = $matrix[$k]['travelTimeInSeconds'] / 60;
                
                // --- SÜPER FORMÜL BURADA ---
                $maliyet = ($s_sure + (count($ekipler[$zk]['hastalar']) * $is_yuku_cezasi)) 
                           - (($h->oncelik == 3 ? 75 : 0) + ($h->mahalle == $last_mahalle ? 40 : 0));

                if ($maliyet < $min_maliyet) {
                    $min_maliyet = $maliyet;
                    $best_idx = $k;
                }
            }

            if ($best_idx != -1) {
                $secilen = $kalanlar[$best_idx];
                $secilen->varis_saati = date('H:i', $ekipSaat + ($matrix[$best_idx]['travelTimeInSeconds']));
                
                // Değerleri Güncelle
                $ekipSaat += ($matrix[$best_idx]['travelTimeInSeconds'] + ($secilen->sure * 60));
                $coords = explode(',', $secilen->coords);
                $ekipKonum = ['lat' => $coords[0], 'lng' => $coords[1]];
                $last_mahalle = $secilen->mahalle;

                $ekipler[$zk]['hastalar'][] = $secilen;
                array_splice($kalanlar, $best_idx, 1);
            }
        }
    }
    return $ekipler;
}

    public function getTomTomMatrixData($startLat, $startLon, $hastalar, $apiKey) {
    $results = array();
    
    // Matrix yetki hatasını aşmak için standart Routing API'yi tek tek çağırıyoruz
    for($k = 0; $k < count($hastalar); $k++) {
        $h = $hastalar[$k];
        $cp = explode(',', $h->coords);
        $destLat = trim($cp[0]);
        $destLon = trim($cp[1]);
        
        // Standart Routing API URL'si (Senin anahtarındaki "Routing API" yetkisini kullanır)
        $url = "http://api.tomtom.com/routing/1/calculateRoute/" . $startLat . "," . $startLon . ":" . $destLat . "," . $destLon . "/json?key=" . $apiKey . "&travelMode=car&traffic=true";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        // SSL Protokol hatalarını aşmak için (Önceki adımda çözmüştük)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if(defined('CURL_SSLVERSION_TLSv1_2')) {
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        } else {
            curl_setopt($ch, CURLOPT_SSLVERSION, 6); 
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code == 200) {
            $data = php4_json_decode($response);
            if (isset($data['routes'][0]['summary'])) {
                // Algoritmanın beklediği formatta veriyi diziye ekle
                $results[$k] = $data['routes'][0]['summary'];
            } else {
                $results[$k] = array('travelTimeInSeconds' => 999999, 'lengthInMeters' => 999999);
            }
        } else {
            // API hatası (Kota vb.) durumunda en uzak nokta kabul et
            $results[$k] = array('travelTimeInSeconds' => 999999, 'lengthInMeters' => 999999);
        }
        
        // Freemium saniye limitine (QPS) takılmamak için minik bir duraklama
        usleep(150000); // 0.15 saniye
    }
    return $results;
}

    public function getRawRouteData($date) {
    $ts = strtotime($date);
    $today_day = date('w', $ts); // Haftanın günü (0: Pazar, 6: Cumartesi)
    $start_time = $date . ' 00:00:00';
    $end_time = $date . ' 23:59:59';
    
    $ham_veri = [];

    // ORTAK SQL ALANLARI VE JOINLER
    $q_common = "h.id, h.isim, h.soyisim, h.tckimlik, h.coords, h.mahalle as mahalle_id, m.adi as mahalle_adi";
    $q_joins  = "LEFT JOIN esh_adrestablosu AS m ON h.mahalle = m.id ";

    // 1. PANSUMANLAR (pgunleri içinde bugünün günü olanlar)
    $sqlPansuman = "SELECT $q_common, h.zaman as zaman_kodu 
                    FROM esh_hastalar AS h $q_joins 
                    WHERE h.pansuman = 1 
                    AND FIND_IN_SET('$today_day', h.pgunleri) > 0 
                    AND h.pasif = 0 AND h.coords != ''";
    $rows = $this->db->setQuery($sqlPansuman)->loadObjectList();
    if($rows) {
        foreach($rows as $r) {
            $r->etiket = 'Pansuman';
            $r->oncelik = 1; // Standart öncelik
            $r->sure = 15;   // Dakika
            $ham_veri[] = $r;
        }
    }

    // 2. İLK MUAYENELER (pasif = -3 ve randevutarihi bugün olanlar)
    $sqlİlk = "SELECT $q_common, h.zaman as zaman_kodu 
               FROM esh_hastalar AS h $q_joins 
               WHERE h.pasif = '-3' 
               AND h.randevutarihi = " . $this->db->quote($date) . " 
               AND h.coords != ''";
    $rows = $this->db->setQuery($sqlİlk)->loadObjectList();
    if($rows) {
        foreach($rows as $r) {
            $r->etiket = 'İlk Ziyaret';
            $r->oncelik = 2; // Yüksek öncelik
            $r->sure = 45; 
            $ham_veri[] = $r;
        }
    }

    // 3. PLANLI İZLEMLER (pizlemler tablosu)
    $sqlIzlem = "SELECT $q_common, p.zaman as zaman_kodu, p.oncelik, i.islemadi as islem_detaylari
                 FROM esh_pizlemler AS p 
                 LEFT JOIN esh_hastalar AS h ON p.hastatckimlik = h.tckimlik 
                 LEFT JOIN esh_islemler AS i ON p.yapilacak = i.id
                 $q_joins 
                 WHERE p.planlanantarih = " . $this->db->quote($date) . " 
                 AND h.pasif = 0 AND h.coords != ''";
    $rows = $this->db->setQuery($sqlIzlem)->loadObjectList();
    if($rows) {
        foreach($rows as $r) {
            $r->etiket = $r->islem_detaylari ?? 'İzlem';
            $r->oncelik = (int)$r->oncelik;
            $r->sure = 25; 
            $ham_veri[] = $r;
        }
    }

    // VERİYİ ZAMAN KODUNA GÖRE GRUPLA (0: Sabah, 1: Öğle, 2: Akşam)
    $zamanli_veri = [0 => [], 1 => [], 2 => []];
    foreach($ham_veri as $item) {
        $zk = (int)$item->zaman_kodu;
        if(isset($zamanli_veri[$zk])) {
            $zamanli_veri[$zk][] = $item;
        }
    }

    return $zamanli_veri;
}

    private function formatDateTurkish($date) {
    $time = strtotime($date);
    $months = ['','Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'];
    $days = ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'];
    return date('j', $time).' '.$months[(int)date('m', $time)].' '.date('Y', $time).', '.$days[date('w', $time)];
}
    /**
     * Takvim için tüm planları getirir
     */
    public function getAllForCalendar() {
        $query = "SELECT p.id, 
                         CONCAT(h.isim, ' ', h.soyisim) as title, 
                         p.planlanantarih as start,
                         p.oncelik
                  FROM esh_pizlemler p 
                  JOIN esh_hastalar h ON p.hastatckimlik = h.tckimlik";
        
        return $this->db->setQuery($query)->loadObjectList();
    }

    /**
     * Belirli bir tarihteki veya gelecekteki izlemleri getirir
     */
    public function getUpcoming($limit = 10) {
        $query = "SELECT p.*, h.isim, h.soyisim 
                  FROM esh_pizlemler p 
                  JOIN esh_hastalar h ON p.hastatckimlik = h.tckimlik 
                  WHERE p.planlanantarih >= CURDATE()
                  ORDER BY p.planlanantarih ASC";
        
        return $this->db->setQuery($query, 0, $limit)->loadObjectList();
    }

    /**
     * Hastanın tüm izlem geçmişini getirir
     */
    public function getHistoryByPatientTc($tc, $limit = 10, $offset = 0) {
        $query = "SELECT p.*, u.username as yapan_personel 
                  FROM esh_pizlemler p 
                  LEFT JOIN esh_users u ON p.planiyapan = u.id 
                  WHERE p.hastatckimlik = " . $this->db->quote($tc) . " 
                  ORDER BY p.planlanantarih DESC";
                  
        return $this->db->setQuery($query, $offset, $limit)->loadObjectList();
    }

    /**
     * İzlemi Başka Bir Tarihe Güncelle (Erteleme/Öne Çekme)
     */
    public function reschedule($newDate) {
        $this->planlanantarih = $newDate;
        return $this->store();
    }

    /**
     * Kaydı Sil (Artık 'yapildimi' olmadığı için iptal edilen kayıtlar silinebilir)
     */
    public function remove() {
        if ($this->id) {
            $query = "DELETE FROM esh_pizlemler WHERE id = " . (int)$this->id;
            return $this->db->setQuery($query)->query();
        }
        return false;
    }
    
    // ÖNERİ: Gecikmiş planları getir (Bugünden önce ve henüz gerçekleşmemiş)
    public function getOverdueVisits() {
        $sql = "SELECT p.*, h.isim, h.soyisim FROM esh_pizlemler p 
                JOIN esh_hastalar h ON p.hastatckimlik = h.tckimlik
                WHERE p.planlanantarih < CURDATE() AND p.id NOT IN (SELECT id FROM esh_izlemler)
                ORDER BY p.planlanantarih ASC";
        return $this->db->setQuery($sql)->loadObjectList();
    }
}