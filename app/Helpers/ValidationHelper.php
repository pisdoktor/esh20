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

}