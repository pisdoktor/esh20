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
    
    /**
     * Planlanmış izlemleri filtreli ve sayfalı olarak getirir
     */
    public function getAllPlanned($limit = 20, $offset = 0, $search = '', $status = '', $ordering = 'p.planlanan_tarih ASC') {
        $where = [];
        
        // Arama (TC veya İsim)
        if (!empty($search)) {
            $searchStr = $this->db->quote('%' . $search . '%');
            $where[] = "(p.hastatckimlik LIKE $searchStr OR h.isim LIKE $searchStr OR h.soyisim LIKE $searchStr)";
        }

        // Durum Filtresi (0: Bekliyor, 1: Tamamlandı/İşleme Alındı)
        if ($status !== '') {
            $where[] = "p.durum = " . (int)$status;
        }

        $whereSql = count($where) ? " WHERE " . implode(" AND ", $where) : "";

        $query = "SELECT p.*, h.isim, h.soyisim, h.cinsiyet, h.telefon,
                         il.adi AS ilce, m.adi AS mahalle
                  FROM {$this->_tbl} AS p
                  LEFT JOIN esh_hastalar AS h ON h.tckimlik = p.hastatckimlik
                  LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce
                  LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle
                  $whereSql
                  ORDER BY $ordering
                  LIMIT $limit OFFSET $offset";
        
        return $this->db->setQuery($query)->loadObjectList();
    }

    public function countAllPlanned($search = '', $status = '') {
        $where = [];
        if (!empty($search)) {
            $searchStr = $this->db->quote('%' . $search . '%');
            $where[] = "(p.hastatckimlik LIKE $searchStr OR h.isim LIKE $searchStr OR h.soyisim LIKE $searchStr)";
        }
        if ($status !== '') {
            $where[] = "p.durum = " . (int)$status;
        }

        $whereSql = count($where) ? " WHERE " . implode(" AND ", $where) : "";

        $query = "SELECT COUNT(p.id) FROM {$this->_tbl} AS p
                  LEFT JOIN esh_hastalar AS h ON h.tckimlik = p.hastatckimlik
                  $whereSql";
        return $this->db->setQuery($query)->loadResult();
    }

    //Aylık planlı getir
    //Aylık planlı getir (Pansumanlar Dahil Edildi)
    public function getMonthPlans($year, $month) {
    
        $startDate = sprintf('%04d-%02d-01', (int)$year, (int)$month);
        $endDate = date("Y-m-t", strtotime($startDate));
        
        $list = ['resProc' => [], 'resDone' => [], 'resFirst' => [], 'resPansuman' => []];

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

        // 4. PLANLI PANSUMANLAR (Haftalık periyoda göre hesaplama)
        // Aktif olan ve pansuman günleri tanımlanmış hastaları çekiyoruz
        $sqlPansuman = "SELECT pgunleri FROM esh_hastalar WHERE pansuman = 1 AND pasif = 0 AND pgunleri != ''";
        $pansumanHastalari = $this->db->setQuery($sqlPansuman)->loadObjectList();

        if ($pansumanHastalari) {
            $period = new \DatePeriod(
                new \DateTime($startDate),
                new \DateInterval('P1D'),
                (new \DateTime($endDate))->modify('+1 day')
            );

            foreach ($period as $date) {
                $tarihKey = $date->format("Y-m-d");
                $gunIndex = $date->format("w"); // 0: Pazar, 6: Cumartesi

                $gunlukToplam = 0;
                foreach ($pansumanHastalari as $hasta) {
                    $gunler = explode(',', $hasta->pgunleri);
                    if (in_array($gunIndex, $gunler)) {
                        $gunlukToplam++;
                    }
                }

                if ($gunlukToplam > 0) {
                    $list['resPansuman'][$tarihKey] = (object)['tarih' => $tarihKey, 'total' => $gunlukToplam];
                }
            }
        }
        
        return $list;
    }
    //Günlük planlı getir
    public function getDailyPlans($date) {
        $data = [[], [], []]; // Sabah, Öğle, Akşam
        $nakiller = [];
        
        $dayOfWeek = date('w', strtotime($date));

        for ($i = 0; $i < 3; $i++) {
            // Planlanmış izlemler
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
            //Planlanmış ilk ziyaretler
            $sql = "SELECT h.id AS hastaid, h.tckimlik, h.isim, h.soyisim, il.adi AS ilce, m.adi AS mahalle, 'İlk Kayıt' as islem_label 
                    FROM esh_hastalar AS h
                    LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce
                    LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle
                    WHERE h.zaman = $i AND h.pasif='-3' AND h.randevutarihi = " . $this->db->quote($date) . "
                    ORDER BY h.mahalle ASC";
            $data[$i]['ilkziyaret'] = $this->db->setQuery($sql)->loadObjectList();
            
            // 3. EKLENEN: Periyodik Pansumanlar
            // pgunleri içinde bugünün günü var mı ve pansuman aktif mi kontrolü
            $sqlPansuman = "SELECT h.id AS hastaid, h.tckimlik, h.isim, h.soyisim, il.adi AS ilce, m.adi AS mahalle, 'Pansuman' as islem_label 
                            FROM esh_hastalar AS h
                            LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce
                            LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle
                            WHERE h.pzaman = $i 
                            AND h.pansuman = 1 
                            AND h.pasif = 0 
                            AND FIND_IN_SET('$dayOfWeek', h.pgunleri) > 0
                            ORDER BY h.mahalle ASC";
            $data[$i]['pansuman'] = $this->db->setQuery($sqlPansuman)->loadObjectList();
            
        }
        //Planlanmış nakiller
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

    public function calculateSmartRoute($date) {
    // 1. AYARLAR & KATSAYILAR (Config'den çekilebilir)
    $merkez = ['lat' => START_LAT, 'lng' => START_LNG];
    $is_yuku_cezasi = 10; // Her eklenen hasta için maliyet artışı
    $mahalle_bonusu = 40; // Aynı mahalledeki hastalar için öncelik
    
    // 2. VERİLERİ ÇEK (Pansuman, İlk Ziyaret ve İzlemler)
    // Not: Bu kısımdaki SQL'leri senin getDailyPlans metodundaki JOIN'ler ile birleştirebilirsin
    $hastalar = $this->getRawRouteData($date); // Tüm aktif işleri getiren yardımcı metod
    
    // 3. EKİPLERİ BELİRLE (Veritabanından veya varsayılan)
    $ekipler = [
        0 => ['isim' => 'Sabah Ekibi 1', 'baslangic' => '09:00', 'hastalar' => []],
        1 => ['isim' => 'Öğle Ekibi 1', 'baslangic' => '13:00', 'hastalar' => []],
        2 => ['isim' => 'Akşam Ekibi 1', 'baslangic' => '16:00', 'hastalar' => []]
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
            $matrix = $this->getTomTomMatrixData($ekipKonum['lat'], $ekipKonum['lng'], $kalanlar, TOMTOM_KEY);
            
            $last_mahalle = '';
            
            for ($k = 0; $k < count($kalanlar); $k++) {
                $h = $kalanlar[$k];
                $s_sure = $matrix[$k]->travelTimeInSeconds / 60;
                
                // --- SÜPER FORMÜL BURADA ---
                $maliyet = ($s_sure + (count($ekipler[$zk]['hastalar']) * $is_yuku_cezasi)) 
                           - (($h->oncelik == 3 ? 75 : 0) + ($h->mahalle_id == $last_mahalle ? 40 : 0));

                if ($maliyet < $min_maliyet) {
                    $min_maliyet = $maliyet;
                    $best_idx = $k;
                }
            }

            if ($best_idx != -1) {
                $secilen = $kalanlar[$best_idx];
                $secilen->varis_saati = date('H:i', $ekipSaat + ($matrix[$best_idx]->travelTimeInSeconds));
                
                // Değerleri Güncelle
                $ekipSaat += ($matrix[$best_idx]->travelTimeInSeconds + ($secilen->sure * 60));
                $coords = explode(',', $secilen->coords);
                $ekipKonum = ['lat' => $coords[0], 'lng' => $coords[1]];
                $last_mahalle = $secilen->mahalle_id;

                $ekipler[$zk]['hastalar'][] = $secilen;
                array_splice($kalanlar, $best_idx, 1);
            }
        }
    } 
    
    return $ekipler;
}

    public function getTomTomMatrixData($startLat, $startLon, $hastalar, $apiKey) {
    set_time_limit(0);
    $results = array();
    $originStr = $startLat . "," . $startLon;

    for($k = 0; $k < count($hastalar); $k++) {
        $h = $hastalar[$k];
        $destStr = trim($h->coords);
        $hash = md5($originStr . $destStr);
        
        // 1. Önce Cache tablosuna bakıyoruz
        $cache = $this->db->setQuery("SELECT sure, mesafe FROM esh_rota_cache WHERE hash = ".$this->db->quote($hash))->loadObject();

        if ($cache) {
            // Varsa cache'ten al
            $results[$k] = (object) [
                'travelTimeInSeconds' => (int)$cache->sure, 
                'lengthInMeters' => (int)$cache->mesafe
            ];
        } else {
            // 2. Yoksa SENİN ORİJİNAL cURL YAPINLA API'ye git
            $url = "https://api.tomtom.com/routing/1/calculateRoute/$originStr:$destStr/json?key=$apiKey&travelMode=car&traffic=true";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            // TomTom bazen User-Agent bekler, onu da ekleyelim ki 403 vermesin
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'); 
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode == 200 && $response) {
                $data = json_decode($response);
                if (isset($data->routes[0]->summary)) {
                    $summary = $data->routes[0]->summary;
                    
                    // 3. Gelecek sefer için Cache'e Kaydet
                    $this->db->setQuery("INSERT IGNORE INTO esh_rota_cache (hash, origin, destination, sure, mesafe) VALUES (".
                        $this->db->quote($hash).", ".$this->db->quote($originStr).", ".$this->db->quote($destStr).", ".
                        (int)$summary->travelTimeInSeconds.", ".(int)$summary->lengthInMeters.")")->query();
                        
                    $results[$k] = $summary;
                } else {
                    // Veri beklenen formatta değilse varsayılan
                    $results[$k] = (object) ['travelTimeInSeconds' => 600, 'lengthInMeters' => 2000];
                }
            } else {
                // HATA DURUMU (403, Timeout vb.): Sistemi çökertmemek için varsayılan bir değer dönüyoruz
                // Buradaki 600 saniye (10 dk) ve 2000 metre senin algoritmanın çalışmaya devam etmesini sağlar
                $results[$k] = (object) ['travelTimeInSeconds' => 600, 'lengthInMeters' => 2000];
            }
            
            // API'yi yormamak için çok kısa bir bekleme
            usleep(100000); 
        }
    }
    return $results;
}

    public function getRawRouteData($date) {
    // Veriyi vardiya (zaman_kodu) bazlı gruplayarak topluyoruz
    $ham_veri = [
        0 => [], // Sabah
        1 => [], // Öğle
        2 => []  // Akşam
    ];
    
    $dayOfWeek = date('w', strtotime($date));
    $q_common = "h.id AS hastaid, h.tckimlik, h.isim, h.soyisim, h.coords, il.adi AS ilce, m.adi AS mahalle, h.mahalle as mahalle_id";
    $q_joins = "LEFT JOIN esh_adrestablosu AS il ON il.id = h.ilce LEFT JOIN esh_adrestablosu AS m ON m.id = h.mahalle";

    // 1. İLK ZİYARETLER
    $sql1 = "SELECT $q_common, h.zaman as zaman_kodu, 2 as oncelik, 'İlk Ziyaret' as islem_detaylari 
             FROM esh_hastalar h $q_joins 
             WHERE h.randevutarihi = '$date' AND h.pasif = -3 AND h.coords != ''";
    $res1 = $this->db->setQuery($sql1)->loadObjectList() ?: [];
    foreach($res1 as $r) { 
        $r->etiket = 'İlk Ziyaret'; 
        $r->sure = 45; 
        $ham_veri[(int)$r->zaman_kodu][] = $r; 
    }

    // 2. PLANLI İZLEMLER
    $sql2 = "SELECT $q_common, p.zaman as zaman_kodu, p.oncelik, i.islemadi as islem_detaylari 
             FROM esh_pizlemler p 
             LEFT JOIN esh_hastalar h ON p.hastatckimlik = h.tckimlik 
             LEFT JOIN esh_islemler i ON p.yapilacak = i.id 
             $q_joins 
             WHERE p.planlanantarih = '$date' AND h.pasif = 0 AND h.coords != ''";
    $res2 = $this->db->setQuery($sql2)->loadObjectList() ?: [];
    foreach($res2 as $r) { 
        $r->etiket = $r->islem_detaylari; 
        $r->sure = 30; 
        $ham_veri[(int)$r->zaman_kodu][] = $r; 
    }

    // 3. PANSUMANLAR
    $sql3 = "SELECT $q_common, h.zaman as zaman_kodu, 1 as oncelik, 'Pansuman' as islem_detaylari 
             FROM esh_hastalar h $q_joins 
             WHERE h.pansuman = 1 AND h.pasif = 0 AND h.coords != '' 
             AND FIND_IN_SET('$dayOfWeek', h.pgunleri) > 0";
    $res3 = $this->db->setQuery($sql3)->loadObjectList() ?: [];
    foreach($res3 as $r) { 
        $r->etiket = 'Pansuman'; 
        $r->sure = 20; 
        $ham_veri[(int)$r->zaman_kodu][] = $r; 
    }

    return $ham_veri;
}
}