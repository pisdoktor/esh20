<?php
// --- TEST EDELİM ---

// --- DENEYELİM ---

$tc = "40090105582";
$kodlanmis = tcSifrele($tc);

echo "URL'de görünecek hali: ?tc=" . $kodlanmis . "<br>";
echo "Sistemde çözülen hali: " . tcCoz($kodlanmis);


function tcSifrele($tcNo) {
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
function tcCoz($kod) {
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
?>