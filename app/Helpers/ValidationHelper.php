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
        $d11 = array_sum(array_slice($digits, 0, 10)) % 10;

        if ($digits[9] != $d10 || $digits[10] != $d11) return false;

        return true;
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
     * @param array $requiredFields ['isim', 'soyisim', 'tckimlik'] gibi
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
     * E-posta Geçerlilik Kontrolü
     */
    public static function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
/*
Küçük Bir İpucu: ValidationHelper ile bulduğun hataları implode("<br>", $errors) yaparak 
$_SESSION['error'] içine atarsan, Toastr tüm hataları alt alta şık bir şekilde listeler.