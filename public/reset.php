<?php
// Hata raporlamayı açalım ki ne olduğunu görelim
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';  
require_once '../app/Core/Database.php';
require_once '../app/Models/BaseModel.php';
require_once '../app/Models/User.php';

use App\Models\User;

$userModel = new User();

// 1 numaralı kullanıcıyı (Admin) yükle
if ($userModel->load(1)) {
    $yeniSifre = "123";
    
    // PHP'nin kendi fonksiyonuyla güvenli hash oluştur
    $userModel->password = password_hash($yeniSifre, PASSWORD_DEFAULT);
    
    // HATALARI ÇÖZEN KISIM: Zorunlu alanları boş bırakma
    $simdi = date('Y-m-d H:i:s');
    $userModel->nowvisit = $simdi;
    $userModel->lastvisit = $simdi;
    $userModel->registerDate = $simdi;
    
    // Null gitmemesi gereken alanlara varsayılan değerler
    if (is_null($userModel->image)) $userModel->image = '';
    if (is_null($userModel->activation)) $userModel->activation = '';
    if (is_null($userModel->tckimlikno)) $userModel->tckimlikno = '00000000000';
    if (is_null($userModel->email)) $userModel->email = 'admin@localhost';

    // Veritabanına kaydet
    if ($userModel->store()) {
        echo "<h3>İşlem Başarılı!</h3>";
        echo "Admin şifresi '123' yapıldı ve tarih hataları düzeltildi.<br>";
        echo "Yeni Hash: " . $userModel->password;
    } else {
        echo "Şifre güncellenirken bir hata oluştu.";
    }
} else {
    echo "ID'si 1 olan kullanıcı bulunamadı!";
}