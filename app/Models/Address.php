<?php
namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Adres Tablosu Modeli
 * BaseModel'den miras alır, bind ve store yeteneklerine sahiptir.
 */
class Address extends BaseModel {
    
    // Veritabanı sütunları
    public $id = null;      // UUID veya ID
    public $adi = null;
    public $ust_id = null;  // Parent ID
    public $tip = null;     // ilce, mahalle, sokak, kapino

    public function __construct() {
        // esh_adrestablosu tablosu, anahtar sütunu 'id'
        parent::__construct('esh_adrestablosu', 'id');
    }

    /**
     * Tüm ilçeleri getirir (loadObjectList kullanımı)
     */
    public function getDistricts() {
        $query = "SELECT id, adi FROM {$this->_tbl} WHERE tip = 'ilce' ORDER BY adi ASC";
        return $this->db->setQuery($query)->loadObjectList();
    }

    /**
     * Alt birimleri getirir (Mahalle, Sokak vb.)
     */
    public function getSubs($parentId, $type) {
        $query = "SELECT id, adi FROM {$this->_tbl} 
              WHERE ust_id = " . $this->db->quote($parentId) . " 
              AND tip = " . $this->db->quote($type) . " 
              ORDER BY adi ASC";
    
    $result = $this->db->setQuery($query)->loadObjectList();

        // Eğer veritabanında yoksa dış servisten çek
        if (empty($result) && ($type == 'sokak' || $type == 'kapino')) {
            return $this->fetchFromExternalService($parentId, $type);
        }

        return $result;
    }

    /**
    * Denizli belediyesi sisteminden olmayan sokak, kapino getirir veritabanına işler
    */
    private function fetchFromExternalService($parentId, $type) {
    $t = ($type == 'sokak') ? 'sokak' : 'kapi';
    $url = "https://adres.denizli.bel.tr/veriHazirla.ashx?id=" . $parentId . "&t=" . $t;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $xml_verisi = curl_exec($ch);
    curl_close($ch);

    if (!$xml_verisi) return [];

    $parser = xml_parser_create();
    xml_parse_into_struct($parser, $xml_verisi, $degerler);
    xml_parser_free($parser);

    $items = [];
    $temp_id = ""; 
    $temp_adi = "";

    foreach ($degerler as $hucre) {
        $tag = strtoupper($hucre['tag']);
        $val = isset($hucre['value']) ? trim($hucre['value']) : '';

        // 1. ID Etiketini yakala
        if ($tag == 'ID') {
            $temp_id = $val;
        }
        
        // 2. ADI (Sokaklar için) veya NO (Kapı numaraları için) etiketini yakala
        // Paylaştığın XML'de kapı numarası <NO> etiketi içinde geliyor
        if ($tag == 'ADI' || $tag == 'NO' || $tag == 'KAPI_NO') {
            $temp_adi = $val;
        }

        // 3. Hem ID hem de ADI/NO verisi tamamlandığında listeye ekle
        if ($temp_id !== "" && $temp_adi !== "") {
            // Veritabanına kaydet (Önbellekleme)
            $this->saveToDb($temp_id, $temp_adi, $parentId, $type);
            
            $items[] = (object)[
                'id' => $temp_id,
                'adi' => $temp_adi
            ];
            
            // Sonraki satır için temizle
            $temp_id = ""; 
            $temp_adi = "";
        }
    }
    return $items;
}
    /**
    * Veritabanına kayıt fonksiyonu
    */
    public function saveToDb($id, $adi, $parentId, $type) {
    $this->reset(); // Nesneyi temizle
    $this->id = $id;
    $this->adi = $adi;
    $this->ust_id = $parentId;
    $this->tip = $type;
    
    // Database.php'deki insertObject PDO kullanır, daha güvenlidir
    return $this->db->insertObject($this->_tbl, $this); 
}
    
    public function getUserAddress($userid) {
        $sql = "SELECT i.adi AS ilce, m.adi as mahalle, s.adi as sokak, k.adi as kapino
                FROM esh_hastalar as h
                LEFT JOIN esh_adrestablosu AS i ON i.id=h.ilce
                LEFT JOIN esh_adrestablosu AS m ON m.id=h.mahalle
                LEFT JOIN esh_adrestablosu AS s ON s.id=h.sokak
                LEFT JOIN esh_adrestablosu AS k ON k.id=h.kapino
                WHERE h.id=$userid";
        return $this->db->setQuery($sql)->loadObject();
    }
    
    public function getAdresListeleri($patient) {
  
    $lists = array();

    // 1. İLÇE LİSTESİ
    $lists['ilce']    = $this->generateAdresSelect('ilce', null, $patient->ilce, 'İlçe', 'ilce', 1);

    // 2. MAHALLE LİSTESİ (İlçeye bağlı)
    $lists['mahalle'] = $this->generateAdresSelect('mahalle', $patient->ilce, $patient->mahalle, 'Mahalle', 'mahalle', 1);

    // 3. SOKAK LİSTESİ (Mahalleye bağlı)
    $lists['sokak']   = $this->generateAdresSelect('sokak', $patient->mahalle, $patient->sokak, 'Sokak', 'sokak', 1);

    // 4. KAPINO LİSTESİ (Sokağa bağlı)
    $lists['kapino']  = $this->generateAdresSelect('kapino', $patient->sokak, $patient->kapino, 'Kapı No', 'kapino');
    
    $lists['adres_aciklama'] = $this->generateAdresAciklama('adres_aciklama', $patient->adres_aciklama, 'adres_aciklama');

    return $lists;
}

    public function getUserOtherAddresses($adresler) {
    // Adres listesinin dizi ve dolu olup olmadığını kontrol ediyoruz
    if (is_array($adresler) && !empty($adresler)) {
        
        $adresbilgi = array();
        
        foreach ($adresler as $v) {
            $sql = "SELECT 
                        i.adi AS ilce, 
                        m.adi AS mahalle, 
                        s.adi AS sokak, 
                        k.adi AS kapino
                    FROM esh_adrestablosu AS i
                    LEFT JOIN esh_adrestablosu AS m ON m.id = '{$v['mahalle']}'
                    LEFT JOIN esh_adrestablosu AS s ON s.id = '{$v['sokak']}'
                    LEFT JOIN esh_adrestablosu AS k ON k.id = '{$v['kapino']}'
                    WHERE i.id = '{$v['ilce']}' 
                    LIMIT 1"; // Sadece ilgili satırı almak için limit ekledik
            
            $result['adres'] = $this->db->setQuery($sql)->loadObject();
            $result['adres_aciklama'] = $v['adres_aciklama'];
            if ($result) {
                $adresbilgi[] = $result;
            }
        }
        
        return $adresbilgi;
    }
    
    return array(); // Eğer adres yoksa boş dizi döndürmek daha güvenlidir
}
    /**
     * PHP4 için yardımcı SQL ve HTML oluşturucu fonksiyon
     */
    public function generateAdresSelect($tip, $ust_id, $current_val, $label, $element_id, $required=0) {    
        // SQL sorgusunu PHP4 standartlarında birleştiriyoruz 
        $query = "SELECT id, adi FROM esh_adrestablosu WHERE tip='" . $tip . "'";
        
        // Eğer bir üst kategori seçiliyse (veya ilçe gibi kök dizinse) sorguya ekle
        if ($ust_id !== null && $ust_id > 0) {
            $query .= " AND ust_id='" . $ust_id . "'";
        } elseif ($tip != 'ilce' && ($ust_id === null || $ust_id == 0)) {
            // Eğer ilçe değilse ve üst id yoksa boş liste döndürmek için imkansız bir şart
            $query .= " AND 1=2";
        }

        $query .= " ORDER BY adi ASC";

        $rows = $this->db->setQuery($query)->loadObjectList(); 
        
        $options[] = \App\Helpers\FormHelper::makeOption('', $label.' Seçin');
        foreach($rows as $row) {
         $options[] = \App\Helpers\FormHelper::makeOption($row->id, $row->adi);
        }
                                                               
        $requiredtext = $required ? 'required="required"': '';
        
        return \App\Helpers\FormHelper::selectList($options, $tip, $requiredtext, 'value', 'text', $current_val);
    }

    function generateAdresAciklama($name, $value, $element_id) {
        // HTML stringini PHP4 uyumlu birleştiriyoruz
        $html = '<textarea name="' . $name . '" id="' . $element_id . '" class="form-control" rows="2" placeholder="Adres detayı (Bina adı, Kat, Daire vb.)">' . $value . '</textarea>';
        return $html;
    }
}