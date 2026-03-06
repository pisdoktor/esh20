<?php
namespace App\Helpers;

class ValidationHelper {
    /**
     * T.C. Kimlik Numarası Algoritma Kontrolü
     */
    public static function isTc($tc) {
        $tc = preg_replace("/[^0-9]/", "", $tc);
        if (strlen($tc) != 11) return false;
        if ($tc[0] == 0) return false;

        $digits = str_split($tc);
        $d10 = (($digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8]) * 7 - 
               ($digits[1] + $digits[3] + $digits[5] + $digits[7])) % 10;
        
        // Mod negatif çıkarsa 10 ekle (Bazı PHP sürümleri için önlem)
        if ($d10 < 0) $d10 += 10;
        
        $d11 = array_sum(array_slice($digits, 0, 10)) % 10;

        if ($digits[9] != $d10 || $digits[10] != $d11) return false;

        return true;
    }

    /**
     * T.C. Kimlik numarasını 123 45 67 89 01 formatına çevirir
     */
    public static function formatTc($tc) {
        $tc = preg_replace("/[^0-9]/", "", $tc); // Sadece rakamlar kalsın
        if (strlen($tc) != 11) return $tc; 
        
        return substr($tc, 0, 3) . ' ' . 
               substr($tc, 3, 2) . ' ' . 
               substr($tc, 5, 2) . ' ' . 
               substr($tc, 7, 2) . ' ' . 
               substr($tc, 9, 2);
    }

    /**
     * Telefon Numarası Format Kontrolü (05xx xxx xx xx)
     */
    public static function isPhone($phone) {
        $phone = preg_replace("/[^0-9]/", "", $phone);
        // Türkiye için 10 veya 11 hane kontrolü
        return (strlen($phone) == 10 || strlen($phone) == 11);
    }

    /**
     * Zorunlu Alan Kontrolü
     * @param array $data Formdan gelen $_POST
     * @param array $requiredFields ['isim', 'soyisim'] gibi
     */
    public static function checkRequired($data, $requiredFields) {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = $field . " alanı boş bırakılamaz.";
            }
        }
        return $errors;
    }
    
    /**
     * Denizli Büyükşehir Belediyesi Mezarlık Sistemi üzerinden ölüm kontrolü yapar
     * @param object $patient Hasta nesnesi (id, isim, soyisim, anneAdi, babaAdi içeren)
     * @return string|bool Ölüm tarihi (d.m.Y) veya bulunamadıysa false
     */
    public static function checkDeathNotification($patient) {
        // 1 Ay önceki timestamp (Karşılaştırma için)
        $limitTimestamp = strtotime('-1 month');
        $patient->isim = mb_strtoupper(trim($patient->isim), 'UTF-8');
        $patient->soyisim = mb_strtoupper(trim($patient->soyisim), 'UTF-8');
        $patient->anneAdi = mb_strtoupper(trim($patient->anneAdi), 'UTF-8');
        $patient->babaAdi = mb_strtoupper(trim($patient->babaAdi), 'UTF-8');  

        // Belediye Sorgu Linki (UTF-8 karakter sorunlarını önlemek için urlencode kullanıyoruz)
        $link = "http://mezarlik.denizli.bel.tr/sorgu.ashx?islem=definListesiGetir";
        $link .= "&ad=" . urlencode(trim($patient->isim));
        $link .= "&soyad=" . urlencode(trim($patient->soyisim));
        $link .= "&anneAd=" . urlencode(trim($patient->anneAdi));
        $link .= "&babaAd=" . urlencode(trim($patient->babaAdi));

        try {
            // Veriyi çek (file_get_contents bazen DOMDocument'ten daha hızlıdır)
            $jsonRaw = @file_get_contents($link);
            
            if (!$jsonRaw || strlen($jsonRaw) < 10) return false;

            // JSON verisini parse et (Diziye çevir)
            $data = json_decode($jsonRaw, true);

            // Gelen veri bir dizi mi ve içinde kayıt var mı? (Belediye genelde dizi içinde nesne döner)
            if (is_array($data) && isset($data[0]['olumTarihi'])) {
                
                $kisi = $data[0]; // İlk eşleşen kaydı al
                $olumTarihiRaw = $kisi['olumTarihi']; // Örn: 2023-10-25T00:00:00
                $olumTimestamp = strtotime($olumTarihiRaw);

                // Eğer ölüm tarihi son 1 ay içindeyse tarihi döndür
                if ($olumTimestamp > $limitTimestamp) {
                    return date('d.m.Y', $olumTimestamp);
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda (bağlantı vb.) sessiz kal veya logla
            return false;
        }

        return false;
    }
    
/**
 * TC Kimlik numarasını URL'de gizlemek için "anlamsız" bir hale getirir.
 */
function tc_encode($tc, $anahtar) {
    // 1. Adım: Her rakamı basit bir anahtarla kaydır (Örn: +3 ekle)
    // Bu sayede 1 -> 4, 9 -> 2 (mod 10) gibi değişir.
    $anahtar = [3, 7, 1, 9, 4, 2, 8, 5, 0, 6, 3]; // 11 haneli gizli dizin
    $yeniTc = "";
    
    for ($i = 0; $i < 11; $i++) {
        $rakam = (int)$tcNo[$i];
        $yeniRakam = ($rakam + $anahtar[$i]) % 10;
        $yeniTc .= $yeniRakam;
    }

    // 2. Adım: Başına ve sonuna rastgele karakterler ekleyip hex yapalım
    // Böylece kimse bunun bir TC olduğunu bile anlamaz.
    return bin2hex("xyz" . $yeniTc . "abc");
}

/**
 * URL'deki kodu tekrar orijinal TC'ye dönüştürür.
 */
function tc_decode($tc, $anahtar) {
    // 1. Adım: Hex'ten metne geri çevir
    $data = hex2bin($kod);
    
    // 2. Adım: Eklediğimiz "xyz" ve "abc" kısımlarını temizle
    $yeniTc = substr($data, 3, 11);
    
    // 3. Adım: Kaydırmayı tersine çevir (Çıkarma işlemi)
    $anahtar = [3, 7, 1, 9, 4, 2, 8, 5, 0, 6, 3];
    $orijinalTc = "";
    
    for ($i = 0; $i < 11; $i++) {
        $rakam = (int)$yeniTc[$i];
        $eskiRakam = ($rakam - $anahtar[$i]);
        if ($eskiRakam < 0) $eskiRakam += 10;
        $orijinalTc .= $eskiRakam;
    }

    return $orijinalTc;
}
}